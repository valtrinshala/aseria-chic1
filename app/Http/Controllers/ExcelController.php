<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function ZReportExportExcel(Request $request){
        $filters = $request->query('date');
        if ($filters) {
            $filters = json_decode($filters, true);

            $query = Sale::query();
            $query->where(function ($query) use ($filters) {
                if (!empty($filters['orderTaker']) && $filters['orderTaker'] !== 'all') {
                    $query->where('order_taker_id', $filters['orderTaker']);
                }
                if (!empty($filters['paymentMethod']) && $filters['paymentMethod'] !== 'all') {
                    $query->where('payment_method_type', $filters['paymentMethod']);
                }
                if (!empty($filters['order']) && $filters['order'] !== 'all') {
                    $query->where('pos_or_kiosk', $filters['order']);
                }
                if (!empty($filters['orderType']) && $filters['orderType'] !== 'all') {
                    $query->where('order_type', $filters['orderType']);
                }
                if (!empty($filters['chef']) && $filters['chef'] !== 'all') {
                    $query->where('chef_id', $filters['chef']);
                }
                if (!empty($filters['date']) && $filters['date'] !== 'all') {
                    if (str_contains($filters['date'], 'to')) {
                        [$startDate, $endDate] = explode(' to ', $filters['date']);
                        $startDate = Carbon::parse($startDate)->startOfDay();
                        $endDate = Carbon::parse($endDate)->endOfDay();
                    } else {
                        $startDate = Carbon::parse($filters['date'])->startOfDay();
                        $endDate = Carbon::parse($filters['date'])->endOfDay();
                    }
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            });
            $orders = $query->where(['is_cancelled' => false, 'is_paid' => true])
                ->whereNotNull('completed_at')
                ->select(
                    'order_receipt',
                    'order_number',
                    'order_type',
                    'pos_or_kiosk',
                    'payment_method_type',
                    'cart_total_cost',
                    'discount_amount',
                    'profit_after_all',
                    'tax_amount',
                    'payable_after_all',
                    DB::raw('DATE(created_at) as created_date'),
                    DB::raw('DATE(updated_at) as updated_date')
                );
//            $orders = $query->where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->select('order_receipt', 'order_number', 'order_type', 'pos_or_kiosk', 'payment_method_type', 'cart_total_cost', 'discount_amount', 'profit_after_all', 'tax_amount', 'payable_after_all', 'created_at', 'updated_at');
            $totals = [
                'total_cart_price' => $query->where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('cart_total_price'),
                'total_cart_cost' => $query->where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('cart_total_cost'),
                'total_discount' => $query->where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('discount_amount'),
                'total_payable' => $query->where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('payable_after_all'),
                'total_profit' => $query->where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('profit_after_all'),
                'total_tax' => $query->where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('tax_amount'),
            ];
        } else {
            $orders = Sale::where(['is_cancelled' => false, 'is_paid' => true])
                ->whereNotNull('completed_at')
                ->select(
                    'order_receipt',
                    'order_number',
                    'order_type',
                    'pos_or_kiosk',
                    'payment_method_type',
                    'cart_total_cost',
                    'discount_amount',
                    'profit_after_all',
                    'tax_amount',
                    'payable_after_all',
                    DB::raw('DATE(created_at) as created_date'),
                    DB::raw('DATE(updated_at) as updated_date')
                );
            $totals = [
                'total_cart_price' => Sale::where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('cart_total_price'),
                'total_cart_cost' => Sale::where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('cart_total_cost'),
                'total_discount' => Sale::where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('discount_amount'),
                'total_payable' => Sale::where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('payable_after_all'),
                'total_profit' => Sale::where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('profit_after_all'),
                'total_tax' => Sale::where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('tax_amount'),
            ];
        }
        if (isset($filters['date'])) {
            if (str_contains($filters['date'], 'to')) {
                [$startDate, $endDate] = explode(' to ', $filters['date']);
                $fileName = 'overall-report-from-' . $startDate . '-to-'. $endDate . '.xlsx';
                return Excel::download(new SalesReportExport($orders, $totals, $filters), $fileName);
            } else {
                $fileName = 'overall-report-for-date-'. $filters['date'].'.xlsx';
                return Excel::download(new SalesReportExport($orders, $totals, $filters), $fileName);
            }
        }
        $fileName = 'overall-report-for-all-time.xlsx';
        return Excel::download(new SalesReportExport($orders, $totals, $filters), $fileName);
    }
}
