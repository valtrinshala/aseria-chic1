@extends('layouts.main-view')
@section('title', 'Stock alert')
@section('setup-script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    @vite('resources/assets/js/custom/apps/reports/stock-alert-list.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0 p-0">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card shadow-none bg-transparent border-0 me-2 mb-2">
                        <div class="card-header border-0 pt-6 p-0 h-100px">
                            <div class="card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24">
                                    <g id="users-active" transform="translate(-8719 -6517)">
                                        <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24"
                                            transform="translate(8719 6517)" fill="none" />
                                        <path id="Subtraction_9" data-name="Subtraction 9"
                                            d="M22.108-386H4.278a1.757,1.757,0,0,1-1.283-.538,1.76,1.76,0,0,1-.539-1.283v-14.83H23.929v14.83a1.76,1.76,0,0,1-.538,1.283A1.76,1.76,0,0,1,22.108-386ZM9.632-398.057v1.406h7.122v-1.406Zm15.52-6H1.233a1.964,1.964,0,0,1-.041-.4v-3.716a1.76,1.76,0,0,1,.538-1.283A1.76,1.76,0,0,1,3.014-410H23.37a1.757,1.757,0,0,1,1.283.538,1.76,1.76,0,0,1,.539,1.283v3.716a1.956,1.956,0,0,1-.041.4Z"
                                            transform="translate(8717.808 6927)" fill="#5d4bdf" />
                                    </g>
                                </svg>
                                <span
                                    class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 p-4">
                                    {{ __('Stock alert') }}</span>
                            </div>
                            <div class="card-toolbar">
                                <div class="d-flex align-items-center gap-2 gap-lg-0">
                                    @include('settings.search', [
                                        'label' => __('Search stock by name'),
                                    ])

                                    <div class="d-flex justify-content-end" data-kt-customer-table-filter="print">
                                        <div class="btn btn-primary w-125px h-45px border-0" onclick="printTable()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21"
                                                viewBox="0 0 36 36" class="me-3">
                                                <g id="print-settings" transform="translate(-8719 -6517)">
                                                    <rect id="Rectangle_95" data-name="Rectangle 95" width="36"
                                                        height="36" transform="translate(8719 6517)" fill="none" />
                                                    <path id="print_FILL0_wght300_GRAD0_opsz48"
                                                        d="M136.6-799.776v-5.854H120.587v5.854h-2.1v-7.914H138.7v7.914Zm-22.959,2.061h0Zm26.343,4.366a1.476,1.476,0,0,0,1.07-.444,1.413,1.413,0,0,0,.452-1.041,1.411,1.411,0,0,0-.452-1.041,1.467,1.467,0,0,0-1.061-.444,1.457,1.457,0,0,0-1.07.444,1.426,1.426,0,0,0-.443,1.041,1.426,1.426,0,0,0,.443,1.041,1.447,1.447,0,0,0,1.061.443ZM136.6-779.435v-8.822H120.587v8.822Zm2.1,2.061H118.487v-7.8h-6.946v-10.541a3.886,3.886,0,0,1,1.2-2.887,4.062,4.062,0,0,1,2.96-1.175h25.789a4.06,4.06,0,0,1,2.963,1.175,3.89,3.89,0,0,1,1.2,2.887v10.541H138.7Zm4.845-9.86v-8.49a1.9,1.9,0,0,0-.592-1.419,2.026,2.026,0,0,0-1.466-.573H115.7a2.006,2.006,0,0,0-1.466.583,1.916,1.916,0,0,0-.592,1.418v8.48h4.845v-3.084H138.7v3.084Z"
                                                        transform="translate(8608.405 7327.531)" fill="#ffffff" />
                                                </g>
                                            </svg>
                                            {{ __('Print') }}
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    function printTable() {
                                        var printWindow = window.open('/admin/printStockAlert', '_blank');
                                        printWindow.onload = function() {
                                            printWindow.print();
                                            setTimeout(function() {
                                                printWindow.close();
                                            }, 1000);
                                        };
                                    }
                                </script>
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
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_customers_table">
                                <thead>
                                    <tr class="text-start text-gray-600 fs-6 gs-0">
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">
                                            {{ __('Ingredient name') }}</th>
                                            <th class="text-gray-900 fw-bold min-w-125px py-10">
                                            {{ __('Price') }}</th>
                                            <th class="text-gray-900 fw-bold min-w-125px py-10">
                                            {{ __('Available Quantity') }}</th>
                                            <th class="text-gray-900 fw-bold min-w-125px py-10">
                                            {{ __('Unit') }}</th>
                                            <th class="text-gray-900 fw-bold text-end min-w-70px py-10">
                                            {{ __('Alert Quantity') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-800">
                                    @foreach ($ingredients as $ingredient)
                                        <tr>
                                            <td>
                                                <div
                                                    class="text-gray-600 fs-10.5 flex-column justify-content-center my-0 py-4">
                                                    <a href="{{ route('ingredient.edit', ['ingredient' => $ingredient->id]) }}"
                                                        class="text-gray-800 text-hover-primary mb-1">{{ $ingredient->name }}</a>
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    @price($ingredient->price, $settings)
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    {{ $ingredient->quantity }}
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    {{ $ingredient->unit }}
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                class="text-end text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    {{ $ingredient->alert_quantity }}
                                                </div>
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
    </div>
@endsection
