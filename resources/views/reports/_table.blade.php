@extends('layouts.blank-view')

<div id="printTableContainer">
    <table class="table table-bordered">
        <thead>
            <tr><title>{{__('Stock alert')}}</title>
                <th class="text-gray-900 fw-bold w-auto text-center py-3">{{ __('#') }}</th>
                <th class="text-gray-900 text-center fw-bold">{{ __('Ingredient name') }}</th>
                <th class="text-center text-gray-900 fw-bold">{{ __('Price') }}</th>
                <th class="text-center text-gray-900 fw-bold">{{ __('Available Quantity') }}</th>
                <th class="text-center text-gray-900 fw-bold">{{ __('Unit') }}</th>
                <th class="text-gray-900 fw-bold text-center">{{ __('Alert Quantity') }}</th>
            </tr>
        </thead>
        <tbody class="fw-semibold text-gray-800">
            @php $counter = 1; @endphp
            @foreach ($ingredient as $ingredients)
                <tr>
                    <td class="text-center py-4">{{ $counter++ }}</td>
                    <td>
                        <div class="text-gray-600 text-center fs-10.5 flex-column justify-content-center my-0 py-2">
                            <a href="{{ route('ingredient.edit', ['ingredient' => $ingredients->id]) }}"
                                class="text-gray-800 text-hover-primary">{{ $ingredients->name }}</a>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0 py-2">
                            @price($ingredients->price, $settings)
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0 py-2">
                            {{ $ingredients->quantity }}
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0 py-2">
                            {{ $ingredients->unit }}
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0 py-2">
                            {{ $ingredients->alert_quantity }}
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
