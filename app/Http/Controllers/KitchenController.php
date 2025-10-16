<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\FoodItem;
use App\Models\Sale;
use App\Models\ZReport;
use App\Services\ItemKitchenInvoice;
use App\Services\OrderKitchenInvoice;
use App\Models\PrintSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function index()
    {
//        $user = auth()->user()->id;
//        $waiting = Sale::where(['chef_id' => null, 'prepared_at' => null, 'approve_cancel_kitchen' => false])->with('serviceTable', 'chef')->get()->toArray();
//        $completed = Sale::where(['chef_id' => $user, 'progress' => 100, 'prepared_at' => null, 'approve_cancel_kitchen' => false])->with('serviceTable', 'chef')->get()->toArray();
//        $inProgress = Sale::where(['chef_id' => $user, 'prepared_at' => null, 'approve_cancel_kitchen' => false])->where('progress', '!=', 100)->with('serviceTable', 'chef')->get()->toArray();
//
//        $order_list = array_merge($completed, $inProgress, $waiting);

        $order_list = [];
        if ($this->isAdmin()) {
            $currentLocation = session()->get('localization_for_changes_data');
            if (!$currentLocation) {
                return view('pop-up-locations');
            } elseif (!$currentLocation->kitchen) {
                return redirect()->route('dashboard')->withErrors(['kitchen_error' => $currentLocation->name . ' ' . __("location has not allowed kitchen in the system, if you want to continue please enable it in the system")]);
            }
        } elseif(!auth()->user()->location?->kitchen) {
            return view('kitchen/alert-disabled-kitchen')->withErrors(['kitchen_error' => auth()->user()->location?->name . ' ' . __("location has not allowed kitchen in the system, if you want to continue please enable it in the system")]);
        }
        return view('kitchen/kitchen', compact('order_list'));
    }

    private function getAutoPrintOrders()
    {
        $ordersToPrint = Sale::where('z_report_id', '!=', null)
            ->where(['prepared_at' => null, 'approve_cancel_kitchen' => false, 'is_cancelled' => false, 'is_auto_printed' => false, 'save_order' => false])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        $ready = Sale::where('prepared_at', '!=', null)
            ->where('z_report_id', '!=', null)
            ->where(['completed_at' => null, 'is_cancelled' => false, 'save_order' => false, 'is_auto_printed' => false])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        $mergedSales = $ordersToPrint->merge($ready);
        $canceledOrders = [];
        foreach ($mergedSales as $key => $order) {
            $canceledOrders[$key]['id'] = $order->id;
            $inject = new \App\Services\SaleInvoice($order);
            $printData = $inject->getDataForPrinter();
            $printString = $inject->prepareString($printData);
            $canceledOrders[$key]['printString'] = $printString;
        }
        return $canceledOrders ?? [];
    }

    public function orderKitchen(Request $request){
        $isAutoPrinted = $this->currentLocation()['auto_print'];
        if ($isAutoPrinted){
            $orderIds = $request->query->all() ?? [];
            Sale::whereIn('id', $orderIds)->update(['is_auto_printed' => true]);
        }
//        $openZReport = ZReport::where(['end_z_report' => null, 'cash_register_id' => session()->get('cash_register')])->first();

//        $waiting = [];
//        $completed = [];
//        $inProgress = [];

//        if ($openZReport) {
//            $waiting = $openZReport->orders()->where(['chef_id' => null, 'prepared_at' => null, 'approve_cancel_kitchen' => false])->with('serviceTable', 'chef')->get()->toArray();
//            $completed = $openZReport->orders()->where(['chef_id' => $user, 'progress' => 100, 'prepared_at' => null, 'approve_cancel_kitchen' => false])->with('serviceTable', 'chef')->get()->toArray();
//            $inProgress = $openZReport->orders()->where(['chef_id' => $user, 'prepared_at' => null, 'approve_cancel_kitchen' => false])->where('progress', '!=', 100)->with('serviceTable', 'chef')->get()->toArray();
//        }
        $user = auth()->user()->id;
        $waiting = Sale::where(['chef_id' => null, 'prepared_at' => null, 'approve_cancel_kitchen' => false])->where('z_report_id', '!=', null)->with('serviceTable', 'chef')->orderBy('created_at', 'asc')->get()->toArray();
        $completed = Sale::where(['chef_id' => $user, 'progress' => 100, 'prepared_at' => null, 'approve_cancel_kitchen' => false])->where('z_report_id', '!=', null)->with('serviceTable', 'chef')->orderBy('created_at', 'asc')->get()->toArray();
        $inProgress = Sale::where(['chef_id' => $user, 'prepared_at' => null, 'approve_cancel_kitchen' => false])->where('progress', '!=', 100)->where('z_report_id', '!=', null)->with('serviceTable', 'chef')->orderBy('created_at', 'asc')->get()->toArray();

        $settings = $this->master();
        $ser_order_list_all = array_merge($completed, $inProgress, $waiting);

        // $ser_order_list_all = $openZReport->orders;

        $user = auth()->user();
        $sizeKeys = [
            'small' => __('Small'),
            'medium' => __('Medium'),
            'large' => __('Large')
        ];

        $ser_order_list = [];
        $canceled_orders = [];
        foreach($ser_order_list_all as $order) {
            if ($order['is_cancelled'] == true && $order['chef_id'] == $user->id) $canceled_orders[] = $order;
            else $ser_order_list[] = $order;
        }

        $order_list = [];
        $set_ids = [];
        foreach($ser_order_list as $i => $order) {
            if ($order['is_cancelled'] == true) {
                $canceled_orders[] = $order;
                continue;
            }

            $price = number_format(floatval($order['cart_total_price']), 2, '.', '');
            $view_price = $settings['currency_symbol_on_left'] ? $settings['currency_symbol'] . " " . $price : $price . " " . $settings['currency_symbol'];

            $status = $order['progress'] != 0 ? ($order['progress'] == 100 ? 3 : 2) : ($order['chef_id'] == null ? 1 : 2);
            $statuses = [__('Waiting'), __('In Progress'), __('Completed')];
            $status_colors = ['#E7B951', '#FF3636', '#29B93A'];


            $card_cancel = 0;
            $cancel_amount = 0;
            $order['paid_bank'] = floatval($order['paid_bank']);
            if ($order['is_paid'] && $order['paid_bank'] > 0) {
                $card_cancel = 1;
                $cancel_amount = $order['paid_bank'];
            }

            $order_type_name = __('Take away');
            if ($order['order_type'] == 'dine_in') {
                $order_type_name = __('Dine In');
                if (isset($order['service_table'])) $order_type_name .= ": " . $order['service_table']['title'];
                if (isset($order['locator'])) $order_type_name .= ": " . __('Locator') . " " . $order['locator'];
            }

            $order_data = [
                'id' => $order['id'],
                'view_price' => $view_price,
                'view_id' => $order['order_number'],
                'table' => $order_type_name,
                'status_code' => $status,
                'assignment' => $order['chef_id'] == null ? 1 : 2,
                'progress' => $order['progress'],
                'status' =>  $statuses[$status - 1],
                'status_color' =>  $status_colors[$status - 1],
                'time' => strtotime($order['created_at']), // date($order['created_at'])->format('H:i'),
                'view_date' => date('H:i', strtotime($order['created_at'])), // date($order['created_at'])->format('H:i'),
                'chef' => $order['chef'] == null ? 'N/A' : $order['chef']['name'],
                'note' => isset($order['note_for_chef']) ? implode('<br>', json_decode($order['note_for_chef'], true)) : '',
                'items' => [],
                'completed' => 0,
                'count' => $order['cart_total_items'],
                'card_cancel' => $card_cancel,
                'cancel_amount' => $cancel_amount
            ];

            foreach($order['items'] as $order_item) {
                $isMeal = $order_item['type'] == 'deal' || $order_item['type'] == 'meal';


                // The randomKey must only be outside the loop, once in the loop then it should only be a meal randomKey
                $list_item = $order_item;
                $list_item['randomKey'] = 0;

                $current_items = [$list_item];
                if ($isMeal) {
                    if (!$order_item['category_for_kitchen']) continue;
                    $order_data['items'][] = [
                        'id' => $order_item['id'],
                        'mealName' => true,
                        'name' => $order_item['name'],
                        'quantity' => $order_item['quantity'],
                        'checked' => isset($order_item['is_ready']) ? $order_item['is_ready'] : 0,
                        'randomKey' => isset($order_item['randomKey']) ? $order_item['randomKey'] : 0,
                        'isMeal' => false
                    ];

                    $current_items = $order_item['products'];
                }

                foreach($current_items as $current_product) {
                    $show_kitchen = $current_product['category_for_kitchen'];

                    if (!$show_kitchen && !$isMeal) {
                        $order_data['count'] = $order_data['count'] - 1;
                    }

                    $name = $current_product['name'];
                    /*
                    foreach($current_product['size'] as $sizeA => $size) {
                        if ($size != null) {
                            $name = $current_product['name']; // . ', ' . $sizeKeys[$sizeA];
                            break;
                        }
                    }
                     */

                    $modifiers = [];
                    if (isset($current_product['modifiers'])) {
                        $modifiers = $current_product['modifiers'];
                    }

                    $removed = [];
                    if (isset($current_product['removed_ingredients_names']))
                        $removed = array_map(function($ing) { return ['name' => $ing]; }, $current_product['removed_ingredients_names']);

                    $item = [
                        'id' => $current_product['id'],
                        'quantity' => $current_product['quantity'],
                        'isMeal' => $isMeal,
                        'mealName' => false,
                        'name' => $name,
                        'meal' => [
                            'checked' => isset($order_item['is_ready']) ? $order_item['is_ready'] : 0,
                            'id' => $order_item['id']
                        ],
                        'checked' => isset($current_product['is_ready']) ? $current_product['is_ready'] : 0,
                        'randomKey' => isset($order_item['randomKey']) ? $order_item['randomKey'] : 0,
                        'childRandomKey' => isset($current_product['randomKey']) ? $current_product['randomKey'] : 0,
                        'extra' => $modifiers,
                        'removed' => $removed,
                        'show_kitchen' => $show_kitchen,
                    ];
                    $order_data['items'][] = $item;
                }
            }

            if (in_array($order_data['id'], $set_ids)) continue;

            $order_list[] = $order_data;
            $set_ids[] = $order_data['id'];
        }

        $autoPrintOrders = [];
        if ($isAutoPrinted){
            $autoPrintOrders = $this->getAutoPrintOrders();
        }
        return $this->response(0, '', ['orders' => $order_list, 'canceled_orders' => $canceled_orders, 'auto_print_orders' => $autoPrintOrders]);
    }

    public function kitchenList()
    {
        $order_list = Sale::whereDate('created_at', \Carbon\Carbon::today())->orderBy('created_at', 'asc')->get();

        // $openZReport = ZReport::where(['end_z_report' => null, 'cash_register_id' => session()->get('cash_register')])->first();
        // $order_list = $openZReport?->orders;
        return view('kitchen/kitchen-index', compact('order_list'));
    }


    public function assignToMe(Request $request)
    {
        $user = auth()->user();
        $sale = Sale::where('id', $request->order_id)->first();
        if (!$sale) {
            return $this->response(1, __("This order doesn't exist!"));
        }
        $sale->chef_id = $user->id;
        $staticData = $sale->static_data;
        $staticData['chef_name'] = $user->name;
        $sale->static_data = $staticData;
        $success = $sale->update();
        if (!$success) {
            return $this->response(1, __("This order doesn't update!"));
        }
        return $this->response(0, "");
    }

    public function prepareItem(Request $request)
    {
        $data = $request->all();
        $sale = Sale::where('id', $data['order_id'])->first();
        if (!$sale) {
            return $this->response(1, __("This order doesn't exist!"));
        } elseif ($sale->chef_id == null) {
            return $this->response(1, __("You must assign this order, because the chef is not defined!"));
        }
        $items = [];
        $readyItems = 0;
        foreach ($sale->items as $key => $item) {
            if ($data['randomKey'] == $item['randomKey']) {
                if (isset($data['childRandomKey'])) {
                    $currentStatus = $item['is_ready'];
                    $mealReady = true;
                    foreach ($item['products'] as $key1 => $product) {
                        if ($product['randomKey'] == $data['childRandomKey']){
                            if ($data['added'] == 'true'){
                                $item['products'][$key1]['is_ready'] = true;
                            }else{
                                $item['products'][$key1]['is_ready'] = false;
                            }
                        }
                        if (!$item['products'][$key1]['is_ready']) $mealReady = false;
                    }
                    $item['is_ready'] = $mealReady;
                    if ($currentStatus != $mealReady){
                        if ($mealReady){
                            $sale->cost_during_preparation += $item['cost'];
                        }else{
                            $sale->cost_during_preparation -= $item['cost'];
                        }
                    }
                }else{
                    if ($data['added'] == 'true'){
                        $item['is_ready'] = true;
                        $sale->cost_during_preparation += $item['cost'];
                    }else{
                        $item['is_ready'] = false;
                        $sale->cost_during_preparation -= $item['cost'];
                    }
                }

                $items[$key] = $item;
            } else {
                $items[$key] = $item;
            }
            if ($item['is_ready']) {
                $readyItems += 1;
            }
        }
        $progress = $readyItems / count($items) * 100;
        $sale->progress = $progress;
        $sale->is_preparing = $readyItems != 0;
        $sale->items = $items;
        $success = $sale->update();
        if (!$success) {
            return $this->response(1, __("This order doesn't update!"));
        }
        return $this->response(0, "");
    }

    public function confirmOrder(Request $request)
    {
        $orderId = $request->get('order_id');
        $sale = Sale::where('id', $orderId)->first();
        if (!$sale) {
            return $this->response(1, __("This order doesn't exist!"));
        } elseif ($sale->progress != 100) {
            return $this->response(1, __("You have not completed all the products!"));
        }
        $sale->prepared_at = Carbon::now();
        $success = $sale->update();
        if (!$success) {
            return $this->response(1, __("This order doesn't update!"));
        }
        return $this->response(0, "");
    }

    public function settings()
    {
        $printers = PrintSettings::where('device_status', true)->get();
        return view('kitchen.kitchen-settings', compact('printers'));
    }

    public function approveCancelKitchen(Request $request)
    {
        $orderId = $request->get('order_id');
        $sale = Sale::where('id', $orderId)->first();
        if (!$sale) {
            return $this->response(1, __("This order doesn't exist!"));
        }
        $sale->approve_cancel_kitchen = true;
        $sale->update();
        return $this->response(0, "");
    }

    public function printKitchenOrder(Request $request){
        $orderId = $request->order_id;
        $order = Sale::find($orderId);
        if (!$order){
            return $this->response('1', __('This order does not exist, please refresh the page!'));
        }
        $inject = new \App\Services\OrderKitchenInvoice($order);
        $printData = $inject->getItemDataForPrinter();
        $printString = $inject->prepareString($printData);
        return $this->response('0', "" ,null, null, $printString);
    }

    public function printKitchenItem(Request $request){
        $data = $request->all();
        $orderId = $data['order_id'];
        $randomKey = $data['random_key'];
        $childRandomKey = $data['child_random_key'] ?? false;
        $items = Sale::find($orderId);
        if (!isset($items)){
            return $this->response('1', __('This item does not exist, please refresh the page!'));
        }
        $index1 = 1000; $index2 = 1000;
        foreach ($items->items as $key => $item){
            if ($item['randomKey'] == $randomKey){
                $index1 = $key;
                if ($childRandomKey){
                    foreach ($item['products'] as $key1 => $product){
                        if ($product['randomKey'] == $childRandomKey){
                            $index2 = $key1;
                        }
                    }
                }
            }
        }
//        if (isset($items->items[$index])){
            $inject = new ItemKitchenInvoice(Sale::find($orderId), $index1, $index2, $childRandomKey);
            $printData = $inject->getItemDataForPrinter();
            $printString = $inject->prepareString($printData);
            return $this->response('0', "" ,null, null, $printString);
//        }
//        return $this->response('1', __('This item does not exist, please refresh the page!'));
    }

    public function assignDeviceInKitchen(Request $request)
    {
        $data = $request->all();
        $message = __('These devices are used: ');
        $usedDevices = false;
        PrintSettings::where('kitchen_id', $data['device_kitchen_id'])
            ->update([
                'kitchen_id' => null,
                'cash_register_or_e_kiosk' => null,
                'cash_register_id' => null,
                'e_kiosk_id' => null,
                'cash_register_or_e_kiosk_assigned' => false
            ]);

        foreach ($data['device_ids'] as $deviceId) {
            $device = PrintSettings::where(['id' => $deviceId])->first();
            if ($device) {
                $device->kitchen_id = $data['device_kitchen_id'];
                $device->cash_register_or_e_kiosk_assigned = true;
                $device->update();
            } else {
                $message .= $device->device_name . '(' . $device->device_ip . '), ';
                $usedDevices = true;
            }
        }
        if ($usedDevices) {
            return $this->response(1, $message);
        }
        return $this->response(0, __('Success!'));
    }

    private function response($status, $message, $data = [], $redirectUrl = null, $printOrder = null)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'redirect_uri' => $redirectUrl,
            'print_order' => $printOrder
        ], 200);
    }

}
