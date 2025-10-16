
@extends('layouts.kitchen')
@section('title', 'Kitchen')
@section('setup-script')
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    @vite('resources/assets/js/custom/apps/sales/list/list.js')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
@endsection
@section('page-style')
    <link href="{{ asset('assets/css/kitchen.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
    <style>
        #kt_customers_table_wrapper {
            overflow: auto !important;
            height: 80vh;
        }
    </style>
    <script>
        window.settings = {
            'currency_on_left': `{{ $settings->currency_symbol_on_left }}`,
            'currency_symbol': `{{ $settings->currency_symbol }}`
        }
    </script>

    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-4 mt-4 p-0">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="p-0 app-container container-xxl">
                    <div class="shadow-none bg-transparent border-0 me-2">
                        <div class="d-none border-0 px-0 py-8 d-flex justify-content-end">
                            <div class="card-toolbar d-flex">
                                <div class="d-flex align-items-center gap-2 gap-lg-0">
                                    @include('settings.search', [
                                        'label' => __("Search orders"),
                                    ])
                                </div>
                                <div class="d-flex justify-content-end align-items-center d-none"
                                    data-kt-customer-table-toolbar="selected">
                                    <div class="fw-bold me-5">
                                        <span class="me-2"
                                            data-kt-customer-table-select="selected_count"></span>{{ __('Selected') }}
                                    </div>
                                    <button type="button" class="btn btn-danger"
                                        data-kt-customer-table-select="delete_selected">{{ __('Delete Selected') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card" style="background: transparent">
                        <div class="card card-body pt-0">
                            <table class="table align-middle order-filter-table table-row-dashed fs-6 gy-5" id="kt_customers_table">
                                <thead>
                                    <tr class="text-start text-gray-600 fs-6 gs-0">
                                        <th class="w-10px pe-2 pt-10 pb-10 d-none">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                    data-kt-check-target="#kt_customers_table .form-check-input"
                                                    value="1" />
                                            </div>
                                        </th>
                                        <th class="text-gray-900 fw-bold min-w-20px pt-10 pb-10">{{ __('Receipt #') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px pt-10 pb-10">{{ __('Order ID') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px pt-10 pb-10">{{ __('Order type') }}
                                        </th>
                                        <th class="text-gray-900 fw-bold min-w-125px pt-10 pb-10">{{ __('Total') }}</th>
                                        <th class="text-end text-gray-900 fw-bold min-w-125px pt-10 pb-10">{{ __('Paid with') }}
                                        </th>
                                        <th class="text-end text-gray-900 fw-bold min-w-125px pt-10 pb-10">{{ __('Created date') }}
                                        </th>
                                        <th class="text-end text-gray-900 fw-bold min-w-125px pt-10 pb-10">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach ($order_list as $sale)
                                        <tr>
                                            <td class="d-none">
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $sale->id }}" />
                                                </div>
                                            </td>
                                            <td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                <a href="{{ route('order.show', ['order' => $sale->id]) }}"
                                                    class="text-gray-800 text-hover-primary mb-1">{{ (int)$sale->order_receipt }}</a>
                                            </td>
                                            <td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                {{ $sale->order_number }}</td>
                                            <td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                {{ ucwords(str_replace('_', ' ', $sale->order_type))}}
                                            </td>
                                            <td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                @price($sale->payable_after_all, $settings)
                                            </td>
                                            <td class="text-end pe-0 text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                {{ $sale->payment_method_type }}</td>
                                            <td class="text-end pe-0 text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                {{ $sale->created_at->format('d.m.Y') }}</td>
                                            <td class="text-end pe-0">
                                                @if ($sale->is_cancelled == true)
                                                    <div class="badge badge-light-danger">{{ __('Refunded') }}</div>
                                                @elseif($sale->completed_at != null)
                                                    <div class="badge badge-light-success">{{ __('Completed') }}</div>
                                                @elseif($sale->chef_id != null)
                                                    <div class="badge badge-light-primary">{{ __('In Progress') }}</div>
                                                @else
                                                    <div class="badge badge-light-warning">{{ __('Waiting') }}</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bottom-bar">
            <div class="bar d-flex justify-content-center mx-4 p-4 card rounded-0">
                <div class="d-flex w-100 justify-content-end align-items-center gap-4">
                    <div class="left flex-grow-1 d-flex gap-4 align-items-center justify-content-end">
                        <div class="name ms-4">
                            <div class="fw-bold">{{ $user->name }}</div>
                            <div class="secondary-text">{{ $user->random_id }}</div>
                        </div>
                        <div class="d-flex align-items-center w-50 gap-4">
                            <div class="sale-limit-holder"></div>
                            <div class="ml-5 sale-search-holder flex-grow-1"></div>
                        </div>
                    </div>

                    <div class="right d-flex gap-4 align-items-center justify-content-end">
                        <div class="sale-pargination-holder"></div>
                        <div class="options">
                        <button onclick="location.reload()" class="btn btn-primary d-flex align-items-center gap-3">
                            <svg id="Layer_1" class="text-white" width="20" height="20" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 118.04 122.88"><path d="M16.08,59.26A8,8,0,0,1,0,59.26a59,59,0,0,1,97.13-45V8a8,8,0,1,1,16.08,0V33.35a8,8,0,0,1-8,8L80.82,43.62a8,8,0,1,1-1.44-15.95l8-.73A43,43,0,0,0,16.08,59.26Zm22.77,19.6a8,8,0,0,1,1.44,16l-10.08.91A42.95,42.95,0,0,0,102,63.86a8,8,0,0,1,16.08,0A59,59,0,0,1,22.3,110v4.18a8,8,0,0,1-16.08,0V89.14h0a8,8,0,0,1,7.29-8l25.31-2.3Z"/></svg>
                            {{ __('Refresh') }}
                        </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
