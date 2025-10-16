<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Tax;
use App\Models\ZReport;
use DateTime;

class XReportInvoice
{
    protected $zReport;
    protected $settings;
    protected $tax;

    public function __construct($zReport)
    {
        $this->zReport = $zReport;
        $this->settings = Setting::first();
        $this->tax = Tax::class;
    }

    public function getXReportDataForPrinter()
    {
        $number_of_decimals = 2;
        $taxes = [];
        $orders = $this->zReport->orders()->where('is_cancelled', false)->get();
        foreach ($orders as $order) {
            foreach ($order->sum_taxes as $eachTax){
                if(isset($taxes[$eachTax['tax_id'] . ' ' . $eachTax['tax_rate'] . ' %'])){
                    $taxes[$eachTax['tax_id'] . ' ' . $eachTax['tax_rate'] . ' %'] += $eachTax['tax_amount'];
                }else{
                    $taxes[$eachTax['tax_id'] . ' ' . $eachTax['tax_rate'] . ' %'] = $eachTax['tax_amount'];
                }
            }
        }

        return [
            'header' => [
                'image' => $this->settings->getClientImage(),
                'row_data' => [
                    $this->settings->app_name,
                    $this->settings->app_address,
                    __("TVA:") . " " . $this->settings->tva,
                    // __("Tel.") . " " . $this->settings->app_phone,
                    "space",
                    __('X Report'),
                    "space",
                    __('Report No.:') . " #" . (int)$this->zReport->report_number,
                    "space",
                    __('Cash Register ID:') . " " . $this->zReport->cashRegister->key,
                ],
            ],

            'totals' => [
                'row_data' => [
                    __('Opened') => is_string($this->zReport->start_z_report) ? (new DateTime($this->zReport->start_z_report))->format("d/m/Y - H:i A") : null,
                    __(' ') => 'by ' . $this->zReport->openUser->name,
                    "1" => "space",
                    "2" => "space",


                    __('Opening Amount') => number_format(floatval($this->zReport->saldo), $number_of_decimals, ".", ""),
                    __('Expected Drawer') => number_format(floatval($this->zReport->orders()->where(['is_cancelled' => false, 'is_paid' => true])->sum('paid_cash') - $this->zReport->orders()->where(['is_cancelled' => false, 'is_paid' => true])->sum('payment_return')), $number_of_decimals, ".", ""),
                    __('Short') => '0.00',
                    "3" => "space",
                    "4" => "space",

                    __('Cash Sales') => number_format(floatval($this->zReport->orders()->where(['is_cancelled' => false, 'is_paid' => true])->sum('paid_cash') - $this->zReport->orders()->where(['is_cancelled' => false, 'is_paid' => true])->sum('payment_return')), $number_of_decimals, ".", ""),
                    __('Cash Returns') => number_format(floatval($this->zReport->orders()->where('is_cancelled', true)->sum('paid_cash') - $this->zReport->orders()->where('is_cancelled', true)->sum('payment_return')), $number_of_decimals, '.', ''),
                    __('Card Returns') => number_format(floatval($this->zReport->orders()->where(['is_cancelled' => true])->sum('paid_bank') - $this->zReport->orders()->where(['is_cancelled' => true])->sum('payment_return')), $number_of_decimals, ".", ""),
                    "5" => "space",
                    "6" => "space",

                    __('Total Sales') => number_format(floatval($this->zReport->orders()->where(['is_cancelled' => false, 'is_paid' => true])->sum('payable_after_all')), $number_of_decimals, '.', ''),
                    __('Total Discounts') => '-'.number_format(floatval($this->zReport->orders()->where(['is_cancelled' => false, 'is_paid' => true])->sum('discount_amount')), $number_of_decimals, '.', ''),
                    __('Total Returns') => '-'.number_format(floatval($this->zReport->orders()->where(['is_cancelled' => true, 'is_paid' => true])->sum('payable_after_all')), $number_of_decimals, '.', ''),
                ],
            ],
            'sales_by_service' => [
                'row_data' => [
                    'row_data_header' => [
                        __('Sales by Service'),
                        __('Sales'),
                        __('Amount')
                    ],
                    'row_data_take_away' => [
                        __('Take away'),
                        count($this->zReport->orders()->where(['is_cancelled' => false, 'order_type' => 'take_away'])->get()),
                        number_format(floatval($this->zReport->orders()->where(['is_cancelled' => false, 'order_type' => 'take_away'])->sum('payable_after_all')), $number_of_decimals, '.', '')
                    ],
                    'row_data_dine_in' => [
                        __('Dine-in'),
                        count($this->zReport->orders()->where(['is_cancelled' => false, 'order_type' => 'dine_in'])->get()),
                        number_format(floatval($this->zReport->orders()->where(['is_cancelled' => false, 'order_type' => 'dine_in'])->sum('payable_after_all')), $number_of_decimals, '.', '')
                    ],
                    'row_data_total' => [
                        __('Total'),
                        count($this->zReport->orders()->where('is_cancelled', false)->get()),
                        number_format(floatval($this->zReport->orders()->where('is_cancelled', false)->sum('payable_after_all')), $number_of_decimals, '.', '')
                    ],
                ],
            ],

            'final' => [
                'row_data' => array_merge([
                    __('Net Sales') => number_format(floatval($this->zReport->orders()->where('is_cancelled', false)->sum('payable_after_all') - $this->zReport->orders()->where('is_cancelled', false)->sum('tax_amount')), $number_of_decimals, '.', ''),
                    __('Gift Card Issues') => '0.00',
                    __('Tips') => '0.00',
                    __('Total Tax') => number_format(floatval($this->zReport->orders()->where('is_cancelled', false)->sum('tax_amount')), $number_of_decimals, '.', ''),
                ],
                $taxes,
                [
//                    __('Tax ').$this->tax::where('type', "take_away")->first()->tax_id => number_format(floatval($this->zReport->orders()->where(['order_type' => 'take_away', 'is_cancelled' => false])->sum('tax_amount')), $number_of_decimals, '.', ''),
//                    __('Tax ').$this->tax::where('type', "dine_in")->first()->tax_id => number_format(floatval($this->zReport->orders()->where(['order_type' => 'dine_in', 'is_cancelled' => false])->sum('tax_amount')), $number_of_decimals, '.', ''),
                    "1" => "space",
                    "2" => "space",
                    __('Total Tendered') => number_format(floatval($this->zReport->orders()->where('is_cancelled', false)->sum('payable_after_all')), $number_of_decimals, '.', ''),
                    "3" => "space",
                    "4" => "space",
                    __('Gift Card (L)') => '0.00',
                    __('Cash') => number_format(floatval($this->zReport->orders()->where('is_cancelled', false)->sum('paid_cash') - $this->zReport->orders()->where('is_cancelled', false)->sum('payment_return')), $number_of_decimals, '.', ''),
                    __('Total Card') => number_format(floatval($this->zReport->orders()->where('is_cancelled', false)->sum('paid_bank')), $number_of_decimals, '.', ''),
                    __('Visa ') => number_format(floatval($this->zReport->orders()->where('is_cancelled', false)->sum('paid_bank')), $number_of_decimals, '.', ''),
//                    "5" => "space",
//                    "6" => "space",
//                    __('Voided Items ') => '0',
//                    __('Voided Total ') => '0.00',
                    "7" => "space",
                    "8" => "space",
                    __('Discarded Items ') => (string)count($this->zReport->orders()->where(['is_cancelled' => true])->get()),
                    __('Discarded Total ') => number_format(($this->zReport->orders()->where(['is_cancelled' => true])->sum('payable_after_all')), $number_of_decimals, ".", ""),
                    "10" => "space",
                    "11" => "space",
                    __('Open Sales') => '0',
                    __('Open sales Total') => '0.00',
                    "12" => "space",
                    "13" => "space",
                    __('Printed On') => now()->format("d/m/Y - H:i A"),
                ])
            ]
        ];
    }

    public function prepareString($printData)
    {
        $printString = "";

        $header = $printData['header'];
        // $printString .= "LOGO:{$header['image']};";
        // $printString .= "LR:2;";
        foreach($header['row_data'] as $row) {
            if ($row == null) continue;
            if ($row == 'space') {
                $printString .= "LR:1;";
                continue;
            }

            $printString .= "CL:{$row};";
        }


        $printString .= "LR:2;";
        $totals = $printData['totals'];
        foreach($totals['row_data'] as $left => $right) {
            if ($right == null) continue;
            if ($right == 'space') {
                $printString .= "LR:1;";
                continue;
            }

            $printString .= "R:;LRL:{$left};RRL:{$right};";
        }


        // Sales by services
        $printString .= "LR:2;";
        $sales_by_service = $printData['sales_by_service'];
        foreach($sales_by_service['row_data'] as $sales_key => $sales_data) {
            if ($sales_key == 'space') {
                $printString .= "LR:1;";
                continue;
            }

            $row = "R:;";
            // First part is left alone


            if ($sales_key == 'row_data_total') {
                $row .= "B:;";
            }

            $row .= "LRL:{$sales_data[0]};";

            if ($sales_key == 'row_data_total') {
                $row .= "UB:;";
            }


            // Second part is divided into 15 characters right side and whatever is left on the left + 1 for space in between
            $full = $sales_data[1];
            $right_side = str_pad($sales_data[2], 15, " ", STR_PAD_LEFT);

            $full .= $right_side;

            $row .= "RRL:{$full};";


            if ($sales_key == 'row_data_header') {
                $printString .= "B:;";
            }

            $printString .= $row;

            if ($sales_key == 'row_data_header') {
                $printString .= "UB:;";
            }
        }

        $printString .= "LR:2;";
        $finals = $printData['final'];
        foreach($finals['row_data'] as $left => $right) {
            if ($right == null) continue;

            if ($right == 'space') {
                $printString .= "LR:1;";
                continue;
            }

            $printString .= "R:;LRL:{$left};RRL:{$right};";
        }
        return $printString;
    }
}
