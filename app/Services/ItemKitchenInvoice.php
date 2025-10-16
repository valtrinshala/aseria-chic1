<?php

namespace App\Services;

use App\Models\FoodItem;
use App\Models\Setting;
use function Sodium\add;

class ItemKitchenInvoice
{
    protected $sale;
    protected $index1;
    protected $index2;
    protected $child;

    public function __construct($sale, $index1, $index2, $child)
    {
        $this->sale = $sale;
        $this->index1 = $index1;
        $this->index2 = $index2;
        $this->child = $child;
    }

    public function getItemDataForPrinter()
    {
        if (!$this->child){
            $item = $this->sale['items'][$this->index1];
        }else{
            $item = $this->sale['items'][$this->index1]['products'][$this->index2];
        }
        $object = [
            'header' => [
                // These are required attributes despite them being
                __('Order No') . ': ' . (int)$this->sale->order_number,
                ($this->sale->order_type == 'take_away' ? __('Take away') : __('Dine in')),
            ],
            'items' => [],
            'footer' => [
                'row_data' => [
                    now()->format('d/m/Y - H:i A')
                ]
            ]
        ];

        if ($this->sale->locator !== null) {
            $object['header'][] = __('Locator') . ': ' . $this->sale->locator;
        }
//        if ($item['type'] == 'product') {
            $object['items']['row_data'][] = $this->renderProducts($item, 0);
//        } else {
//            $items[] = [
//                $item['quantity'] . 'x ' . $item['name'],
//                "space"
//            ];
//            foreach ($item['products'] as $product) {
//                $items[] = $this->renderProducts($product, 1);
//            }
//            $object['items']['row_data'] = $items;
//        }
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
            $item['quantity'] . 'x ' . $item['name'], "space"
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
        $ct = count($header);
        $printString .= "LR:4;FS:50;B:;CL:{$header[0]};FS:35;LR:1;UB:;FL:;R:;RRL:{$header[1]};";

        if (count($header) > 2)
            $printString .= "LRL:{$header[2]};";
//        $printString .= "CL:{$header[1]};UB:;";

        $printString .= 'FL:;LR:1;';
//----------------------------

        $items = $printData['items'];
        $printString .= "LR:2;B:;";
        foreach ($items['row_data'] as $item) {
            foreach($item as $itemEl) {
                if ($itemEl == 'space') {
                    $printString .= "UB:;LR:1;FS:25;";
                    continue;
                }
                $printString .= "R:;LRF:100:0-{$itemEl};";
            }

//            $printString .= "R:;LRL:   {$item};";
        }

        $printString .= "LR:2;FL:;LR:2;";

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
