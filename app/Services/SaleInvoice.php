<?php

namespace App\Services;

use App\Models\InvoicePrinting;
use App\Models\Sale;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;


class SaleInvoice
{
    protected $sale;
    protected $settings;
    protected $printInvoice;

    public function __construct($sale)
    {
        $this->sale = $sale;
        $this->settings = Setting::first();
        $this->printInvoice = InvoicePrinting::first();
    }

    public function getDataForPrinter()
    {
        $formattedItems = [];
        $countItems = 0;
        $totalTaxes = [];
        foreach ($this->sale->sum_taxes as $sumTax){
            $totalTaxes[$sumTax['tax_id'] . ' '. $sumTax['tax_rate'] . ' %'] = number_format(floatval($sumTax['tax_amount']), 2, ".", "");
        }

        foreach ($this->sale->items as $item) {
            $formattedItems[] = [
                'QTY' => $item['quantity'],
                'name' => $item['name'],
                'price' => number_format(floatval($item['price_per']) * floatval($item['quantity']), 2, ".", ""),
                'tax_id' => $item['tax_id'],
            ];
            $countItems++;
            if ($item['type'] == 'deal' || $item['type'] == 'meal') {
                foreach ($item['products'] as $product){
                    $sizePrice = 0.00;
                    foreach ($product['size'] as $size) {
                        if ($size !== null){
                            $sizePrice = floatval($size);
                        }
                    }
                    $formattedItems[] = [
                        'quantity' => $product['quantity'] * $item['quantity'],
                        'name' => $product['name'],
                        'price' => $sizePrice,
                    ];
                    $countItems++;
                    if ($product['modifiers']){
                        foreach ($product['modifiers'] as $modifier){
                            $modifier['quantity'] = $modifier['quantity'] * $item['quantity'];
                            $formattedItems[] = $modifier;
                            $countItems++;
                        }
                    }
                }
            }else{
                if ($item['modifiers']){
                    foreach ($item['modifiers'] as $modifier){
                        $modifier['quantity'] = $modifier['quantity'] * $item['quantity'];
                        $formattedItems[] = $modifier;
                        $countItems++;
                    }
                }
            }
        }
        $cashRegisterId = [__("Cash Register:"), $this->sale->zReport?->cashRegister->key];
        $discount = [];
        if ((double)$this->sale->discount_amount != 0){
            $discount = [__('Discounts') => number_format(floatval($this->sale->discount_amount), 2, ".", "")];
        }
        $companyData = [
            "space",
            $this->settings->web,
            $this->settings->app_phone,
            __('Instagram') . " " . $this->settings->instagram,
            __('Wifi-password') . " " . $this->settings->wifi_password,
        ];
        $title = __('SALES RECEIPT');
        $messageOrderReceipt = [];
        $receipt = [__('Receipt') . " #" . (int)$this->sale->order_receipt];
        if ($this->sale->e_kiosk_id){
            $cashRegisterId = [__("E-kiosk name:"), $this->sale->eKiosk?->name];
            if (!$this->sale->is_paid){
                $title = __('ORDER RECEIPT');
                $companyData = [];
                $messageOrderReceipt = [
                    "space",
                    __('YOUR ORDER HAS NOT BEEN PAID OR '),
                    __('PROCESSED YET!'),
                    __('Please continue to the next available Cashier, to'),
                    __('finish payment and to receive your fiscal receipt.'),
                    "space",
                    __('This is not a fiscal receipt.')
                ];
                $receipt = [];
            }
        }

        $now = now();
        $paymentData = [];
        // Old version = 0, New version = 1
        $paymentDataType = 0;
        $paymentDataLeft = [];
        $paymentDataCentre = [];
        $paymentDataFooter = [];
        if ($this->sale->payment_status && !$this->sale->is_manual_payment) {
            $data = $this->sale->payment_data;
            $paymentDataCentre = [
                "space",
                "space",
                __("Cashless transaction information"),
                "===========================================",
                "space",
            ];

            if (array_key_exists('final_customer', $data)) {
                $parts = json_decode($data['final_customer'], true);
                $paymentData = $parts;
                $paymentDataType = 1;
            } else {
                $paymentDataCentre[] = $data['title'];
                $paymentDataLeft = [
                    $data['type'],
                    $data['card_type'],
                    $data['card_number']
                ];
                $seqCntKey = ""; $seqCntValue = ""; $totalKey =""; $totalValue = ""; $dateKey = ""; $dateValue = ""; $trmIdKey = ""; $trmIdValue = ""; $aidKey = ""; $aidValue = ""; $acqIdKey = ""; $acqIdValue = ""; $refNoKey = ""; $refNoValue = ""; $authCodeKey = ""; $authCodeValue = "";
                if (isset($data['date']) && preg_match('/^(.+?):?\s+(.+?)$/', $data['date'], $matches)) {
                    $dateKey = trim($matches[1]) . ":";
                    $dateValue = trim($matches[2]);
                }
                if (isset($data['trm_id']) && preg_match('/^(.+?):?\s+(.+?)$/', $data['trm_id'], $matches)) {
                    $trmIdKey = trim($matches[1]) . ":";
                    $trmIdValue = trim($matches[2]);
                }
                if (isset($data['aid']) && preg_match('/^(.+?):?\s+(.+?)$/', $data['aid'], $matches)) {
                    $aidKey = trim($matches[1]) . ":";
                    $aidValue = trim($matches[2]);
                }
                if (isset($data['acq_id']) && preg_match('/^(.+?):?\s+(.+?)$/', $data['acq_id'], $matches)) {
                    $acqIdKey = trim($matches[1]) . ":";
                    $acqIdValue = trim($matches[2]);
                }
                if (isset($data['seq_cnt']) && preg_match('/^(.+?):\s+(.+)$/', $data['seq_cnt'], $matches)) {
                    $seqCntKey = trim($matches[1]) . ":";
                    $seqCntValue = trim($matches[2]);
                }
                if (isset($data['ref_no']) && preg_match('/^(.+?):\s+(.+)$/', $data['ref_no'], $matches)) {
                    $refNoKey = trim($matches[1]) . ":";
                    $refNoValue = trim($matches[2]);
                }
                if (isset($data['auth_code']) && preg_match('/^(.+?):\s+(.+)$/', $data['auth_code'], $matches)) {
                    $authCodeKey = trim($matches[1]) . ":";
                    $authCodeValue = trim($matches[2]);
                }
                if (isset($data['total']) && preg_match('/^(.+?):\s+(.+)$/', $data['total'], $matches)) {
                    $totalKey = trim($matches[1]) . ":";
                    $totalValue = trim($matches[2]);
                }
                $paymentData = [
                    $dateKey => $dateValue,
                    $trmIdKey => $trmIdValue,
                    $aidKey => $aidValue,
                    $seqCntKey => $seqCntValue,
                    $refNoKey => $refNoValue,
                    $authCodeKey => $authCodeValue,
                    $acqIdKey => $acqIdValue,
                    $totalKey => $totalValue
                ];
            }

            $paymentDataFooter = [
                "space",
                "===========================================",
            ];
        }

        /* Old version
        $now = now();
        $paymentData = [];
        $paymentDataLeft = [];
        $paymentDataCentre = [];
        $paymentDataFooter = [];
        if ($this->sale->payment_status){
            $data = $this->sale->payment_data;
            $paymentDataCentre = [
                "space",
                "space",
                __("Cashless transaction information"),
                "===========================================",
                $data['title'],
            ];
            $paymentDataLeft = [
                $data['type'],
                $data['card_type'],
                $data['card_number']
            ];
            $seqCntKey = ""; $seqCntValue = ""; $totalKey =""; $totalValue = ""; $dateKey = ""; $dateValue = ""; $trmIdKey = ""; $trmIdValue = ""; $aidKey = ""; $aidValue = ""; $acqIdKey = ""; $acqIdValue = ""; $refNoKey = ""; $refNoValue = ""; $authCodeKey = ""; $authCodeValue = "";
            if (isset($data['date']) && preg_match('/^(.+?):?\s+(.+?)$/', $data['date'], $matches)) {
                $dateKey = trim($matches[1]) . ":";
                $dateValue = trim($matches[2]);
            }
            if (isset($data['trm_id']) && preg_match('/^(.+?):?\s+(.+?)$/', $data['trm_id'], $matches)) {
                $trmIdKey = trim($matches[1]) . ":";
                $trmIdValue = trim($matches[2]);
            }
            if (isset($data['aid']) && preg_match('/^(.+?):?\s+(.+?)$/', $data['aid'], $matches)) {
                $aidKey = trim($matches[1]) . ":";
                $aidValue = trim($matches[2]);
            }
            if (isset($data['acq_id']) && preg_match('/^(.+?):?\s+(.+?)$/', $data['acq_id'], $matches)) {
                $acqIdKey = trim($matches[1]) . ":";
                $acqIdValue = trim($matches[2]);
            }
            if (isset($data['seq_cnt']) && preg_match('/^(.+?):\s+(.+)$/', $data['seq_cnt'], $matches)) {
                $seqCntKey = trim($matches[1]) . ":";
                $seqCntValue = trim($matches[2]);
            }
            if (isset($data['ref_no']) && preg_match('/^(.+?):\s+(.+)$/', $data['ref_no'], $matches)) {
                $refNoKey = trim($matches[1]) . ":";
                $refNoValue = trim($matches[2]);
            }
            if (isset($data['auth_code']) && preg_match('/^(.+?):\s+(.+)$/', $data['auth_code'], $matches)) {
                $authCodeKey = trim($matches[1]) . ":";
                $authCodeValue = trim($matches[2]);
            }
            if (isset($data['total']) && preg_match('/^(.+?):\s+(.+)$/', $data['total'], $matches)) {
                $totalKey = trim($matches[1]) . ":";
                $totalValue = trim($matches[2]);
            }
            $paymentData = [
                $dateKey => $dateValue,
                $trmIdKey => $trmIdValue,
                $aidKey => $aidValue,
                $seqCntKey => $seqCntValue,
                $refNoKey => $refNoValue,
                $authCodeKey => $authCodeValue,
                $acqIdKey => $acqIdValue,
                $totalKey => $totalValue
            ];
            $paymentDataFooter = [
                "space",
                "space",
                "===========================================",
            ];
        }
         */

        return [
            'tax_id' => $this->sale->tax['tax_id'],
            'header' => [
                'image' => Storage::disk('public')->url($this->printInvoice->logo_header),
                'row_data' => [
                    $this->settings->app_name,
                    $this->settings->app_address,
                    __("Tel.") . " " . $this->settings->app_phone,
                    'space',
                    __("TVA:") . " " . $this->settings->tva,
                ],
            ],
            'receipt' => [
                'title' => $title,
                'order_no' => [
                    __("Order No."),
                    $this->sale->order_number
                ],
                'row_data' => [
                    __("Type").':' => $this->sale->order_type,
                    __("Locator Id:") => $this->sale->locator,
                    "space" => "space",
                    $cashRegisterId[0] => $cashRegisterId[1],
                    __("Employee").':' => $this->sale?->taker?->name
                ]
            ],
            'items' => [
                'order_items' => $formattedItems,
                'row_data' => array_merge([
                    __('Items') => $countItems,
                    __('Subtotal (inc. VAT)') => number_format(floatval($this->sale->cart_total_price), 2, ".", "")
                ],
                $discount,
                [
                ]),
                'total' => [
                    __('TOTAL IN').' '. $this->settings->currency_symbol,
                    number_format(floatval($this->sale->payable_after_all), 2, ".", "")
                ]

            ],
            'payment_method' => [
                'row_data' => array_merge([
                    __('Payment method').' - '. __('Card') => $this->sale->paymentMethod->name == 'Card' || $this->sale->paymentMethod->name == "Mix" ? number_format(floatval($this->sale->paid_bank), 2, ".", "") : null,
                    __('Payment method').' - '. __('Cash') => $this->sale->paymentMethod->name == 'Cash' || $this->sale->paymentMethod->name == "Mix" ? number_format(floatval($this->sale->paid_cash), 2, ".", "") : null,
                    __('Change') => number_format(floatval($this->sale->payment_return), 2, ".", ""),
                    "space" => "space",
                    __('VAT Total') => number_format(floatval($this->sale->tax_amount), 2, ".", ""),
                    ], $totalTaxes, [
                    __('Total without VAT') => number_format(floatval($this->sale->payable_after_all - $this->sale->tax_amount), 2, ".", ""),
                ]),
                'row_centre_terminal_data' => $paymentDataCentre,
                'row_left_terminal_data' => $paymentDataLeft,
                'row_terminal_data_version' => $paymentDataType,
                'row_terminal_data' => $paymentData,
                'row_footer_terminal_data' => $paymentDataFooter
            ],
            'barcode' => (int)$this->sale->order_receipt,
            'footer' => [
                'row_data' => array_merge([
                    $now->format('d/m/Y - H:i A')],
                    $receipt,
                    [
                    __('Thank you for your visit!'), // 2
                    ],
                    $companyData,
                    $messageOrderReceipt
                )
            ]
        ];
    }

    public function prepareString($printData) {
        $printString = "";
        $default_font_size = 24;

        // Currently we will be skipping the image
        $header = $printData['header'];
        $printString .= "LOGO:{$header['image']};";
        $printString .= "LR:2;";
        foreach($header['row_data'] as $row) {
            if ($row == null) continue;
            if ($row == 'space') {
                $printString .= "LR:2;";
                continue;
            }

            $printString .= "CL:{$row};";
        }

        $printString .= "LR:2;";

        $receipt = $printData['receipt'];
        $printString .= "B:;FS:28;CL:{$receipt['title']};";
        $printString .= "LR:2;";

        $order_no_items = $receipt['order_no'];
        $printString .= "FS:{$default_font_size};CL:{$receipt['order_no'][0]};";
        $printString .= "LR:7;FS:100;CL:{$receipt['order_no'][1]};";

        $printString .= "LR:2;FS:{$default_font_size};";
        foreach($receipt['row_data'] as $left => $right) {
            if ($right == null) continue;

            if ($left == 'space' || $right == 'space') {
                $printString .= "LR:2;UB:;";
                continue;
            }

            $printString .= "R:;LRL:{$left};RRL:{$right};";
        }

        $printString .= "LR:4;";

        $items = $printData['items'];

        $order_items = $items['order_items'];
        // Sorry this is commented this way I honestly could not find a better solution considering I have approximately 4 hours

        // 10 spaces per PRICE and TOTAL and 1 space for C
        // The ITEM fills the space, there must be a limit soon but for now we do not know the limit we need to test to get it
        // QTY has 4 spaces

        // Between every item of the same side there must be a space (so 10 for total 10 for price and a space in between)
        $printString .= "R:;LRL:".__('QTY')." ".__('ITEM').";RRL:     ".__('PRICE')."      TOTAL  ;";
        foreach($order_items as $item) {
            $row = "R:;";

            $cSymbol = " ";
            $qty = "   ";
            $price = "          ";
            $total = "          ";

            $qtyVal = 0;
            $priceVal = 0;

            if (array_key_exists('QTY', $item)) {
                $qty = str_pad($item['QTY'] . 'x', 3, " ", STR_PAD_LEFT);
                $qtyVal = floatval($item['QTY']);
                $cSymbol = $item['tax_id'];
            }

            $name = $item['name'];

            if (array_key_exists('price', $item)) {
                $plusChar = $item['price'] != 0 ? "+" : "";
                $price = str_pad($plusChar.number_format(floatval($item['price']), 2, '.', ''), 10, " ", STR_PAD_LEFT);
                $priceVal = floatval($item['price']);
            }

            if (array_key_exists('price_per', $item)) {

                $qtyVal = floatval($item['quantity']);
                $price = str_pad(number_format(floatval($item['price_per'] * $qtyVal), 2, '.', ''), 10, " ", STR_PAD_LEFT);
                $priceVal = floatval($item['price_per']) * $qtyVal;
                $cSymbol = $printData['tax_id'];
                $totalQty = $item['quantity'];
                $name = "+{$totalQty} {$name}";
            } else {
                // This means that it has quantity however it's not a modifier, meaning it's a meal item
                if (array_key_exists('quantity', $item)) {
                    $name = "{$item['quantity']} {$name}";
                }
            }

            if ($cSymbol != " ") {
                // Item is elligble to get a total
                $total = str_pad(number_format(floatval($priceVal), 2, ".", ""), 10, " ", STR_PAD_LEFT);
            }

            $price_to_display = $price;
            if ($qtyVal != 0)
                $price_to_display = number_format($price / $qtyVal, 2, ".", "");

            // $row .= "LRL:{$qty} {$name};RRL:{$price} {$total} {$cSymbol};";
            $row .= "LRL:{$qty} {$name};RRL:{$price_to_display} {$total} {$cSymbol};";

            $printString .= $row;
        }

        $printString .= "LR:2;";
        foreach($items['row_data'] as $left => $right) {
            if ($right == null) continue;

            if ($left == 'space') {
                $printString .= "LR:2;";
                continue;
            }

            $printString .= "R:;LRL:{$left};RRL:{$right};";
        }

        $printString .= "B:;LR:1;FS:35;";
        $printString .= "R:;LRL:{$items['total'][0]};RRL:{$items['total'][1]};";
        $printString .= "UB:;FS:{$default_font_size};";

        $printString .= "LR:2;";

        $payment_method = $printData['payment_method'];

        foreach($payment_method['row_data'] as $left => $right) {
            if ($right == null) continue;

            if ($left == 'space') {
                $printString .= "LR:2;";
                continue;
            }

            $printString .= "R:;LRL:{$left};RRL:{$right};";
        }

        foreach($payment_method['row_centre_terminal_data'] as $item) {
            if ($item == 'space') {
                $printString .= "LR:2;";
                continue;
            }

            $printString .= "CL:{$item};";
        }

        foreach($payment_method['row_left_terminal_data'] as $item) {
            if ($item == 'space') {
                $printString .= "LR:2;";
                continue;
            }

            $printString .= "R:;LRL:{$item};";
        }

        $version = $payment_method['row_terminal_data_version'];
        if ($version == 1) {
            foreach($payment_method['row_terminal_data'] as $key => $value) {
                $text = $value['value'];
                $type = $value['type'];

                if ($type == 'newline') {
                    $printString .= "LR:2;";
                    continue;
                } else if ($type == 'center') {
                    $printString .= "CL:";
                } else if ($type == 'left') {
                    $printString .= 'R:;LRL:';
                } else if ($type == 'right') {
                    $printString .= 'R:;RRL:';
                } else if ($type == 'split_text') {
                    if (is_countable($text) && count($text) > 1) {
                        $printString .= "R:;LRL:{$text[0]};RRL:{$text[1]};";
                    }
                    continue;
                } else {
                    $printString .= 'CL:';
                }

                $printString .= $text . ';';
            }
        } else {
            foreach($payment_method['row_terminal_data'] as $left => $right) {
                if ($right == null) continue;

                if ($left == 'space') {
                    $printString .= "LR:2;";
                    continue;
                }

                $printString .= "R:;LRL:{$left};RRL:{$right};";
            }
        }

        foreach($payment_method['row_footer_terminal_data'] as $item) {
            if ($item == 'space') {
                $printString .= "LR:2;";
                continue;
            }

            $printString .= "CL:{$item};";
        }

        $printString .= "LR:4;";

        $barcode = $printData['barcode'];
        if ($barcode != null) {
            $printString .= "BARCODE:{$barcode};LR:2;";
        }

        $footer = $printData['footer'];

        foreach($footer['row_data'] as $index => $row) {
            if ($row == null) continue;

            if ($row == 'space') {
                $printString .= "LR:2;";
                continue;
            }

            if ($index == 2) {
                $printString .= "B:;FS:26;";
            }

            $printString .= "CL:{$row};LR:2;";

            if ($index == 2) {
                $printString .= "UB:;FS:{$default_font_size};";
            }
        }
        return $printString;
    }
}
