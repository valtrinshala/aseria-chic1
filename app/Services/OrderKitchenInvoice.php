<?php

namespace App\Services;


use App\Models\FoodItem;

class OrderKitchenInvoice
{
    protected $sale;

    public function __construct($sale)
    {
        $this->sale = $sale;
    }

    public function getItemDataForPrinter()
    {
        if ($this->sale->e_kiosk_id){
            $cashRegisterId = [__("E-kiosk name:") => $this->sale->eKiosk?->name];
        }else{
            $cashRegisterId = [__("Cash Register:") => $this->sale->zReport?->cashRegister->key];
        }
        $items = $this->sale['items'];
        $object = [
            'header' => [
                __('Kitchen')
            ],
            'totals' => [
                'row_data' => array_merge([
                    __('Order No:') => (int)$this->sale->order_number,
                    __('Type:') => $this->sale->order_type,
                    __('Locator Id:') => $this->sale->locator,
                    "1" => "space",
                ],
                    $cashRegisterId,
                    [
                        __('Customer Name:') => $this->sale->customer->name,
                    ]
                ),
            ],
            'items' => [],
            'footer' => [
                'row_data' => [
                    substr(auth()->user()->name, 0, 15).', '.now()->format('d/m/Y - H:i A')
                ]
            ]
        ];
        $object['items']['row_data'][] = [__("Qty / Item"), "space"];
        foreach ($items as $item){
            if (!$item['category_for_kitchen']) continue;
            $item_el[] = ['QTY, name'];
            if ($item['type'] == 'product') {
                $object['items']['row_data'][] = $this->renderProducts($item, 0);
            } else {
                $item_el[] = [
                    $item['quantity'] . 'x ' . $item['name'],
                    "space"
                ];
                foreach ($item['products'] as $product) {
                    $item_el[] = $this->renderProducts($product, 1);
                }
                $object['items']['row_data'] = $item_el;
            }
        }
        return $object;
    }

    /*
     * @param type: [0, 1] where 0 = Product, 1 = Meal/Deal
     */
    private function renderProducts($item, $type = 0)
    {
        $modifiers = "";
        foreach ($item['modifiers'] as $modifier) {
            $modifiers .= $modifier['quantity'] . 'x ' . $modifier['name'] . ', ';
        }
        $ingredients = FoodItem::find($item['id'])->ingredients()->whereNotIn('ingredients.id', $item['ingredients'])->get();
        $noIngredients = '';
        foreach ($ingredients as $ingredient) {
            $noIngredients .= $ingredient->name . ', ';
        }
        $noIngredients = rtrim($noIngredients, ', ');
        $modifiers = rtrim($modifiers, ', ');
        $addNo = [];
        if ($modifiers){
            $addNo[] = __('Add') . ': ' . $modifiers;
        }
        if ($noIngredients){
            $addNo[] = __('No') . ': ' . $noIngredients;
        }
        $object = array_merge([
            $item['quantity'] . 'x ' . $item['name']
        ],$addNo,[
            "space"
        ]);
        if ($type == 1) {
            if (count($object) > 0) {
                for ($i = 0; $i < count($object); $i++) {
                    if ($object[$i] == 'space') continue;
                    $object[$i] = '     ' . $object[$i];
                }
            }
        }
        return $object;
    }

    public function prepareString($printData)
    {
        $printString = "";

        // Currently we will be skipping the image
        $header = $printData['header'];
        $printString .= "B:;CL:{$header[0]};";


        $printString .= "LR:2;";
        $totals = $printData['totals'];
        foreach($totals['row_data'] as $left => $right) {
            if ($right == null) continue;
            if ($right == 'space') {
                $printString .= "LR:2;";
                continue;
            }

            $printString .= "R:;LRL:{$left};RRL:{$right};";
        }

        $printString .= "UB:;";

//----------------------------

        $items = $printData['items'];
        $printString .= "LR:4;";
        foreach ($items['row_data'] as $item) {
            foreach ($item as $itemEl) {
                if ($itemEl == 'space') {
                    $printString .= "LR:2;";
                    continue;
                }
                $printString .= "R:;LRL:{$itemEl};";
            }

//            $printString .= "R:;LRL:   {$item};";
        }

        $printString .= "LR:4;";
        $printString .= "LR:4;";

//-------------------------------

        $footer = $printData['footer'];

        foreach ($footer['row_data'] as $row) {
            if ($row == null) continue;

            if ($row == 'space') {
                $printString .= "LR:2;";
                continue;
            }

            $printString .= "CL:{$row};LR:2;";
        }
        return $printString;
    }

}
