<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AndroidModels\IncomingRequest;
use App\Models\AndroidModels\Kitchen;
use App\Models\AndroidModels\KitchenIncomingRequest;
use App\Models\PrintSettings;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\User;
use App\Services\ItemKitchenInvoice;
use App\Services\LogoutDevicesUsersService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Helpers\ResponseModel;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;

class KitchenController extends Controller
{
    protected $logoutDevicesUsersService;
    public function __construct(LogoutDevicesUsersService $logoutDevicesUsersService)
    {
        $this->logoutDevicesUsersService = $logoutDevicesUsersService;
    }
    public function orderKitchen(Request $request){
        try {
            $isAutoPrinted = $this->currentLocation()['auto_print'];
            if ($isAutoPrinted){
                $orderIds = $request->query->all() ?? [];
                Sale::whereIn('id', $orderIds)->update(['is_auto_printed' => true]);
            }

            $user = auth()->user()->id;
            $waiting = Sale::where(['chef_id' => null, 'prepared_at' => null, 'approve_cancel_kitchen' => false])->where('z_report_id', '!=', null)->with('serviceTable', 'chef')->orderBy('created_at', 'asc')->get()->toArray();
            $completed = Sale::where(['chef_id' => $user, 'progress' => 100, 'prepared_at' => null, 'approve_cancel_kitchen' => false])->where('z_report_id', '!=', null)->with('serviceTable', 'chef')->orderBy('created_at', 'asc')->get()->toArray();
            $inProgress = Sale::where(['chef_id' => $user, 'prepared_at' => null, 'approve_cancel_kitchen' => false])->where('progress', '!=', 100)->where('z_report_id', '!=', null)->with('serviceTable', 'chef')->orderBy('created_at', 'asc')->get()->toArray();

            $settings = $this->master();
            $ser_order_list_all = array_merge($completed, $inProgress, $waiting);

            $user = auth()->user();

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
            return ResponseModel::success(['orders' => $order_list, 'canceled_orders' => $canceled_orders, 'auto_print_orders' => $autoPrintOrders]);
        } catch (\Exception $e) {
            return ResponseModel::error($e->getMessage());
        }
    }

    public function kitchenList()
    {
        try {
            $order_list = Sale::/*whereDate('created_at', \Carbon\Carbon::today())->*/orderBy('created_at', 'asc')->limit(10)->get();
            $order_list = $order_list->map(function ($order) {
                if ($order->is_cancelled) {
                    $order->status = __('Refunded');
                    return $order;
                } elseif ($order->completed_at != null) {
                    $order->status = __('Completed');
                    return $order;
                } elseif ($order->chef_id != null) {
                    $order->status = __('In Progress');
                    return $order;
                } else {
                    $order->status = __('Waiting');
                    return $order;
                }
            });
            return ResponseModel::success(['order_list' => $order_list]);
        } catch (\Exception $e) {
            return ResponseModel::error($e->getMessage());
        }
    }


    public function assignToMe(Request $request)
    {
        try {
            $user = auth()->user();
            $sale = Sale::where('id', $request->order_id)->first();
            if (!$sale) {
                return ResponseModel::error(__('Sale not found.'), 404);
            }
            $sale->chef_id = $user->id;
            $staticData = $sale->static_data;
            $staticData['chef_name'] = $user->name;
            $sale->static_data = $staticData;
            $success = $sale->update();
            if (!$success) {
                return ResponseModel::error(__('Sale not found.'), 404);
            }
            return ResponseModel::success(__('Sale updated.'));
        } catch (\Exception $e){
            return ResponseModel::error($e->getMessage());
        }
    }

    public function prepareItem(Request $request)
    {
        try {
            $data = $request->all();
            $sale = Sale::where('id', $data['order_id'])->first();
            if (!$sale) {
                return ResponseModel::error(__("This order doesn't exist!"), 404);
            } elseif ($sale->chef_id == null) {
                return ResponseModel::error(__('You must assign this order, because the chef is not defined!'), 400);
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
                return ResponseModel::error(__("This order doesn't update!"), 404);
            }
            return ResponseModel::success(__('Sale updated.'));
        } catch (\Exception $e){
            return ResponseModel::error($e->getMessage());
        }
    }

    public function confirmOrder(Request $request)
    {
        try {
            $orderId = $request->get('order_id');
            $sale = Sale::where('id', $orderId)->first();
            if (!$sale) {
                return ResponseModel::error(__("Sale not found."), 404);
            } elseif ($sale->progress != 100) {
                return ResponseModel::error(__('You have not completed all the products!'), 404);
            }
            $sale->prepared_at = Carbon::now();
            $success = $sale->update();
            if (!$success) {
                return ResponseModel::error("This order doesn't update!");
            }
            return ResponseModel::success(__('Order confirmed.'));
        } catch (\Exception $e){
            return ResponseModel::error($e->getMessage());
        }
    }

    public function settings()
    {
        try {
            $printers = PrintSettings::where('device_status', true)->get();
            return ResponseModel::success(['printers' => $printers]);
        } catch (\Exception $e){
            return ResponseModel::error($e->getMessage());
        }
    }

    public function approveCancelKitchen(Request $request)
    {
        try {
            $orderId = $request->get('order_id');
            $sale = Sale::where('id', $orderId)->first();
            if (!$sale) {
                return ResponseModel::error(__("This order doesn't exist!"), 404);
            }
            $sale->approve_cancel_kitchen = true;
            $sale->update();
            return ResponseModel::success(__('Order cancelled.'));
        } catch (\Exception $e){
            return ResponseModel::error($e->getMessage());
        }

    }

    public function printKitchenOrder(Request $request){
        try {
            $orderId = $request->order_id;
            $order = Sale::find($orderId);
            if (!$order){
                return ResponseModel::error(__('This order does not exist, please refresh the page!'), 404);
            }
            $inject = new \App\Services\OrderKitchenInvoice($order);
            $printData = $inject->getItemDataForPrinter();
            $printString = $inject->prepareString($printData);
            return ResponseModel::success(['print_string' => $printString]);
        } catch (\Exception $e){
            return ResponseModel::error($e->getMessage());
        }
    }

    public function printKitchenItem(Request $request){
        try {
            $data = $request->all();
            $orderId = $data['order_id'];
            $randomKey = $data['random_key'];
            $childRandomKey = $data['child_random_key'] ?? false;
            $items = Sale::find($orderId);
            if (!isset($items)){
                return ResponseModel::error(__('This item does not exist, please refresh the page!'), 404);
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
            $inject = new ItemKitchenInvoice(Sale::find($orderId), $index1, $index2, $childRandomKey);
            $printData = $inject->getItemDataForPrinter();
            $printString = $inject->prepareString($printData);
            return ResponseModel::success(['print_string' => $printString]);
        } catch (\Exception $e){
            return ResponseModel::error($e->getMessage());
        }
    }

    public function assignDeviceInKitchen(Request $request)
    {
        try {
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
                return ResponseModel::error($message);
            }
            return ResponseModel::success(__("Success!"));
        } catch (\Exception $e){
            return ResponseModel::error($e->getMessage());
        }
    }

    public function userKitchenLogin(Request $request)
    {
        try {
            $credentials = $request->validate([
                'user_id' => 'required',
                'pin' => 'required'
            ]);
            $checkUser = User::where(['id' => $credentials['user_id'], 'pin' => $credentials['pin']])->first();
            if ($checkUser && Auth::loginUsingId($credentials['user_id'])) {
                $user = Auth::user();
                $token = $user->createToken('API Token')->plainTextToken;
                return ResponseModel::success(['token' => $token]);
            }
            return ResponseModel::error(__('Invalid credentials'));
        } catch (ValidationException $e) {
            return ResponseModel::error($e->getMessage());
        }
    }

    public function deviceKitchenLogin(Request $request)
    {
        try {
            $request->validate([
                'device_id' => 'required',
                'authentication_code' => 'required'
            ]);
            $deviceId = $request->device_id;
            $authenticationCode = $request->authentication_code;
            $kitchen = Kitchen::where(['kitchen_id' => $deviceId, 'authentication_code' => $authenticationCode, 'status' => true])->first();
            if ($kitchen) {
                $token = $kitchen->createToken($kitchen->name . '-AuthToken', ['kitchen'])->plainTextToken;
                return ResponseModel::success(['token' => $token]);
            } else {
                $kitchen = Kitchen::where(['kitchen_id' => $deviceId, 'status' => true])->first();
                if ($kitchen) {
                    return ResponseModel::error(__('Authentication code is invalid!'), 404);
                }
            }

            $checkIncomingRequest = IncomingRequest::where('device_id', $deviceId)->first();
            if ($checkIncomingRequest) {
                return ResponseModel::error(__('You have requested to register, but your request is still pending!'), 404);
            }
            return ResponseModel::error(__('You have not made a request to register'), 401);
        } catch (ValidationException $e) {
            return ResponseModel::error($e->getMessage(), 422);
        }
    }

    public function deviceKitchenRegister(Request $request)
    {
        try {
            $request->validate([
                'device_id' => 'required|unique:kitchens,kitchen_id|unique:kitchen_incoming_requests',
                'authentication_code' => 'required'
            ]);
            $data = $request->all();
            $auth_code = Setting::first()->auth_code_for_e_kiosks;
            if ($data['authentication_code'] !== $auth_code) {
                return ResponseModel::error(__('Authentication code is invalid!'), 401);
            }
            $register = KitchenIncomingRequest::create($data);
            if ($register) {
                return ResponseModel::success();
            }
            return ResponseModel::error(__('Something went wrong on the server!'), 422);
        } catch (ValidationException $e) {
            return ResponseModel::error(
                isset($e->validator->getMessageBag()->getMessages()['device_id'][0]) ?
                    $e->validator->getMessageBag()->getMessages()['device_id'][0] :
                    $e->validator->errors(), 400);
        }catch (\Exception $e) {
            return ResponseModel::error($e->getPrevious(), 422);
        }
    }

    public function users()
    {
        try {
            $users = User::where(['role_id' => config('constants.role.chefId'), 'status' => true])->select('id', 'email', 'language')->get();
            return ResponseModel::success(['users' => $users]);
        } catch (\Exception $exception){
            return ResponseModel::error($exception->getMessage());
        }
    }


    public function logout(Request $request)
    {
        try {
            $routeName = \Route::currentRouteName();
            if ($routeName === 'kitchen.user.logout'){
                $token = $request->bearerToken();
            } else if ($routeName === 'kitchen.device.logout'){
                $token = $request->header('Device-Token');
            } else {
                return ResponseModel::error(__('Invalid logout request'), 400);
            }
            return $this->logoutDevicesUsersService->logout($token);
        } catch (\Exception $exception) {
            return ResponseModel::error($exception->getMessage());
        }
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
}
