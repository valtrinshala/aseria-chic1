<?php

namespace App\Http\Controllers;

use App\Helpers\PriceHelper;
use App\Models\Ingredient;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\User;
use App\Models\ZReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Contracts\View\View;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function reportIndex(): View
    {
        $chefs = User::where('role_id', config('constants.role.chefId'))->get();
        $orderTakers = User::where('role_id', config('constants.role.orderTakerId'))->get();

        return view('reports/overall-report', compact('chefs', 'orderTakers'));
    }
    /**
     * Display a listing of the resource.
     */
    public function taxReportIndex(): View
    {
        $column = 'tax_amount';
        $query = Sale::where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->selectRaw("sum({$column}) as total_amount");
        $query->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as time')
            ->groupBy('time');
        $arrayLength = 12;
        $array = array_fill(0, $arrayLength, 0);
        $data = $query->pluck('total_amount', 'time');

        foreach ($data as $key => $value) {
            $array[$key - 1] = $value;
        }
        return view('reports/tax-report', ['taxes' => $array, 'currentYear' => date('Y')]);
    }

    /**
     * Display a listing of the resource.
     */
    public function stockAlertIndex(): View
    {
        $ingredients = Ingredient::whereRaw('CAST(quantity AS SIGNED) <= CAST(alert_quantity AS SIGNED)')->get();
        return view('reports/stock-alert', compact('ingredients'));
    }
    public function filters(Request $request) {
        $filters = json_decode($request->query('filters'), true);
        $orders = $request->get('order');
        $columns = $request->get('columns');
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

        $query->when(!empty($filters['order']) && $filters['order'] !== 'all', function ($q) use ($filters) {
            return $q->where('pos_or_kiosk', $filters['order']);
        })
            ->when(!empty($filters['orderType']) && $filters['orderType'] !== 'all', function ($q) use ($filters) {
                return $q->where('order_type', $filters['orderType']);
            })
            ->when(!empty($filters['paymentMethod']) && $filters['paymentMethod'] !== 'all', function ($q) use ($filters) {
                return $q->where('payment_method_type', $filters['paymentMethod']);
            })
            ->when(!empty($filters['orderTaker']) && $filters['orderTaker'] !== 'all', function ($q) use ($filters) {
                return $q->where('order_taker_id', $filters['orderTaker']);
            })
            ->when(!empty($filters['chef']) && $filters['chef'] !== 'all', function ($q) use ($filters) {
                return $q->where('chef_id', $filters['chef']);
            })
            ->when(!empty($filters['date']) && $filters['date'] !== 'all', function ($q) use ($filters) {
                if (str_contains($filters['date'], 'to')) {
                    [$startDate, $endDate] = explode(' to ', $filters['date']);
                    $startDate = Carbon::parse($startDate)->startOfDay();
                    $endDate = Carbon::parse($endDate)->endOfDay();
                } else {
                    $startDate = Carbon::parse($filters['date'])->startOfDay();
                    $endDate = Carbon::parse($filters['date'])->endOfDay();
                }
                return $q->whereBetween('created_at', [$startDate, $endDate]);
            });

        $query->where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at');
        $settings = Setting::first();
        $totals = [
            'total_cart_price' => PriceHelper::formatPrice($query->sum('cart_total_price'), $settings),
            'total_cart_cost' => PriceHelper::formatPrice($query->sum('cart_total_cost'), $settings),
            'total_discount' => PriceHelper::formatPrice($query->sum('discount_amount'), $settings),
            'total_payable' => PriceHelper::formatPrice($query->sum('payable_after_all'), $settings),
            'total_profit' => PriceHelper::formatPrice($query->sum('profit_after_all'), $settings),
            'total_tax' => PriceHelper::formatPrice($query->sum('tax_amount'), $settings),
        ];

        $start = $request->get('start', 0);
        $perPage = $request->get('length', 10);
        $recordsFiltered = $query->count();
        $orders = $query->skip($start)->take($perPage)->get();

        $newData = $orders->map(function ($order) use ($settings){

            $order->extraData = new \stdClass();
            $order->extraData->id = $order->id;
            $order->extraData->order_url = route('order.show', ['order' => $order->id]);
            $order->extraData->order_receipt = (int)$order->order_receipt != 0 ? (int)$order->order_receipt : '';
            $order->extraData->order_number = $order->order_number;
            $order->extraData->order_type = __(ucwords(str_replace('_', ' ', $order->order_type)));
            $order->extraData->pos_or_kiosk = __($order->pos_or_kiosk);
            $order->extraData->payment_method_type = __($order->payment_method_type);
            $order->extraData->cart_total_cost = PriceHelper::formatPrice($order->cart_total_cost, $settings);
            $order->extraData->discount_amount = PriceHelper::formatPrice($order->discount_amount, $settings);
            $order->extraData->profit_after_all = PriceHelper::formatPrice($order->profit_after_all, $settings);
            $order->extraData->tax_amount = PriceHelper::formatPrice($order->tax_amount, $settings);
            $order->extraData->payable_after_all = PriceHelper::formatPrice($order->payable_after_all, $settings);
            $order->extraData->created_at = \Carbon\Carbon::parse($order->created_at)->format('m/d/Y h:i');
            $order->extraData->updated_at = \Carbon\Carbon::parse($order->updated_at)->format('m/d/Y h:i');
            return $order->extraData;
        });
        $data = [
            'draw' => $request->get('draw'),
            'recordsTotal' => Sale::count(),
            'recordsFiltered' => $recordsFiltered,
            'data' => $newData,
            'totals' => $totals,
        ];
        return response()->json($data);
    }

    /**
     * Display a listing of the resource.
     */
    public function zReportIndex(): View
    {
        $zReports = ZReport::orderBy('created_at', 'desc')->get();
        return view('reports/z-report', compact('zReports'));
    }

    /**
     * Display a listing of the resource.
     */
    public function zReportByDate(Request $request){
        $dateString = $request->get('date');
        if (str_contains($dateString, 'to')) {
            [$startDate, $endDate] = explode(' to ', $dateString);
            $startDate = \Illuminate\Support\Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
            $zReports = ZReport::whereBetween('created_at', [$startDate, $endDate])->with('cashRegister', 'location')->get();
        } elseif(is_null($dateString)) {
            $zReports = ZReport::with('cashRegister', 'location')->get();
        } else {
            $date = Carbon::parse($dateString);
            $zReports = ZReport::whereDate('created_at', $date)->with('cashRegister', 'location')->get();
        }
        return $this->response(0, '', $zReports);
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
