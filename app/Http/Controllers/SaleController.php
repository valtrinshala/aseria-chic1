<?php

namespace App\Http\Controllers;

use App\Helpers\PriceHelper;
use App\Models\FoodItem;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\Modifier;
use App\Models\Sale;
use App\Models\ServiceTable;
use App\Models\Setting;
use App\Models\Tax;
use App\Models\ZReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Milon\Barcode\DNS1D;
use Ramsey\Uuid\Uuid;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
	  // $sales = Sale::where('z_report_id', '!=', null)->orderBy('created_at', 'desc')->get();
        return view('orders/order-index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $categoryForKitchen = false;
            $items = [];
            $data = $request->all();
            if (isset($data['payment_method_id']['id']) && $data['payment_method_id']['id'] == config('constants.paymentMethod.paymentMethodCashId')) {
                if (($data['paid_cash'] * 1 - $data['payment_return'] * 1) > $data['paid_cash'] * 1) {
                    DB::rollBack();
                    return $this->response(1, __("You have not paid the full amount!"));
                }
            } elseif (isset($data['payment_method_id']['id']) && $data['payment_method_id']['id'] == config('constants.paymentMethod.paymentMethodMixId')) {
                if ($data['paid_cash'] * 1 < 0 || $data['paid_bank'] * 1 < 0 || $data['paid_bank'] > $data['cart_total_price']) {
                    DB::rollBack();
                    return $this->response(1, __("You have not paid the full amount!"));
                }
            }
            $saveOrder = null;
            if (isset($data['order_id'])) {
                $saveOrder = Sale::find($data['order_id']);
            }
            if (array_key_exists('is_manual_payment', $data)){
                $data['is_manual_payment'] = 1;
            }else{
                $data['is_manual_payment'] = 0;
            }
            $user = auth()->user();
            $type = [];
            $calculateCost = $this->getTotalCostAndStockIngredients($data);
            $eachCost = $calculateCost['eachCost'];
            $idOrder = null;
            $tax = Tax::where('type', $data['order_type'])->first();
            $orderReceipt = Sale::withTrashed()->where('is_paid', true)->orderBy('order_receipt', 'desc')->first();
            $discountInPercentage = $data['discount_amount'];
            if ($data['is_discount_in_percentage'] == "false") {
                $allSubTotals = 0;
                foreach ($data['items'] as $item) {
                    if ($item['type'] == 'product') {
                        $product = FoodItem::where('id', $item['id'])->first();
                    } elseif ($item['type'] == 'meal' || $item['type'] == 'deal') {
                        $product = Meal::where('id', $item['id'])->first();
                    }
                    $taxValue = $product->tax?->tax_rate ?? $tax->tax_rate;
                    $allSubTotals += $item['sub_total'] / (1 + $taxValue * 0.01);
                }
                $discountInPercentage = ($data['discount_amount'] / $allSubTotals) * 100;
            }
            $totalPayablePrice = 0;
            $totalDiscount = 0;
            $totalTax = 0;
            $taxSum = [];
            $sizeKeys = [
                'small' => __('Small'),
                'medium' => __('Medium'),
                'large' => __('Large')
            ];
            $sizes = [
                "small" => null,
                "medium" => null,
                "large" => null
            ];
            foreach ($data['items'] as $key => $item) {
                if (isset($data['order_id'])) {
                    foreach ($saveOrder['items'] as $product) {
                        if ($product['id'] == $item['id']) {
                            $items[$key]['is_ready'] = $product['is_ready'];
                            break;
                        } else {
                            $items[$key]['is_ready'] = false;
                        }
                    }
                } else {
                    $items[$key]['is_ready'] = false;
                }
                $items[$key]['randomKey'] = 100 + $key * 100;
                if ($item['type'] == 'product') {
                    $items[$key]['type'] = 'product';
                    $items[$key]['id'] = $item['id'];
                    $items[$key]['name'] = $item['name'];
                    $items[$key]['cost'] = $eachCost[$item['id']];
                    $items[$key]['price_per'] = $item['price_per'];
                    $items[$key]['quantity'] = $item['quantity'];
                    $items[$key]['sub_total'] = $item['sub_total'];
                    $product = FoodItem::where('id', $item['id'])->with('ingredients')->first();
                    $taxProd = $product->tax ?? $tax;
                    $taxValue = $taxProd->tax_rate;
                    $priceWithoutTax = $item['sub_total'] / (1 + $taxValue * 0.01);
                    $priceAfterDiscount = $priceWithoutTax - $priceWithoutTax * $discountInPercentage * 0.01;
                    $items[$key]['tax_amount'] = $priceAfterDiscount * $taxValue * 0.01;
                    $items[$key]['discount_amount'] = $priceWithoutTax - $priceAfterDiscount;
                    $items[$key]['tax_id'] = $product->tax?->tax_id ?? $tax->tax_id;
                    $items[$key]['tax_name'] = $product->tax?->name ?? $tax->name;
                    $items[$key]['payable_price'] = $priceAfterDiscount + $priceAfterDiscount * $taxValue * 0.01;
                    $items[$key]['price_neto'] = $priceAfterDiscount;
                    $items[$key]['profit'] = $priceAfterDiscount - $eachCost[$item['id']];

                    if (isset($taxSum[$taxProd->id])) {
                        $taxSum[$taxProd->id]['tax_amount'] += $items[$key]['tax_amount'];
                    } else {
                        $taxSum[$taxProd->id]['tax_amount'] = $items[$key]['tax_amount'];
                        $taxSum[$taxProd->id]['tax_id'] = $items[$key]['tax_id'];
                        $taxSum[$taxProd->id]['tax_name'] = $items[$key]['tax_name'];
                        $taxSum[$taxProd->id]['tax_rate'] = $taxValue;
                    }
                    $totalPayablePrice += $priceAfterDiscount + $priceAfterDiscount * $taxValue * 0.01;
                    $totalDiscount += $priceWithoutTax - $priceAfterDiscount;
                    $totalTax += $priceAfterDiscount * $taxValue * 0.01;

                    $sizeObject = $item['size'] ?? $sizes;
                    $items[$key]['size'] = $sizeObject;

                    foreach($sizeObject as $k => $value) {
                        if ($value !== null) {
                            $items[$key]['name'] = $product->name. ', ' . $sizeKeys[$k];
                            break;
                        }
                    }

                    $items = $this->getItems($product, $items, $key);
                    $items[$key]['size'] = $item['size'];
                    if (!$product->category?->category_for_kitchen) {
                        $items[$key]['is_ready'] = true;
                    } else {
                        $categoryForKitchen = true;
                    }
                    $items[$key]['category_for_kitchen'] = $product->category?->category_for_kitchen;
                    $items[$key]['ingredients'] = $item['ingredients'] ?? [];
                    $items[$key]['removed_ingredients_names'] = $item['removed_ingredients_names'] ?? [];
                    $items[$key]['modifiers'] = [];
                    if (isset($item['modifiers']) && is_array($item['modifiers'])) {
                        $items[$key]['modifiers'] = $item['modifiers'];
                    }
                    if (!in_array('product', $type)) {
                        $type[] = 'product';
                    }
                } elseif ($item['type'] == 'meal' || $item['type'] == 'deal') {
                    $meal = Meal::where('id', $item['id'])->with('foodItems', 'foodItems.ingredients')->first();
                    $items[$key]['type'] = 'deal';
                    $items[$key]['id'] = $item['id'];
                    $items[$key]['cost'] = $eachCost[$item['id']];
                    $items[$key]['price_per'] = $item['price_per'];
                    $items[$key]['name'] = $item['name'];
                    $items[$key]['quantity'] = $item['quantity'];
                    $items[$key]['sub_total'] = $item['sub_total'];
                    $items = $this->getItems($meal, $items, $key);
                    if (!in_array('deal', $type)) {
                        $type[] = 'deal';
                    }
                    $taxProd = $meal->tax ?? $tax;
                    $taxValue = $taxProd->tax_rate;

                    $priceWithoutTax = $item['sub_total'] / (1 + $taxValue * 0.01);
                    $priceAfterDiscount = $priceWithoutTax - $priceWithoutTax * $discountInPercentage * 0.01;
                    $items[$key]['tax_amount'] = $priceAfterDiscount * $taxValue * 0.01;
                    $items[$key]['discount_amount'] = $priceWithoutTax - $priceAfterDiscount;
                    $items[$key]['tax_id'] = $meal->tax?->tax_id ?? $tax->tax_id;
                    $items[$key]['tax_name'] = $product->tax?->name ?? $tax->name;
                    $items[$key]['payable_price'] = $priceAfterDiscount + $priceAfterDiscount * $taxValue * 0.01;
                    $items[$key]['price_neto'] = $priceAfterDiscount;
                    $items[$key]['profit'] = $priceAfterDiscount - $eachCost[$item['id']];
                    if (isset($taxSum[$taxProd->id])) {
                        $taxSum[$taxProd->id]['tax_amount'] += $items[$key]['tax_amount'];
                    } else {
                        $taxSum[$taxProd->id]['tax_amount'] = $items[$key]['tax_amount'];
                        $taxSum[$taxProd->id]['tax_id'] = $items[$key]['tax_id'];
                        $taxSum[$taxProd->id]['tax_name'] = $items[$key]['tax_name'];
                        $taxSum[$taxProd->id]['tax_rate'] = $taxValue;
                    }

                    $totalPayablePrice += $priceAfterDiscount + $priceAfterDiscount * $taxValue * 0.01;
                    $totalDiscount += $priceWithoutTax - $priceAfterDiscount;
                    $totalTax += $priceAfterDiscount * $taxValue * 0.01;

                    $items[$key]['is_ready'] = true;
                    $items[$key]['products'] = [];
                    $categoryForKitchenMeal = false;
                    foreach ($item['products'] as $key1 => $eachProduct) {
                        $items[$key]['products'][$key1] = $eachProduct;
                        $items[$key]['products'][$key1]['randomKey'] = (100 + $key * 100) + ($key1 + 1);
                        $items[$key]['products'][$key1]['modifiers'] = $eachProduct['modifiers'] ?? [];
                        $product1 = FoodItem::where('id', $eachProduct['id'])->first();
                        $items[$key]['products'][$key1]['is_ready'] = true;
                        if ($product1->category?->category_for_kitchen) {
                            $categoryForKitchen = true;
                            $categoryForKitchenMeal = true;
                            $items[$key]['is_ready'] = false;
                            $items[$key]['products'][$key1]['is_ready'] = false;
                        }
                        $items[$key]['products'][$key1]['id-from-db'] = $product1['id'];
                        $items[$key]['products'][$key1]['category_for_kitchen'] = $product1->category?->category_for_kitchen;
                        $items[$key]['products'][$key1]['name-from-db'] = $product1['name'];
                        $items[$key]['products'][$key1]['location_id'] = $product1['location_id'];
                        $items[$key]['products'][$key1]['status'] = $product1['status'];
                        $items[$key]['products'][$key1]['price-from-db'] = $product1['price'];
                        $items[$key]['products'][$key1]['image'] = $product1['image'];
                        $items[$key]['products'][$key1]['food_category_id'] = $product1['food_category_id'];
                        $items[$key]['products'][$key1]['category_name'] = $product1->category->name;
                        $items[$key]['products'][$key1]['sku'] = $product1['sku'];
                        $items[$key]['products'][$key1]['description'] = $product1['description'];
                        $items[$key]['products'][$key1]['created_at'] = $product1['created_at']->toString();
                        $items[$key]['products'][$key1]['updated_at'] = $product1['updated_at']->toString();
                        $sizeObject = $items[$key]['products'][$key1]['size'] ?? $sizes;

                        $items[$key]['products'][$key1]['size'] = $sizeObject;

                        foreach($sizeObject as $k => $value) {
                            if ($value !== null) {
                                $items[$key]['products'][$key1]['name'] = $product1->name. ', ' . $sizeKeys[$k];
                                break;
                            }
                        }
                    }
                    $items[$key]['category_for_kitchen'] = $categoryForKitchenMeal;
                }
            }
            foreach ($taxSum as $keyTax => $finalizationTax) {
                $taxSum[$keyTax]['tax_amount'] = round($finalizationTax['tax_amount'], 2);
            }
            if (!isset($data['order_id']) || $saveOrder?->z_report_id == null) {
                $zReport = ZReport::where([
                    'cash_register_id' => session()->get('cash_register'),
                    'end_z_report' => null
                ])->latest('created_at')->first();

                $staticData = [
                    'order_taker_id' => $user->name,
                    'z_report' => [
                        'start_z_report' => $zReport->start_z_report,
                        'saldo' => $zReport->saldo,
                        'cash_register_name' => $zReport->cashRegister?->name,
                        'location' => $zReport->location?->name
                    ],
                    'payment_method' => isset($data['payment_method_id']) && $data['payment_method_id'] != 0 ? $data['payment_method_id']['type'] : null,
                ];
            }
            $kitchenStatus = $this->currentLocation()->kitchen && $categoryForKitchen;
            if (isset($data['order_id'])) {
                $saveOrder->type = count($type) == 2 ? 'mixed' : $type[0];
                $saveOrder->items = $items;
                $saveOrder->tax = $tax;
                $saveOrder->sum_taxes = $taxSum;
                if ($saveOrder->z_report_id == null) {
                    $saveOrder->z_report_id = $zReport->id;
                    if ($data['table_id']) {
                        $saveOrder->table_id = $data['table_id'];
                    }
                }
                $saveOrder->tax_amount = $totalTax;
                $saveOrder->cart_total_items = count($data['items']);
                $saveOrder->cart_total_price = $data['cart_total_price']; //price with tax and without discount
                $saveOrder->cart_total_cost = $calculateCost['cost'];
                $saveOrder->profit_after_all = ($totalPayablePrice - $totalTax) - $calculateCost['cost']; //(price - discount) - cost
                $saveOrder->payable_after_all = $totalPayablePrice; //($data['cart_total_price'] - $discountAmount); //how much the customer pays, including discounts and taxes
                $saveOrder->discount_rate = $data['discount_amount']; // percentage or fixed (value)
                $saveOrder->discount_amount = $totalDiscount; // total discount after calculate if value is in percentage
                $saveOrder->is_preparing = !$kitchenStatus ? 1 : 0;
                $saveOrder->prepared_at = !$kitchenStatus ? now() : null;
                $saveOrder->progress = !$kitchenStatus ? 100 : 0;  
			  if ($saveOrder->pos_or_kiosk == 'e_kiosk' && !$saveOrder->order_taker_id){
                    $saveOrder->order_taker_id = $user->id;
                    if (isset($data['save_order'])) {
                        $saveOrder->save_order = true;
                        $saveOrder->order_type = 'dine_in';
                        $saveOrder->locator = null;
                        $bookTable = ServiceTable::find($data['table_id']);
                        if (!$bookTable) {
                            DB::rollBack();
                            return $this->response(1, __("The table which you selected, doesn't exist!"));
                        }
                        $bookTable->is_booked = true;
                        $bookTable->order_id = $saveOrder->id;
                        $bookTable->update();
                    }
                }
                $saveOrder->is_discount_in_percentage = $data['is_discount_in_percentage'] == "true";
                if (!isset($data['save_order'])) {
                    $idOrder = $saveOrder->id;
                    $saveOrder->save_order = false;
                    $saveOrder->paid_cash = $data['paid_cash'];
                    $saveOrder->paid_bank = $data['paid_bank'];
                    $saveOrder->payment_return = $data['payment_return'];
                    $saveOrder->order_type = $data['order_type'];
                    $saveOrder->is_paid = true;
                    $saveOrder->biller_id = $user->id;
                    $saveOrder->order_receipt = $orderReceipt?->order_receipt + 1;
                    $saveOrder->payment_data = isset($data['payment_data']) ? $data['payment_data'] : null;
                    $saveOrder->payment_status = isset($data['payment_status']) ? $data['payment_status'] : false;
                    $saveOrder->payment_method_id = isset($data['payment_method_id']) && $data['payment_method_id'] != 0 ? $data['payment_method_id']['id'] : null;
                    $saveOrder->payment_method_type = isset($data['payment_method_id']) && $data['payment_method_id'] != 0 ? $data['payment_method_id']['type'] : null;
                    $zReport1 = ZReport::find($saveOrder->z_report_id);
                    $zReport1->total_sales += 1;
                    $zReport1->total_balance_with_cash += ($data['paid_cash'] * 1 - $data['payment_return'] * 1);
                    $zReport1->total_balance_with_card += $data['paid_bank'] * 1;
                    $zReport1->update();
                    if ($data['order_type'] == 'dine_in' && $data['table_id'] != null && $saveOrder->z_report_id != null) {
                        $bookTable = ServiceTable::where(['id' => $data['table_id'], 'order_id' => $saveOrder->id])->first();
                        if ($bookTable) {
                            $bookTable->is_booked = false;
                            $bookTable->order_id = null;
                            $bookTable->update();
                        }
                    }
                    $this->removeQuantityFromIngredients($calculateCost['allIngredients']);
                }
                $saveOrder->update();
            } else {
                $idOrder = Uuid::uuid4()->toString();
                $orderNumber = Sale::withTrashed()->whereDate('created_at', \Carbon\Carbon::today())
                    ->orderBy('order_number', 'desc')
                    ->first();
                Sale::create([
                    'id' => $idOrder, // default id(uuid)
                    'type' => count($type) == 2 ? 'mixed' : $type[0], // product || deal || mixed
                    'order_number' => $orderNumber ? $orderNumber?->order_number + 1 : 1,
                    'order_receipt' => !isset($data['save_order']) ? ($orderReceipt?->order_receipt + 1) : null,
                    'save_order' => isset($data['save_order']),
                    'pos_or_kiosk' => 'pos',
                    'z_report_id' => $zReport->id,
                    'tracking' => rand(10000, 20000), // order number
                    'order_type' => $data['order_type'], // take_away || dine_in ||delivery
                    'items' => $items, // all products && deals in a static array
                    'tax' => $tax, // tax for dine_in, take_away || delivery
                    'sum_taxes' => $taxSum,
                    'took_at' => Carbon::now(),
                    'order_taker_id' => $user->id,
                    'is_preparing' => !$kitchenStatus ? 1 : 0,
                    'prepared_at' => !$kitchenStatus ? now() : null,
                    'e_kiosk_id' => isset($data['e_kiosk_id']) ? $data['e_kiosk_id'] : null,
                    'customer_id' => config('constants.role.customerId'),
                    'completed_at' => null /*!$kitchenStatus ? now() : null*/,
                    'progress' => !$kitchenStatus ? 100 : 0,
                    'chef_id' => null,
                    'locator' => $data['locator'] ?? null,
                    'payment_method_id' => isset($data['payment_method_id']) && $data['payment_method_id'] != 0 ? $data['payment_method_id']['id'] : null,
                    'payment_method_type' => isset($data['payment_method_id']) && $data['payment_method_id'] != 0 ? $data['payment_method_id']['type'] : null,
                    'biller_id' => isset($data['save_order']) ? null : $user->id,
                    'note_for_chef' => isset($data['kitchen_notes']) ? json_encode($data['kitchen_notes']) : null,
                    'tax_amount' => $totalTax,
                    'is_paid' => !isset($data['save_order']),
                    'is_manual_payment' => $data['is_manual_payment'],
                    'cart_total_items' => count($data['items']),
                    'cart_total_price' => $data['cart_total_price'], //price with tax and without discount
                    'cart_total_cost' => $calculateCost['cost'],
                    'profit_after_all' => ($totalPayablePrice - $totalTax) - $calculateCost['cost'], //(price - discount) - cost
                    'payable_after_all' => $totalPayablePrice, // ($data['paid_cash'] + $data['paid_bank']) - $data['payment_return'], //How much the customer pays, including discounts and taxes
                    'discount_rate' => $data['discount_amount'], // percentage or fixed (value)
                    'discount_amount' => $totalDiscount, // total discount after calculate if value is in percentage
                    'is_discount_in_percentage' => $data['is_discount_in_percentage'] == "true",
                    'table_id' => $data['table_id'] ?? null,
                    'static_data' => $staticData ?? null,
                    'payment_data' => isset($data['payment_data']) ? $data['payment_data'] : null,
                    'payment_status' => isset($data['payment_status']) ? $data['payment_status'] : false,
                    'cost_during_preparation' => 0,
                    'paid_cash' => isset($data['save_order']) ? 0 : $data['paid_cash'] * 1,
                    'paid_bank' => isset($data['save_order']) ? 0 : $data['paid_bank'] * 1,
                    'payment_return' => isset($data['save_order']) ? 0 : $data['payment_return'] * 1,
                ]);
                if ($data['order_type'] == 'dine_in' && $data['table_id'] != null && isset($data['save_order'])) {
                    $bookTable = ServiceTable::find($data['table_id']);
                    if (!$bookTable) {
                        DB::rollBack();
                        return $this->response(1, __("The table which you selected, doesn't exist!"));
                    }
                    $bookTable->is_booked = true;
                    $bookTable->order_id = $idOrder;
                    $bookTable->update();
                }

                $this->removeQuantityFromIngredients($calculateCost['allIngredients']);
                $zReport->total_sales += 1;
                $zReport->total_balance_with_cash += ($data['paid_cash'] * 1 - $data['payment_return'] * 1);
                $zReport->total_balance_with_card += $data['paid_bank'] * 1;
                $zReport->update();
            }
            $orderPrint = Sale::find($idOrder);
            $printString = "";

            app()->setLocale(env('DEFAULT_LANGUAGE', 'fr'));
            if ($orderPrint?->is_paid) {
                $inject = new \App\Services\SaleInvoice($orderPrint);
                $printData = $inject->getDataForPrinter();
                $printString = $inject->prepareString($printData);
            }
            app()->setLocale(session()->get('locale') ?? 'fr');
            DB::commit();
            return $this->response('0', __('The order has been saved successfully!'), null, null, $printString);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->response(1, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $order)
    {
        $orderId = (int)$order->order_receipt;
        $barcode = new DNS1D();
        $barcodeImage = $barcode->getBarcodePNG((string)$orderId, 'C128', 1, 25);
        $printString = '';
        if ($order?->is_paid) {
            app()->setLocale(env('DEFAULT_LANGUAGE', 'fr'));
            $inject = new \App\Services\SaleInvoice($order);
            $printData = $inject->getDataForPrinter();
            $printString = $inject->prepareString($printData);
            app()->setLocale(session()->get('locale') ?? 'fr');
        }

        return view('orders/order-edit', compact('order', 'barcodeImage', 'printString'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $order)
    {
        return;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $order)
    {
        $order->delete();
        $bookTable = ServiceTable::where(['order_id' => $order->id])->first();
        if ($bookTable) {
            $bookTable->is_booked = false;
            $bookTable->order_id = null;
            $bookTable->update();
        }
//        $order->forceDelete();
        return response()->json(['success' => 'The record is trashed']);
    }

    /**
     * Remove selected resource from storage.
     */
    public function deleteSelectedItems(Request $request)
    {
        Sale::whereIn('id', $request->ids)->delete();
//        Sale::whereIn('id', $request->ids)->forceDelete();
        return response()->json(['success' => 'The records are trashed'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function restore(Sale $order)
    {
        $order->restore();
        return response()->json(['success' => 'The record is restored']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function forceDelete(Sale $order)
    {
        $order->forceDelete();
        return response()->json(['success' => 'The record is deleted']);
    }

    /**
     * @param $meal
     * @param array $items
     * @param int|string $key
     * @param mixed $item
     * @return array
     */
    private function getItems($meal, array $items, int|string $key): array
    {
        $items[$key]['id-from-db'] = $meal['id'];
        $items[$key]['original-name'] = $meal['name'];
        $items[$key]['location_id'] = $meal['location_id'];
        $items[$key]['status'] = $meal['status'];
        $items[$key]['price-from-db'] = $meal['price'];
        $items[$key]['image'] = $meal['image'];
        $items[$key]['food_category_id'] = $meal['food_category_id'];
        $items[$key]['category_name'] = $meal->category->name;
        $items[$key]['sku'] = $meal['sku'];
        $items[$key]['description'] = $meal['description'];
        $items[$key]['created_at'] = $meal['created_at']->toString();
        $items[$key]['updated_at'] = $meal['updated_at']->toString();
        return $items;
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

    public function getOrdersByAllFilters(Request $request)
    {
        $orders = $request->get('order');
        $columns = $request->get('columns');
        $dateFilter = $request->get('dateFilter');
        $search = $request->get('search')['value'];
        $query = Sale::query();

        if ($orders) {
            foreach ($orders as $filter) {
                $column = $columns[$filter['column']]['data'];
                $direction = strtolower($filter['dir']) === 'desc' ? 'desc' : 'asc';
                $query->orderBy($column, $direction);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('order_receipt', 'like', "%$search%")
                    ->orWhere('pos_or_kiosk', 'like', "%$search%")
                    ->orWhere('payable_after_all', 'like', "%$search%")
                    ->orWhere('payment_method_type', 'like', "%$search%");
            });
        }

        if ($dateFilter) {
            $dateString = $dateFilter;
            if (str_contains($dateString, 'to')) {
                [$startDate, $endDate] = explode(' to ', $dateString);
                $startDate = \Carbon\Carbon::parse($startDate)->startOfDay();
                $endDate = \Carbon\Carbon::parse($endDate)->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
            elseif (is_null($dateString)) {
                //
            } else {
                $date = \Carbon\Carbon::parse($dateString);
                $query->whereDate('created_at', $date);
            }
        }

        $start = $request->get('start', 0);
        $perPage = $request->get('length', 10);
        $recordsFiltered = $query->count();
        $sales = $query->skip($start)->take($perPage)->get();

        $newData = $sales->map(function ($order){
            $order->extraData = $order->extraData ?? new \stdClass();
            $order->extraData->id = $order->id;
            $order->extraData->order_url = route('order.show', ['order' => $order->id]);
            $order->extraData->order_receipt = (int)$order->order_receipt != 0 ? (int)$order->order_receipt : '';
            $order->extraData->order_number = $order->order_number;
            $order->extraData->pos_or_kiosk = __($order->pos_or_kiosk);
            $order->extraData->order_type = __(ucwords(str_replace('_', ' ', $order->order_type)));
            $order->extraData->payable_after_all = PriceHelper::formatPrice($order->payable_after_all, Setting::first());
            $order->extraData->payment_method_type = __($order->payment_method_type);
            $order->extraData->created_at = $order->created_at->format('d.m.Y');
            if ($order->is_cancelled) {
                $order->extraData->status = __('Refunded');
                $order->extraData->style = 'danger';
            } elseif ($order->completed_at != null) {
                $order->extraData->status = __('Completed');
                $order->extraData->style = 'success';
            } elseif ($order->chef_id != null) {
                $order->extraData->status = __('In Progress');
                $order->extraData->style = 'primary';
            } else {
                $order->extraData->status = __('Waiting');
                $order->extraData->style = 'warning';
            }
            $order->extraData->view = __('View');
            $order->extraData->delete = __('Delete');
            $order->extraData->actions = __('Actions');
            return $order->extraData;
        });

        $data = [
            'draw' => $request->get('draw'),
            'recordsTotal' => Sale::count(),
            'recordsFiltered' => $recordsFiltered,
            'data' => $newData
        ];
        return response()->json($data);
    }

    private function getTotalCostAndStockIngredients($data)
    {
        $allIngredients = [];
        foreach ($data['items'] as $item) {
            $ratio = [
                "Gram" => 1000,
                "Milliliter" => 1000
            ];
            if ($item['type'] == 'product') {
                $productDetails = FoodItem::where('id', $item['id'])->with('ingredients')->first();
                if ($productDetails) {
                    foreach ($productDetails->ingredients as $ingredient) {
                        if ($ingredient->pivot && in_array($ingredient['id'], $item['ingredients'])) {
                            if ($ingredient->pivot->unit == $ingredient->unit || $ingredient->unit == "Unit") {
                                $cost = $ingredient->cost * $ingredient->pivot->quantity * (int)$item['quantity'];
                            } else {
                                $cost = $ingredient->cost * $ingredient->pivot->quantity * (int)$item['quantity'] * (in_array($ingredient->unit, array_keys($ratio)) ? $ratio[$ingredient->unit] : 0.001);
                            }
                            $allIngredients[] = [
                                'product_id' => $productDetails->id,
                                "ingredient_id" => $ingredient->id,
                                "unit" => $ingredient->pivot->unit,
                                "unit_ingredient" => $ingredient->unit,
                                "quantity" => $ingredient->pivot->quantity * (int)$item['quantity'],
                                "cost" => $cost
                            ];
                        }
                    }
                }
                if (isset($item['modifiers']) && is_array($item['modifiers'])) {
                    foreach ($item['modifiers'] as $modifier) {
                        $modifierDetails = Modifier::where('id', $modifier['id'])->with('ingredients')->first();
                        if ($modifierDetails) {
                            foreach ($modifierDetails->ingredients as $ingredient) {
                                if ($ingredient->pivot->unit == $ingredient->unit || $ingredient->unit == "Unit") {
                                    $cost = $ingredient->cost * $ingredient->pivot->quantity * (int)$modifier['quantity'] * (int)$item['quantity'];
                                } else {
                                    $cost = $ingredient->cost * $ingredient->pivot->quantity * (int)$modifier['quantity'] * (int)$item['quantity'] * (in_array($ingredient->unit, array_keys($ratio)) ? $ratio[$ingredient->unit] : 0.001);
                                }
                                if ($ingredient->pivot) {
                                    $allIngredients[] = [
                                        'product_id' => $productDetails->id,
                                        "ingredient_id" => $ingredient->id,
                                        "unit" => $ingredient->pivot->unit,
                                        "unit_ingredient" => $ingredient->unit,
                                        "quantity" => $ingredient->pivot->quantity * (int)$modifier['quantity'] * (int)$item['quantity'],
                                        "cost" => $cost,
                                    ];
                                }
                            }
                        }
                    }
                }
            } elseif ($item['type'] == 'meal' || $item['type'] == 'deal') {
                foreach ($item['products'] as $eachProduct) {
                    $productDetails = FoodItem::where('id', $eachProduct['id'])->with('ingredients')->first();
                    if ($productDetails) {
                        foreach ($productDetails->ingredients as $ingredient) {
                            if ($ingredient->pivot && in_array($ingredient['id'], $eachProduct['ingredients'])) {
                                if ($ingredient->pivot->unit == $ingredient->unit || $ingredient->unit == "Unit") {
                                    $cost = $ingredient->cost * $ingredient->pivot->quantity * (int)$eachProduct['quantity'] * (int)$item['quantity'];
                                } else {
                                    $cost = $ingredient->cost * $ingredient->pivot->quantity * (int)$eachProduct['quantity'] * (int)$item['quantity'] * (in_array($ingredient->unit, array_keys($ratio)) ? $ratio[$ingredient->unit] : 0.001);
                                }
                                $allIngredients[] = [
                                    'deal_id' => $item['id'],
                                    "ingredient_id" => $ingredient->id,
                                    "unit_ingredient" => $ingredient->unit,
                                    "unit" => $ingredient->pivot->unit,
                                    "quantity" => $ingredient->pivot->quantity * (int)$eachProduct['quantity'] * $item['quantity'],
                                    "cost" => $cost
                                ];
                            }
                        }
                    }
                    if (isset($eachProduct['modifiers']) && is_array($eachProduct['modifiers'])) {
                        foreach ($eachProduct['modifiers'] as $modifier) {
                            $modifierDetails = Modifier::where('id', $modifier['id'])->with('ingredients')->first();
                            if ($modifierDetails) {
                                foreach ($modifierDetails->ingredients as $ingredient) {
                                    if ($ingredient->pivot) {
                                        if ($ingredient->pivot->unit == $ingredient->unit || $ingredient->unit == "Unit") {
                                            $cost = $ingredient->cost * $ingredient->pivot->quantity * (int)$eachProduct['quantity'] * (int)$item['quantity'];
                                        } else {
                                            $cost = $ingredient->cost * $ingredient->pivot->quantity * (int)$eachProduct['quantity'] * (int)$item['quantity'] * (in_array($ingredient->unit, array_keys($ratio)) ? $ratio[$ingredient->unit] : 0.001);
                                        }
                                        $allIngredients[] = [
                                            'deal_id' => $item['id'],
                                            "ingredient_id" => $ingredient->id,
                                            "unit" => $ingredient->pivot->unit,
                                            "unit_ingredient" => $ingredient->unit,
                                            "quantity" => $ingredient->pivot->quantity * (int)$modifier['quantity'] * (int)$eachProduct['quantity'] * $item['quantity'],
                                            "cost" => $cost
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $aggregatedCostsById = [];
        foreach ($allIngredients as $ingredient) {
            $idKey = isset($ingredient['deal_id']) ? 'deal_id' : 'product_id';
            $id = $ingredient[$idKey];
            if (!isset($aggregatedCostsById[$id])) {
                $aggregatedCostsById[$id] = 0;
            }
            $aggregatedCostsById[$id] += $ingredient['cost'];
        }
        $totalCost = array_reduce($allIngredients, function ($carry, $item) {
            $cost = isset($item['cost']) && is_numeric($item['cost']) ? $item['cost'] : 0;
            return $carry + $cost;
        }, 0);

        return [
            'cost' => $totalCost,
            'eachCost' => $aggregatedCostsById,
            'allIngredients' => $allIngredients
        ];
    }

    private function removeQuantityFromIngredients($allIngredients): void
    {
        $conversionRatios = [
            'Liter' => [
                'Milliliter' => 0.001
            ],
            'Milliliter' => [
                'Liter' => 1000,
            ],
            'Kilogram' => [
                'Gram' => 0.001,
            ],
            'Gram' => [
                'Kilogram' => 1000,
            ],
        ];
        $aggregatedQuantities = [];
        foreach ($allIngredients as $ingredient) {
            $ingredientId = $ingredient["ingredient_id"];
            $quantity = $ingredient["quantity"];
            $unit = $ingredient["unit"];
            $unitIngredient = $ingredient["unit_ingredient"];

            if ($unit !== $unitIngredient && isset($conversionRatios[$unitIngredient][$unit])) {
                $quantity *= $conversionRatios[$unitIngredient][$unit];
            }

            if (!isset($aggregatedQuantities[$ingredientId])) {
                $aggregatedQuantities[$ingredientId] = [
                    'quantity' => 0,
                    'unit' => $unitIngredient,
                ];
            }
            $aggregatedQuantities[$ingredientId]['quantity'] += $quantity;
        }
        foreach ($aggregatedQuantities as $ingredientId => $ing) {
            $removeQty = Ingredient::find($ingredientId);
            $removeQty->quantity -= $ing['quantity'];
            $removeQty->update();
        }
    }

}
