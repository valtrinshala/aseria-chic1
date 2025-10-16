<?php

namespace App\Services;

use App\Models\CashRegister;
use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\Tax;
use App\Models\ZReport;
use Carbon\Carbon;
use DateTime;
use function PHPUnit\Framework\isFalse;

class ZReportInvoicePerMonthService
{
    protected $cashRegisterId;
    protected $month;
    protected $year;
    protected $startDate;
    protected $endDate;
    protected $settings;
    protected $tax;

    public function __construct($cashRegisterId, $month, $year, $startDate, $endDate)
    {
        $this->cashRegisterId = $cashRegisterId;
        $this->month = $month;
        $this->year = $year;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->settings = Setting::first();
        $this->tax = Tax::class;
    }

    public function getZReportDataForPrinter()
    {
        $number_of_decimals = 2;
        $taxes = [];
        if ($this->startDate && $this->endDate) {
            $zRaport = ZReport::whereBetween('created_at', [$this->startDate, $this->endDate])
//                ->whereYear('created_at', $this->year)
                ->where('cash_register_id', $this->cashRegisterId);
        }elseif ($this->month && $this->year) {
            $zRaport = ZReport::whereYear('created_at', (int)$this->year)
                ->whereMonth('created_at', (int)$this->month)
                ->where('cash_register_id', $this->cashRegisterId);
        }
        $zReportIdsForMonth = $zRaport->get()->pluck('id')->toArray();
        $orders = Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->get();
        if ($this->startDate && $this->endDate){
            $start = $this->startDate;
            $end = $this->endDate;
        } elseif ($this->month) {
            $start = $zRaport->orderBy('created_at', 'asc')->get()->first()?->start_z_report;
            $end = $zRaport->orderBy('created_at', 'asc')->get()->last()?->start_z_report;
        }

        foreach ($orders as $order) {
            foreach ($order->sum_taxes as $eachTax){
                if(isset($taxes[$eachTax['tax_id'] . ' ' . $eachTax['tax_rate'] . ' %'])){
                    $taxes[$eachTax['tax_id'] . ' ' . $eachTax['tax_rate'] . ' %'] += $eachTax['tax_amount'];
                }else{
                    $taxes[$eachTax['tax_id'] . ' ' . $eachTax['tax_rate'] . ' %'] = $eachTax['tax_amount'];
                }
            }
        }
        $categories = [];
        $total = ['net_total' => 0, 'count_total' => 0];
        foreach ($orders as $order) {
            foreach ($order['items'] as $product) {
                $tax_adjusted_sub_total = $product['sub_total'];
                if (array_key_exists($product['food_category_id'], $categories)) {
                    $categories[$product['food_category_id']]['sales'] += 1;
                    $categories[$product['food_category_id']]['net_amount'] += $tax_adjusted_sub_total;
                } else {
                    $categories[$product['food_category_id']]['name'] = $product['category_name'] ?? null;
                    $categories[$product['food_category_id']]['sales'] = 1;
                    $categories[$product['food_category_id']]['net_amount'] = number_format(floatval($tax_adjusted_sub_total), $number_of_decimals, ".", "");
                }

                $total['net_total'] += $tax_adjusted_sub_total;
                $total['count_total'] += 1;
            }
        }
        $shortVal = null; //$this->zReport->closing_amount - (($this->zReport->orders()->where(['is_cancelled' => false])->sum('paid_cash') - $this->zReport->orders()->where(['is_cancelled' => false])->sum('payment_return')) + $this->zReport->saldo);
        $paymentMethodCard = PaymentMethod::where('id', config('constants.paymentMethod.paymentMethodCardId'))->first();
        $datePicker = $this->startDate && $this->endDate;

        if ($datePicker){
            $dateRange = "";
        }elseif($this->month){
            $dateRange = " #".now()::create()->month($this->month)->format('F');
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
                    __('Z Report'),
                    "space",
                    __('Report:') . $dateRange,
                    "space",
                    __('Cash Register ID:') . " " . CashRegister::find($this->cashRegisterId)->key,
                ],
            ],

            'totals' => [
                'row_data' => [
                    __('From') => Carbon::parse($start)->toDateString(),//is_string($this->zReport->start_z_report) ? (new DateTime($this->zReport->start_z_report))->format("d/m/Y - H:i A") : null,
                    "1" => "space",
                    __('To') => Carbon::parse($end)->toDateString(),//is_string($this->zReport->end_z_report) ? (new DateTime($this->zReport->end_z_report))->format("d/m/Y - H:i A") : null,
                    "2" => "space",
                    "3" => "space",

                    __('Opening Amount') => 0, //number_format(floatval($this->zReport->saldo), $number_of_decimals, ".", ""),
                    __('Closing Amount') => 0, //number_format(floatval($this->zReport->closing_amount), $number_of_decimals, ".", ""),
//                    $shortVal >= 0 ? __('Over') : __('Short') => number_format(floatval($shortVal), $number_of_decimals, ".", ""),
                    "4" => "space",
                    "5" => "space",

                    __('Cash Sales') => number_format(floatval(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->sum('paid_cash') - Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->sum('payment_return')), $number_of_decimals, ".", ""),
                    __('Cash Returns') => number_format(floatval(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', true)->sum('paid_cash') - Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', true)->sum('payment_return')), $number_of_decimals, ".", ""),
                    __('Card Returns') => number_format(floatval(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', true)->sum('paid_bank') - Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', true)->sum('payment_return')), $number_of_decimals, ".", ""),
                    "6" => "space",
                    "7" => "space",

                    __('Total Sales') => number_format(floatval(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->sum('payable_after_all')), $number_of_decimals, ".", ""),
                    __('Total Discounts') => '-'.number_format(floatval(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->sum('discount_amount')), $number_of_decimals, ".", ""),
                    __('Total Returns') => '-'.number_format(floatval(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', true)->sum('payable_after_all')), $number_of_decimals, ".", ""),
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
                        count(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->where('order_type', 'take_away')->get()),
                        number_format(floatval(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->where('order_type', 'take_away')->sum('payable_after_all')), $number_of_decimals, ".", "")
                    ],
                    'row_data_dine_in' => [
                        __('Dine-in'),
                        count(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->where('order_type', 'dine_in')->get()),
                        number_format(floatval(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->where('order_type', 'dine_in')->sum('payable_after_all')), $number_of_decimals, ".", "")
                    ],
                    "1" => "space",
                    'row_data_total' => [
                        __('Total'),
                        count(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->get()),
                        number_format(floatval(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->sum('payable_after_all')), $number_of_decimals, ".", "")
                    ],
                ],
            ],
            'sales_by_categories' => [
                'title' => __('Sales Categories'),
                'row_head' => [
                    __('Category'),
                    __('Sales'),
                    __('Total amount')
                ],
                'row_data' => $categories,
                'row_footer' => [
                    __('Total'),
                    $total['count_total'],
                    number_format(floatval($total['net_total']), $number_of_decimals, ".", ""),
                ],
            ],
            'final' => [
                'row_data' => array_merge([
                    __('Net Sales') => number_format(floatval(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->sum('payable_after_all') - Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->sum('tax_amount')), $number_of_decimals, ".", ""),
                    "1" => "space",
                    "2" => "space",

//                    __('Net Sales') => number_format(floatval(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->sum('payable_after_all') - Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->sum('tax_amount')), $number_of_decimals, ".", ""),
                    __('Gift Card Issues') => '0.00',
                    __('Tips') => '0.00',
                    __('Total Tax') => number_format(floatval(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->sum('tax_amount')), $number_of_decimals, ".", ""),
                ],
                    $taxes,
                    [
//                    $this->tax::where('type', "take_away")->first()?->name => number_format(floatval($this->zReport->orders()->where(['order_type' => 'take_away', 'is_cancelled' => false])->sum('tax_amount')), $number_of_decimals, ".", ""),
//                    $this->tax::where('type', "dine_in")->first()?->name => number_format(floatval($this->zReport->orders()->where(['order_type' => 'dine_in', 'is_cancelled' => false])->sum('tax_amount')), $number_of_decimals, ".", ""),
                        "3" => "space",
                        "4" => "space",

                        __('Total Tendered') => number_format(floatval(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->sum('payable_after_all')), $number_of_decimals, ".", ""),
                        "5" => "space",
                        "6" => "space",

                        __('Gift Card (L)') => '0.00',
                        __('Cash') => number_format(floatval(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->sum('paid_cash') - Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->sum('payment_return')), $number_of_decimals, ".", ""),
                        __('Total Card') => number_format(floatval(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->sum('paid_bank')), $number_of_decimals, ".", ""),
                        $paymentMethodCard->name => number_format(floatval(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', false)->sum('paid_bank')), $number_of_decimals, ".", ""),
//                    "7" => "space",
//                    "8" => "space",

//                    __('Voided Items ') => '0',
//                    __('Voided Total ') => '0.00',
                        "9" => "space",
                        "10" => "space",

                        __('Discarded Items ') => (string)count(Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', true)->get()),
                        __('Discarded Total ') => number_format((Sale::whereIn('z_report_id', $zReportIdsForMonth)->where('is_cancelled', true)->sum('payable_after_all')), $number_of_decimals, ".", ""),
                        "11" => "space",
                        "12" => "space",

//                        __('Open Sales') => '0',
//                        __('Open sales Total') => '0.00',
//                        "13" => "space",
//                        "14" => "space",

                        __('Printed On') => now()->format("d/m/Y - H:i A"),
                    ]),
            ],
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
            if ($sales_data == 'space') {
                $printString .= "LR:1;";
                continue;
            }

            if ($sales_key == 'row_data_header') {
                $printString .= "B:;";
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

            $printString .= $row;

            if ($sales_key == 'row_data_header') {
                $printString .= "UB:;";
            }
        }

        $printString .= "B:;LR:2;";
        // Sales categories here
        $sales_by_categories = $printData['sales_by_categories'];
        $printString .= "R:;LRL:" . $sales_by_categories['title'] . ";UB:;";

        $category_head = $printData['sales_by_categories']['row_head'];
        $row = "R:;";
        // First part is left alone
        $row .= "B:;LRL:{$category_head[0]};UB:;";


        // Second part is divided into 15 characters right side and whatever is left on the left + 1 for space in between
        $full = $category_head[1];
        $right_side = str_pad($category_head[2], 15, " ", STR_PAD_LEFT);

        $full .= $right_side;

        $row .= "RRL:{$full};";
        $printString .= $row;


        $category_data = $printData['sales_by_categories']['row_data'];
        foreach($category_data as $val) {
            $row = "R:;";
            // First part is left alone
            $row .= "LRL:{$val['name']};";


            // Second part is divided into 15 characters right side and whatever is left on the left + 1 for space in between
            $full = $val['sales'];
            $right_side = str_pad(number_format(floatval($val['net_amount']), 2, ".", ""), 15, " ", STR_PAD_LEFT);

            $full .= $right_side;

            $row .= "RRL:{$full};";

            $printString .= $row;
        }

        $category_footer = $printData['sales_by_categories']['row_footer'];
        $row = "R:;";
        // First part is left alone
        $row .= "B:;LRL:{$category_footer[0]};UB:;";


        // Second part is divided into 15 characters right side and whatever is left on the left + 1 for space in between
        $full = $category_footer[1];
        $right_side = str_pad($category_footer[2], 15, " ", STR_PAD_LEFT);

        $full .= $right_side;

        $row .= "RRL:{$full};";

        $printString .= $row;


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
