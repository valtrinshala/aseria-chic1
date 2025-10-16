<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Models\Sale;
use App\Models\User;
use App\Models\ZReport;
use App\Services\ZReportInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PdfController extends Controller
{
    public function pdfZReport(ZReport $zReport)
    {
        $inject = new ZReportInvoice($zReport);
        $data = $inject->getZReportDataForPrinter();
//        return view('pdf/zReport', ['data' => $data]);
        $pdf = PDF::loadView('pdf/zReport', ['data' => $data]);
        return $pdf->download('zReport-'.$zReport->start_z_report.'/'.$zReport->end_z_report.'.pdf');
    }

    public function pdfOverall(Request $request)
    {
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
            $orders = $query->where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->get();
            $totals = [
                'total_cart_price' => $query->where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('cart_total_price'),
                'total_cart_cost' => $query->where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('cart_total_cost'),
                'total_discount' => $query->where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('discount_amount'),
                'total_payable' => $query->where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('payable_after_all'),
                'total_profit' => $query->where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('profit_after_all'),
                'total_tax' => $query->where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('tax_amount'),
            ];
        } else {
            $orders = Sale::where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->get();
            $totals = [
                'total_cart_price' => Sale::where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('cart_total_price'),
                'total_cart_cost' => Sale::where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('cart_total_cost'),
                'total_discount' => Sale::where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('discount_amount'),
                'total_payable' => Sale::where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('payable_after_all'),
                'total_profit' => Sale::where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('profit_after_all'),
                'total_tax' => Sale::where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->sum('tax_amount'),
            ];
        }
        $pdf = PDF::loadView('reports._overall-table', ['orders' => $orders, 'totals' => $totals, 'filters' => $filters])->setPaper('a4', 'landscape')->setOptions(['defaultFont' => 'sans-serif']);
        if (isset($filters['date'])) {
            if (str_contains($filters['date'], 'to')) {
                [$startDate, $endDate] = explode(' to ', $filters['date']);
                $fileName = 'overall-report-from-' . $startDate . '-to-'. $endDate . '.pdf';
                return $pdf->download($fileName);
            } else {
                $fileName = 'overall-report-for-date-'. $filters['date'].'.pdf';
                return $pdf->download($fileName);
            }
        }
        $fileName = 'overall-report-for-all-time.pdf';
        return $pdf->download($fileName);
    }
}
