<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\Sale;
use Carbon\Carbon;

class PrintController extends Controller
{
    public function printStockAlert() {
        $ingredient = Ingredient::whereRaw('quantity <= alert_quantity')->get();
        return view('reports/_table', compact('ingredient'));
    }

    public function printOverall(Request $request) {
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
        } else {
            $orders = Sale::where(['is_cancelled' => false, 'is_paid' => true])->whereNotNull('completed_at')->get();
        }
        return view('reports._overall-table', compact('orders'));
    }
}
