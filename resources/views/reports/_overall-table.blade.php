@extends('layouts.blank-view')
<div id="printOverallContainer">
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 0 5px;
        }
    </style>
    <h3>
        {{ $settings->app_name }}
    </h3>
    <h3>{{__('Overall Report')}} -
        @foreach($filters as $key => $filter)
            {{ $key }} : {{ $filter }},
        @endforeach
    </h3>

    <h3>{{__('Summary')}}</h3>
    <table border="1" style="margin-bottom: 20px">
        <thead>
        <tr style="background-color: #d7d7d7; height: 45px;">
            <th style="width: 95px">{{ __('Total sale amount') }}</th>
            <th style="width: 95px">{{ __('Total cost amount') }}</th>
            <th style="width: 95px">{{ __('Total discount amount') }}</th>
            <th style="width: 95px">{{ __('Total profit amount') }}</th>
            <th style="width: 95px">{{ __('Total tax amount') }}</th>
            <th style="width: 95px">{{ __('Total payable amount') }}</th>
        </tr>
        </thead>
        <tbody>
        <tr style="text-align: center; font-weight: bold">
            <td style="height: 45px;">
                @price($totals['total_cart_price'], $settings)
            </td>
            <td style="height: 45px;">
                @price($totals['total_cart_cost'], $settings)
            </td>
            <td style="height: 45px;">
                @price($totals['total_discount'], $settings)
            </td>
            <td style="height: 45px;">
                @price($totals['total_profit'], $settings)
            </td>
            <td style="height: 45px;">
                @price($totals['total_tax'], $settings)
            </td>
            <td style="height: 45px;">
                @price($totals['total_payable'], $settings)
            </td>
        </tr>
        </tbody>
    </table>


    <h3>{{__('Detailed report')}}</h3>
    <table border="1">
        <thead>
        <tr style="background-color: #d7d7d7; height: 45px;">
            <th style="height: 45px">{{ __('Receipt #') }}</th>
            <th style="height: 45px">{{ __('Order Id') }}</th>
            <th style="height: 45px; width: 80px">{{ __('Order type') }}</th>
            <th style="height: 45px; width: 80px">{{ __('POS / E-kiosk') }}</th>
            <th style="height: 45px">{{ __('Paid with') }}</th>
            <th style="height: 45px">{{ __('Cost') }}</th>
            <th style="height: 45px">{{ __('Discount') }}</th>
            <th style="height: 45px">{{ __('Profit') }}</th>
            <th style="height: 45px">{{ __('Tax amount') }}</th>
            <th style="height: 45px">{{ __('Payable') }}</th>
            <th style="height: 45px; width: 100px">{{ __('Date created') }}</th>
            <th style="height: 45px; width: 100px">{{ __('Date updated') }}</th>
        </tr>
        </thead>
        <tbody>
        @php $counter = 1; @endphp
        @foreach ($orders as $sale)
            <tr>
                <td style="text-align: center; padding: 0">
                    {{ (int)$sale->order_receipt }}
                </td>
                <td style="text-align: center">
                    {{ $sale->order_number }}
                </td>
                <td style="text-align: center">
                    {{ ucwords(str_replace('_', ' ', $sale->order_type)) }}
                </td>
                <td style="text-align: center">
                    {{ $sale->pos_or_kiosk }}
                </td>
                <td style="text-align: center">
                    {{ $sale->payment_method_type }}
                </td>
                <td style="text-align: center">
                    @price($sale->cart_total_cost, $settings)
                </td>
                <td style="text-align: center">
                    @price($sale->discount_amount, $settings)
                </td>
                <td style="text-align: center">
                    @price($sale->profit_after_all, $settings)
                </td>
                <td style="text-align: center">
                    @price($sale->tax_amount, $settings)
                </td>
                <td style="text-align: center">
                    @price($sale->payable_after_all, $settings)
                </td>
                <td style="text-align: center">{{ $sale->created_at }}</td>
                <td style="text-align: center">{{ $sale->updated_at }}</td>
            </tr>
            @php $counter++ @endphp
        @endforeach
        </tbody>
    </table>
</div>
