<?php

namespace App\Services;

use App\Models\InvoicePrinting;
use App\Models\Sale;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;


class OrderRefundInvoice
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
        $totalTaxes = [];
        foreach ($this->sale->sum_taxes as $sumTax){
            $totalTaxes[$sumTax['tax_id'] . ' '. $sumTax['tax_rate'] . ' %'] = number_format(floatval($sumTax['tax_amount']), 2, ".", "");
        }
        $cashRegisterId = [__("Cash Register:"), $this->sale->zReport?->cashRegister->key];
        $companyData = [
            $this->settings->web,
            $this->settings->app_phone,
            __('Instagram') . " " . $this->settings->instagram,
        ];
        $title = __('REFUND RECEIPT');
        $messageOrderReceipt = [];
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
            }
        }

        $now = now();
        $paymentData = [];
        // Old version = 0, New version = 1
        $paymentDataType = 0;
        $paymentDataLeft = [];
        $paymentDataCentre = [];
        $paymentDataFooter = [];
        if ($this->sale->payment_status){
            $data = $this->sale->payment_data_canceled;
            $paymentDataCentre = [
                "space",
                "space",
                __("Cashless transaction information"),
                "===========================================",
                "space",
                $data['title'],
            ];

            if (array_key_exists('final_customer', $data)) {
                $parts = json_decode($data['final_customer'], true);
                $paymentData = $parts;
                $paymentDataType = 1;
            } else {
                $paymentDataLeft = [
                    $data['type'],
                    $data['card_type'],
                    $data['card_number']
                ];
                $seqCntKey = ""; $seqCntValue = ""; $totalKey =""; $totalValue = ""; $dateKey = ""; $dateValue = ""; $trmIdKey = ""; $trmIdValue = ""; $aidKey = ""; $aidValue = ""; $acqIdKey = ""; $acqIdValue = "";

                if (preg_match('/^(.+?):?\s+(.+?)$/', $data['date'], $matches)) {
                    $dateKey = trim($matches[1]) . ":";
                    $dateValue = trim($matches[2]);
                }
                if (preg_match('/^(.+?):?\s+(.+?)$/', $data['trm_id'], $matches)) {
                    $trmIdKey = trim($matches[1]) . ":";
                    $trmIdValue = trim($matches[2]);
                }
                if (preg_match('/^(.+?):?\s+(.+?)$/', $data['aid'], $matches)) {
                    $aidKey = trim($matches[1]) . ":";
                    $aidValue = trim($matches[2]);
                }
                if (preg_match('/^(.+?):?\s+(.+?)$/', $data['acq_id'], $matches)) {
                    $acqIdKey = trim($matches[1]) . ":";
                    $acqIdValue = trim($matches[2]);
                }
                if (preg_match('/^(.+?):\s+(.+)$/', $data['seq_cnt'], $matches)) {
                    $seqCntKey = trim($matches[1]) . ":";
                    $seqCntValue = trim($matches[2]);
                }
                if (preg_match('/^(.+?):\s+(.+)$/', $data['total'], $matches)) {
                    $totalKey = trim($matches[1]) . ":";
                    $totalValue = trim($matches[2]);
                }
                $paymentData = [
                    $dateKey => $dateValue,
                    $trmIdKey => $trmIdValue,
                    $aidKey => $aidValue,
                    $seqCntKey => $seqCntValue,
                    $acqIdKey => $acqIdValue,
                    $totalKey => $totalValue
                ];
            }
            $paymentDataFooter = [
                "space",
                "===========================================",
            ];
        }
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
                'row_data' => [
                    $cashRegisterId[0] => $cashRegisterId[1],
                    __("Customer Name:") => $this->sale->customer->name,
                    __("Order Id:") => '#'.(int)$this->sale->order_number,
                    __("Receipt:") => '#'.(int)$this->sale->order_receipt,


                ]
            ],
            'items' => [
                'total' => [
                    __('REFUND TOTAL').' '. $this->settings->currency_symbol,
                    number_format(floatval($this->sale->payable_after_all), 2, ".", "")
                ]

            ],
            'payment_method' => [
                'row_centre_terminal_data' => $paymentDataCentre,
                'row_left_terminal_data' => $paymentDataLeft,
                'row_terminal_data_version' => $paymentDataType,
                'row_terminal_data' => $paymentData,
                'row_footer_terminal_data' => $paymentDataFooter
            ],
            'footer' => [
                'row_data' => array_merge([
                    $now->format('d/m/Y - H:i A'),
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

        $printString .= "LR:4;";

        $receipt = $printData['receipt'];
        $printString .= "B:;FS:28;CL:{$receipt['title']};";
        $printString .= "LR:2;";

        $printString .= "LR:4;FS:{$default_font_size};";
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

        $printString .= "B:;LR:1;FS:35;";
        $printString .= "R:;LRL:{$items['total'][0]};RRL:{$items['total'][1]};";
        $printString .= "UB:;FS:{$default_font_size};";

        $printString .= "LR:2;";

        $payment_method = $printData['payment_method'];

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
