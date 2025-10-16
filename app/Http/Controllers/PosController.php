<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\AuthController;
use App\Models\CashRegister;
use App\Models\Location;
use App\Models\PrintSettings;
use App\Models\Sale;
use App\Models\Tax;
use App\Models\FoodCategory;
use App\Models\FoodItem;
use App\Models\Meal;
use App\Models\Modifier;
use App\Models\PaymentMethod;
use App\Models\ServiceTable;
use App\Models\ZReport;
use App\Services\SaleInvoice;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Webkul\Customer\Http\Controllers\RegistrationController as BaseRegistrationController;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $categories = FoodCategory::where('id', '!=', config('constants.api.dealId'))->where(['status' => true, 'category_for_pos' => true])->get();
        $foodCategory = [];
        $drinkCategory = [];
        foreach ($categories as $category) {
            if ($category->products()->where('status', true)->count() > 0) {
                if ($category->category_for_kitchen === true && $category->category_to_ask_for_extra_kitchen === true){
                    $foodCategory[] = $category;
                }else{
                    $drinkCategory[] = $category;
                }
            }
        }
        $categories = array_merge(
            $foodCategory,
            $drinkCategory
        );
        $dealCategory = null;
        $checkDealCategory = FoodCategory::where('id', config('constants.api.dealId'))->where('status', true)->first();
        if ($checkDealCategory?->deals()->where('status', true)->count() > 0){
            $dealCategory = $checkDealCategory;
        }
        $paymentMethods = PaymentMethod::where('status', true)->get();
        $serviceTables = ServiceTable::get();
        $modifiers = Modifier::where('status', true)->get();
        $cashRegisters = CashRegister::where('status', true)->get();
        $taxes = Tax::get();
        if ($this->isAdmin()) {
            $currentLocation = session()->get('localization_for_changes_data');
            if (!$currentLocation) {
                return view('pop-up-locations');
            } elseif (!session()->get('cash_register')) {
                if (count($cashRegisters) == 1) {
                    session()->put('cash_register', $cashRegisters[0]->id);
                    session()->put('cash_register_data', $cashRegisters[0]);
                } else {
                    return view('pos/pop-up-cash-register-pos', compact('cashRegisters'));
                }
            } elseif (!$currentLocation->pos) {
                return redirect()->route('dashboard')->withErrors(['pos_error' => $currentLocation->name . ' ' . __("location has not allowed POS in the system, if you want to continue please enable it in the system")]);
            }
        } else {
            if (!session()->get('cash_register')) {
                if (count($cashRegisters) == 1) {
                    session()->put('cash_register', $cashRegisters[0]->id);
                    session()->put('cash_register_data', $cashRegisters[0]);
                } else {
                    return view('pos/pop-up-cash-register-pos', compact('cashRegisters'));
                }
            } elseif (!auth()->user()->location?->pos) {
                return view('pos/alert-disabled-pos')->withErrors(['pos_error' => auth()->user()->location?->name . ' ' . __("location has not allowed POS in the system, if you want to continue please enable it in the system")]);
            }
        }
        $openZReport = ZReport::where(['end_z_report' => null, 'cash_register_id' => session()->get('cash_register')])->first();
        if (!$openZReport) {
            return view('pos/z-report-create');
        }
        $order = null;
        if ($request->get('order_id')) {
            $order = Sale::find($request->get('order_id'));
        }
	  
	          $months = [];
        $zReports = ZReport::where('cash_register_id', session()->get('cash_register'))->where('total_sales', '!=', 0)->get();
        if (count($zReports) > 0) {
            $months = [];
            foreach ($zReports as $zReport){
                if(!in_array(now()::parse($zReport->created_at)->month, $months)){
                    $months[now()::parse($zReport->created_at)->month] = __(now()::parse($zReport->created_at)->format('F'));
                }
            }
        }


        // $inject = new \App\Services\SaleInvoice(\App\Models\Sale::orderBy('created_at', 'desc')->first());
        // $printData = $inject->prepareString($inject->getDataForPrinter());

	  return view('pos/pos-index', compact('categories', 'paymentMethods', 'serviceTables', 'modifiers', 'taxes', 'order', 'months', 'dealCategory'/*, 'printData'*/));
    }

    public function getProducts(FoodCategory $foodCategory): JsonResponse
    {
        $products = $foodCategory->products()->where('status', true)->with('ingredients')->get();
        $modifiers = $foodCategory->modifiers()->where('status', true)->with('ingredients')->get();
        if (count($products) == 0) {
            return $this->response(1, __("This category doesn't have products"));
        }
        return $this->response(0, "", ['products' => $products, 'modifiers' => $modifiers]);
    }

    public function getMeals(): JsonResponse
    {
        $meals = Meal::where('status', true)->with('foodItems', 'foodItems.ingredients')->get();
        $newMeal = $meals->map(function ($meal) {
            $categories_id = [];
            foreach ($meal->foodItems as $foodItem) {
                $category_id = $foodItem->food_category_id;
                if (!in_array($category_id, $categories_id)) {
                    $categories_id[] = $category_id;
                }
            }
            $modifiers = Modifier::whereIn('id', function ($query) use ($categories_id) {
                $query->select('modifier_id')
                    ->from('categories_modifiers')
                    ->whereIn('category_id', $categories_id);
            })->where('status', true)->with('category')->get();
            $meal->modifiers = $modifiers;
            return $meal;
        });
        if (count($newMeal) == 0) {
            return $this->response(1, __("This category doesn't have products"));
        }
        return $this->response(0, "", ['meals' => $newMeal]);
    }

    public function getProductsFromCategories(Request $request): JsonResponse
    {
        $categories = $request->category_ids;
        $products = FoodItem::where('status', true)->whereIn('food_category_id', $categories)->with('ingredients')->get();
        $modifiers = Modifier::where('status', true)->whereIn('id', function ($query) use ($categories) {
            $query->select('modifier_id')
                ->from('categories_modifiers')
                ->whereIn('category_id', $categories);
        })->with('category')->get();
        if (count($products) == 0) {
            return $this->response(1, __("This category doesn't have products"));
        }
        return $this->response(0, "", ['products' => $products, 'modifier' => $modifiers]);
    }


    public function getTables()
    {
        $tables = ServiceTable::get();
        if (count($tables) == 0) {
            return $this->response(1, __("Tables doesn't exists"));
        }
        return $this->response(0, "", ['tables' => $tables]);
    }

    public function kitchen()
    {
        $waiting = Sale::where(['chef_id' => null, 'prepared_at' => null, 'is_cancelled' => false])->with('serviceTable')->orderBy('created_at', 'asc')->get()->toArray();
        $completed = Sale::where(['progress' => 100, 'prepared_at' => null, 'is_cancelled' => false])->with('serviceTable')->orderBy('created_at', 'asc')->get()->toArray();
        $inProgress = Sale::where(['prepared_at' => null, 'is_cancelled' => false])->where('progress', '!=', 100)->where('chef_id', '!=', NULL)->with('serviceTable')->orderBy('created_at', 'asc')->get()->toArray();
        $order_list = array_merge($completed, $inProgress, $waiting);

        return view('pos.kitchen-index', compact('order_list'));
    }

    public function queReady(Request $request){
        $ready = $request->ready;
        $orderId = $request->order_id;
        if ($ready != 'true'){
            return $this->response(1, __("Sorry, looks like there are some errors in server, please try again."));
        }
        $sale = Sale::find($orderId);
        $sale->que_ready = true;
        $sale->update();
        return $this->response(0, __("Success!"));
    }

    public function ready()
    {
        $order_list = Sale::where('prepared_at', '!=', null)->where('z_report_id', '!=', null)->where(['completed_at' => null, 'is_cancelled' => false])->orderBy('created_at', 'asc')->get();
        return view('pos.ready-index', compact('order_list'));
    }

    public function orderReady()
    {
        $ser_order_list_all = Sale::where('prepared_at', '!=', null)->where('z_report_id', '!=', null)->where(['completed_at' => null, 'is_cancelled' => false])->orderBy('created_at', 'asc')->get();

        $settings = $this->master();

        $user = auth()->user();
        $sizeKeys = [
            'small' => __('Small'),
            'medium' => __('Medium'),
            'large' => __('Large')
        ];

        $ser_order_list = [];
        foreach ($ser_order_list_all as $order) {
            if ($order['is_cancelled'] == true && $order['chef_id'] == $user->id) continue;
            else $ser_order_list[] = $order;
        }

        $order_list = [];
        $set_ids = [];
        foreach ($ser_order_list as $i => $order) {
            if ($order['is_cancelled'] == true) {
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
                'status' => $statuses[$status - 1],
                'que_ready' => $order['que_ready'],
                'status_color' => $status_colors[$status - 1],
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

            foreach ($order['items'] as $order_item) {
                $isMeal = $order_item['type'] == 'deal' || $order_item['type'] == 'meal';


                $current_items = [$order_item];
                if ($isMeal) {
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

                foreach ($current_items as $current_product) {
                    $show_kitchen = $current_product['category_for_kitchen'];

                    if (!$show_kitchen && !$isMeal) {
                        $order_data['count'] = $order_data['count'] - 1;
                    }

                    $name = $current_product['name'];
                    /*
                    foreach ($current_product['size'] as $sizeA => $size) {
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
                        $removed = array_map(function ($ing) {
                            return ['name' => $ing];
                        }, $current_product['removed_ingredients_names']);

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

        return $this->response(0, '', ['orders' => $order_list, 'canceled_orders' => []]);
    }

    public function acknowledgeOrder(Request $request)
    {
        $orderId = $request->order_id;
        $order = Sale::find($orderId);
        if (!$order) {
            return $this->response(1, __("This order doesn't exists"));
        }
        $order->completed_at = now();
        $staticData = $order->static_data;
        $staticData['acknowledged_by'] = auth()->user()->name;
        $order->static_data = $staticData;
        $order->update();
        return $this->response(0, "");
    }

    public function eKiosk()
    {
        $order_list = Sale::where('e_kiosk_id', '!=', null)->where(['is_cancelled' => false, 'z_report_id' => null])->orderBy('created_at', 'asc')->get();
        return view('pos.e-kiosk-index', compact('order_list'));
    }

    public function eKioskOrders()
    {
        $ser_order_list_all = Sale::where('e_kiosk_id', '!=', null)->where(['is_cancelled' => false, 'z_report_id' => null])->orderBy('created_at', 'asc')->get();

        $settings = $this->master();

        $user = auth()->user();
        $sizeKeys = [
            'small' => __('Small'),
            'medium' => __('Medium'),
            'large' => __('Large')
        ];

        $ser_order_list = [];
        foreach ($ser_order_list_all as $order) {
            if ($order['is_cancelled'] == true && $order['chef_id'] == $user->id) continue;
            else $ser_order_list[] = $order;
        }

        $order_list = [];
        $set_ids = [];
        foreach ($ser_order_list as $i => $order) {
            if ($order['is_cancelled'] == true) {
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
                'status' => $statuses[$status - 1],
                'status_color' => $status_colors[$status - 1],
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

            foreach ($order['items'] as $order_item) {
                $isMeal = $order_item['type'] == 'deal' || $order_item['type'] == 'meal';


                $current_items = [$order_item];
                if ($isMeal) {
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

                foreach ($current_items as $current_product) {
                    $show_kitchen = $current_product['category_for_kitchen'];

                    if (!$show_kitchen && !$isMeal) {
                        $order_data['count'] = $order_data['count'] - 1;
                    }

                    $name = $current_product['name'];
                    /*
                    foreach ($current_product['size'] as $sizeA => $size) {
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
                        $removed = array_map(function ($ing) {
                            return ['name' => $ing];
                        }, $current_product['removed_ingredients_names']);

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

        return $this->response(0, '', ['orders' => $order_list, 'canceled_orders' => []]);
    }

    public function history()
    {
        $openZReport = ZReport::where(['end_z_report' => null, 'cash_register_id' => session()->get('cash_register')])->first();
        $order_list = $openZReport->orders()->orderBy('created_at', 'desc')->get();
        return view('pos.history-index', compact('order_list'));
    }

    public function settings(Request $request)
    {
        $printers = PrintSettings::where('device_status', true)->get();
        if (!$this->currentLocation()->integrated_payments){
            foreach ($printers as $key => $printer){
                if ($printer->device_type == 'terminal'){
                    unset($printers[$key]);
                }
            }
        }
        $manualPayment = false;
        if ($this->currentLocation()->manual_payments){
            $manualPayment = true;
        }

        $default_size = false;
        $settingsPin = false;
        if ($request->pin){
            if (CashRegister::find(session()->get('cash_register'))?->pin_for_settings == $request->pin){
                $settingsPin = true;
            }

            $view = view('pos.settings-index', compact('printers', 'default_size', 'settingsPin', 'manualPayment'));
            if (!$settingsPin) {
                $view->withErrors(['pos_error' => 'The pin was incorrect']);
            }

            return $view;
        }

        return view('pos.settings-index', compact('printers', 'default_size', 'settingsPin', 'manualPayment'));
    }

    public function getSaveOrder(Request $request)
    {
        $tableId = $request->get('table_id');
        $order = ZReport::where([
            'cash_register_id' => session()->get('cash_register'),
            'end_z_report' => null
        ])->with('orders')->latest('created_at')->first()->orders()->where(['table_id' => $tableId, 'order_type' => 'dine_in', 'save_order' => true])->latest()->first();

        if (!$order) {
            return $this->response(1, __("Order doesn't exist, or has been paid"));
        }
        return $this->response(0, "", ['order' => $order]);
    }

    public function locationsCashRegisterForPos(Request $request)
    {
        $data = $request->all();
        $cashRegister = CashRegister::where('status', true)->where(['id' => $data['cash_register'], 'pin' => $data['pin']])->first();
        if (!$cashRegister) {
            return back()->with('error', __('Your pin is incorrect.'))->withInput(['cash_register' => $data['cash_register']]);
        }
        session()->put('cash_register', $cashRegister->id);
        session()->put('cash_register_data', $cashRegister);
        return back();
    }

    public function createZReport(Request $request)
    {
        $cashRegisterId = session()->get('cash_register');
        $lastZReport = ZReport::where('cash_register_id', $cashRegisterId)->latest('created_at')->first();
        ZReport::create([
            'open_user_id' => auth()->id(),
            'cash_register_id' => $cashRegisterId,
            'report_number' => $lastZReport ? $lastZReport->report_number + 1 : 1,
            'saldo' => $request->saldo,
            'start_z_report' => now(),
        ]);
        return back();
    }

    public function getProductsIds(Request $request)
    {
        $foodItems = FoodItem::where('status', true)->whereIn('id', $request->product_ids)->with('ingredients')->get();
        $categories_id = [];
        foreach ($foodItems as $foodItem) {
            $category_id = $foodItem->food_category_id;
            if (!in_array($category_id, $categories_id)) {
                $categories_id[] = $category_id;
            }
        }
        $modifiers = Modifier::where('status', true)->whereIn('id', function ($query) use ($categories_id) {
            $query->select('modifier_id')
                ->from('categories_modifiers')
                ->whereIn('category_id', $categories_id);
        })->with('category')->get();
        if (count($foodItems) == 0) {
            return $this->response(1, __("These products do not exist!"));
        }
        return $this->response('0', '', ['food_items' => $foodItems, 'modifiers' => $modifiers]);
    }

    public function endZReport(Request $request)
    {
        $closingAmount = $request->closing_amount ?? 0;
        $zReport = ZReport::where(['cash_register_id' => session()->get('cash_register'), 'end_z_report' => null])->first();
        $checkIfOrdersNotCompleted = Sale::where('z_report_id', $zReport->id)->where(['completed_at' => null, 'is_cancelled' => 0])->first();
        if (!$zReport || $checkIfOrdersNotCompleted != null) {
            return $this->response(1, __("You have some unfinished orders, that's why you can't finish the daily shift, please finish the unfinished orders then make the zReport, or you dont have any zReport for this cash register!"));
        }
        if (ZReport::where('end_z_report', null)->count() === 1){
            $ser_order_list_all = Sale::where('e_kiosk_id', '!=', null)->where(['is_cancelled' => false, 'z_report_id' => null])->orderBy('created_at', 'asc')->count();
            if ($ser_order_list_all > 0){
                return $this->response(1, __("You have orders made in eKiosk that have not been processed, please process them so that you can close the report"));
            }
        }
        $zReport->closing_amount = $closingAmount;
        $zReport->close_user_id = auth()->user()->id;
        $zReport->end_z_report = now();

        $zReport->update();
        session()->forget('cash_register');
//        session()->forget('login_without_auth');
        // $inject = new \App\Services\ZReportInvoice(ZReport::find($zReport->id));
        $inject = new \App\Services\ZReportInvoice($zReport);
        $printData = $inject->getZReportDataForPrinter();
        $printString = $inject->prepareString($printData);
        $printData = [
            'cash_register_name' => $zReport->cashRegister->name,
            'cash_register_key' => $zReport->cashRegister->key,
            'location_name' => $zReport->location->name,
            'shift_status' => __('Closed'),
            'shift_opened' => (new DateTime($zReport->start_z_report))->format("d/m/Y - H:i A"),
            'total_net_sales' => $zReport->orders()->where('is_cancelled', false)->sum('payable_after_all') - $zReport->orders()->where('is_cancelled', false)->sum('tax_amount'),
            'opening_amount' => $zReport->saldo,
            'expected_amount' => ($zReport->orders()->where('is_cancelled', false)->sum('paid_cash') - $zReport->orders()->where('is_cancelled', false)->sum('payment_return')) + $zReport->saldo,
            'short_over' => $closingAmount - ($zReport->orders()->where('is_cancelled', false)->sum('paid_cash') - $zReport->orders()->where('is_cancelled', false)->sum('payment_return') + $zReport->saldo),
            'cash_sales' => $zReport->orders()->where('is_cancelled', false)->sum('paid_cash') - $zReport->orders()->where('is_cancelled', false)->sum('payment_return'),
            'cash_returns' => $zReport->orders()->where('is_cancelled', true)->sum('paid_cash') - $zReport->orders()->where('is_cancelled', true)->sum('payment_return')
        ];
        app(AuthController::class)->logout($request, true);
        return $this->response('0', __("You have closed the Z Report"), $printData, null, $printString);
    }

    public function printXReport()
    {
        $zReport = ZReport::where(['cash_register_id' => session()->get('cash_register'), 'end_z_report' => null])->first();
        $orders = $zReport->orders()->where('is_cancelled', false)->get();
        $employees = [];
        foreach ($orders as $order) {
            if (!in_array($order->taker?->name, $employees)) {
                $employees[] = $order->taker?->name;
            }
        }
        $inject = new \App\Services\XReportInvoice($zReport);
        $printData = $inject->getXReportDataForPrinter();
        $printString = $inject->prepareString($printData);
        $data = [
            'cash_register_name' => $zReport->cashRegister->name,
            'cash_register_key' => $zReport->cashRegister->key,
            'location_name' => $zReport->location->name,
            'employees' => $employees,
            'shift_status' => __('Open'),
            'shift_opened' => (new DateTime($zReport->start_z_report))->format("d/m/Y - H:i A"),
            'total_net_sales' => $zReport->orders()->where(['is_cancelled' => false, 'is_paid' => true])->sum('payable_after_all') - $zReport->orders()->where(['is_cancelled' => false, 'is_paid' => true])->sum('tax_amount'),
            'opening_amount' => $zReport->saldo,
            'expected_amount' => ($zReport->orders()->where('is_cancelled', false)->sum('paid_cash') - ($zReport->orders()->where('is_cancelled', false)->sum('payment_return')) + $zReport->saldo),
            'short_over' => 0.00,
            'cash_sales' => $zReport->orders()->where(['is_cancelled' => false, 'is_paid' => true])->sum('paid_cash') - $zReport->orders()->where(['is_cancelled' => false, 'is_paid' => true])->sum('payment_return'),
            'cash_returns' => $zReport->orders()->where(['is_cancelled' => true, 'is_paid' => true])->sum('paid_cash') - $zReport->orders()->where(['is_cancelled' => true, 'is_paid' => true])->sum('payment_return')
        ];
        return $this->response('0', __(""), $data, null, $printString);
    }

    public function printPosOrder(Request $request) {
        try {
            $orderId = $request->order_id;
            $order = Sale::find($orderId);
            if (!$order){
                return $this->response('1', __('This order does not exist, please refresh the page!'));
            }

            $inject = new SaleInvoice($order);
            $printData = $inject->getDataForPrinter();
            $printString = $inject->prepareString($printData);

            return $this->response(0, __(""), ['string' => $printString]);
        } catch (\Exception $e) {
            return $this->response(1, $e->getMessage());
        }
    }

    public function getClosingAmount()
    {
        $zReport = ZReport::where(['cash_register_id' => session()->get('cash_register'), 'end_z_report' => null])->first();
        return $this->response('0', __(""), ['saldo' => $zReport->saldo, 'total_balance_with_cash' => $zReport->total_balance_with_cash]);
    }

    public function cancelOrder(Request $request)
    {
        $data = $request->all();
        $sale = Sale::find($data['order_id']);

        // Ky kod duhet te kalohet tek cancel order pas krijimit jo tek refund, sepse ky kancel realisht eshte refund
        if ($request->get('url_from') == 'e_kiosk'){
            $sale->delete();
            return $this->response(0, __("Cancel order has been done successfully!"));
        }
        // end

        if ($sale) {
            $printString = "";
            if(isset($data['payment_data_canceled'])){
                $sale->payment_data_canceled = $data['payment_data_canceled'];
            }
            $sale->is_cancelled = true;
            $sale->cancellation_reason = $data['message'];
            $staticData = $sale->static_data;
            $staticData['cancellation_reason_by'] = auth()->user()->name;
            $sale->static_data = $staticData;
            $sale->update();
            $bookTable = ServiceTable::where(['order_id' => $sale->id])->first();
            if ($bookTable) {
                $bookTable->is_booked = false;
                $bookTable->order_id = null;
                $bookTable->update();
            }
            if ($sale->payment_data_canceled != null){
                $inject = new \App\Services\OrderRefundInvoice($sale);
                $printData = $inject->getDataForPrinter();
                $printString = $inject->prepareString($printData);
            }
            return $this->response(0, __("Cancel order has been done successfully!"), [], null, $printString);
        }
        return $this->response(1, __("Cancel order failed"));
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $foodItems = FoodItem::where('name', 'like', "%{$searchTerm}%")->where('status', true)->with('ingredients')->get();
        $foodCategoryIds = FoodItem::where('name', 'like', "%{$searchTerm}%")
            ->get()
            ->pluck('food_category_id')
            ->unique()
            ->values();
        $modifiers = Modifier::whereHas('category', function ($query) use ($foodCategoryIds) {
            $query->whereIn('category_id', $foodCategoryIds);
        })->where('status', true)->get();

        return $this->response(0, null, ['products' => $foodItems, 'modifiers' => $modifiers]);
    }

    public function changeCashRegisterPos()
    {
        session()->remove('cash_register');
        return redirect()->back();
    }

    public function assignDeviceInPos(Request $request)
    {
        $data = $request->all();
        $message = __('These devices are used: ');
        $usedDevices = false;
        PrintSettings::where('cash_register_id', session()->get('cash_register'))
            ->update([
                'cash_register_or_e_kiosk' => null,
                'cash_register_id' => null,
                'e_kiosk_id' => null,
                'cash_register_or_e_kiosk_assigned' => false
            ]);
        foreach ($data['device_ids'] as $deviceId) {
            $device = PrintSettings::where(['id' => $deviceId])->first();
            if ($device && !$device->cash_register_or_e_kiosk_assigned) {
                $device->cash_register_or_e_kiosk = 'cash_register';
                $device->cash_register_id = session()->get('cash_register');
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

    public function logs(Request $request){
        try {
            $message = $request->message;
            $date = \Illuminate\Support\Carbon::now()->format('Y-m-d');
            $filename = "terminal_logs/pos_$date.txt";
            $logMessage = $message;
            Storage::disk('local')->append($filename, $logMessage);
            return response()->json(['message' => 'Log saved successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to save log.'], 500);
        }
    }

    public function printLastZReport(Request $request)
    {
        $pinForZReport = $request->pin_to_print_reports;
        $lastZReport = ZReport::where(['cash_register_id' => session()->get('cash_register')])->orderBy('created_at', 'desc')->whereNotNull('end_z_report')->first();
        $checking = CashRegister::where([
            'pin_to_print_reports' => $pinForZReport,
            'id' => session()->get('cash_register')
        ])->first();
        $printString = "";
        if ($lastZReport && $checking){
            $inject = new \App\Services\ZReportInvoice($lastZReport);
            $printData = $inject->getZReportDataForPrinter();
            $printString = $inject->prepareString($printData);
            return $this->response(0, '', ['printString' => $printString]);
        }
        return $this->response(1, __('Pin invalid!'));
    }

    public function pinToPrintReports(Request $request)
    {
        try {
            $pinForZReport = $request->pin_to_print_reports;
            $lastZReport = ZReport::where(['cash_register_id' => session()->get('cash_register')])->orderBy('created_at', 'desc')->whereNotNull('end_z_report')->first();
            $checking = CashRegister::where([
                'pin_to_print_reports' => $pinForZReport,
                'id' => session()->get('cash_register')
            ])->first();
            $printString = "";
            if ($lastZReport && $checking){
                $inject = new \App\Services\ZReportInvoice($lastZReport);
                $printData = $inject->getZReportDataForPrinter();
                $printString = $inject->prepareString($printData);
                return $this->response(0, '', ['printString' => $printString]);
            }
            return $this->response(1, __('Pin invalid!'));
//            $pinForZReport = $request->pin_to_print_reports;
//            $checking = CashRegister::where([
//                'pin_to_print_reports' => $pinForZReport,
//                'id' => session()->get('cash_register')
//            ])->first();
//            if ($checking){
//                return $this->response(0, '');
//            } else {
//                return $this->response(1, __('Pin invalid!'));
//            }
        } catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function response($status, $message, $data = [], $redirectUrl = null, $printOrder = null)
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
