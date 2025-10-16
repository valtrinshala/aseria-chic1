<?php

namespace App\Http\Controllers;

use App\Models\AndroidModels\FoodCategory;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $year = now()->year;
        $annualColumns = ['payable_after_all', 'cart_total_cost'];
        $results = [];

        foreach ($annualColumns as $column) {
            $arrayLength = 12;
            $array = array_fill(0, $arrayLength, 0);

            $query = Sale::where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')
                ->selectRaw("SUM($column) as total_amount")
                ->whereYear('created_at', $year)
                ->selectRaw('MONTH(created_at) as time')
                ->groupBy('time');

            $data = $query->pluck('total_amount', 'time');

            foreach ($data as $key => $value) {
                $array[$key - 1] = $value;
            }

            $results[] = $array;
        }
        return view('dashboard/dashboard-index', ['data' => $results]);
    }

    public function states(Request $request): JsonResponse
    {
        $var = now();
        $year = $var->year;
        $month = $var->month;
        $day = $var->day;

        $saleModel = Sale::class;
        $duration = $request->is_duration;
        return response()->json(
            [
                'total_price_amount' => $this->calculateAmountFromModel($saleModel, 'cart_total_price', $duration),
                'last_total_price_amount' => $this->calculateAmountFromModel($saleModel, 'cart_total_price', $duration, true),
                'chart_total_price_amount' => $this->getSalesAggregatedByTime('cart_total_price', $duration, $year, $month, $day),

                'total_cost_amount' => $this->calculateAmountFromModel($saleModel, 'cart_total_cost', $duration),
                'last_total_cost_amount' => $this->calculateAmountFromModel($saleModel, 'cart_total_cost', $duration, true),
                'chart_total_cost_amount' => $this->getSalesAggregatedByTime('cart_total_cost', $duration, $year, $month, $day),

                'total_discount_amount' => $this->calculateAmountFromModel($saleModel, 'discount_amount', $duration),
                'last_total_discount_amount' => $this->calculateAmountFromModel($saleModel, 'discount_amount', $duration, true),
                'chart_total_discount_amount' => $this->getSalesAggregatedByTime('discount_amount', $duration, $year, $month, $day),

                'total_payable_amount' => $this->calculateAmountFromModel($saleModel, 'payable_after_all', $duration),
                'last_total_payable_amount' => $this->calculateAmountFromModel($saleModel, 'payable_after_all', $duration, true),
                'chart_total_payable_amount' => $this->getSalesAggregatedByTime('payable_after_all', $duration, $year, $month, $day),

                'total_profit_amount' => $this->calculateAmountFromModel($saleModel, 'profit_after_all', $duration),
                'last_total_profit_amount' => $this->calculateAmountFromModel($saleModel, 'profit_after_all', $duration, true),
                'chart_total_profit_amount' => $this->getSalesAggregatedByTime('profit_after_all', $duration, $year, $month, $day),

                'total_tax_amount' => $this->calculateAmountFromModel($saleModel, 'tax_amount', $duration),
                'last_total_tax_amount' => $this->calculateAmountFromModel($saleModel, 'tax_amount', $duration, true),
                'chart_total_tax_amount' => $this->getSalesAggregatedByTime('tax_amount', $duration, $year, $month, $day),

                'days_of_month' => $var->daysInMonth,

//                'chart' => $this->generateChartFromModel($saleModel, 'payable_after_all', $duration),
//                'last_chart' => $this->generateChartFromModel($saleModel, 'payable_after_all', $duration, true),
            ]
        );
    }

    public function getSalesAggregatedByTime($column, $duration, $year, $month = null, $day = null)
    {
        if (!$duration){
            return null;
        }
        $query = Sale::where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->selectRaw("sum({$column}) as total_amount");

        if ($duration === 'day' && $day) {
            $query->whereDate('created_at', "{$year}-{$month}-{$day}")
                ->selectRaw('HOUR(created_at) as time')
                ->groupBy('time');
        } elseif ($duration === 'month' && $month) {
            $query->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->selectRaw('DAY(created_at) as time')
                ->groupBy('time');
        } elseif ($duration === 'year') {
            $query->whereYear('created_at', $year)
                ->selectRaw('MONTH(created_at) as time')
                ->groupBy('time');
        }
        return $query->pluck('total_amount', 'time');
    }
}
