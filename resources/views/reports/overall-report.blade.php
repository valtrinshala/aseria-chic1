@extends('layouts.main-view')
@section('title', 'Overall report')
@section('setup-script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    @vite('resources/assets/js/custom/apps/reports/overall-report-list.js')
@endsection
@section('page-style')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0 p-0">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card shadow-none bg-transparent border-0">
                        <div class="card-header border-0 pt-6 p-0">
                            <div class="card-title fw-normal fs-7 flex-wrap justify-content-between w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24">
                                    <g id="overall-report-active" transform="translate(-8719 -6517)">
                                        <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24"
                                            transform="translate(8719 6517)" fill="none"></rect>
                                        <path id="Subtraction_10" data-name="Subtraction 10"
                                            d="M22.206-567.332H1.8a1.734,1.734,0,0,1-1.264-.532A1.731,1.731,0,0,1,0-569.128v-15.078a1.734,1.734,0,0,1,.532-1.264A1.736,1.736,0,0,1,1.8-586h20.41a1.736,1.736,0,0,1,1.264.531A1.736,1.736,0,0,1,24-584.205v15.078a1.733,1.733,0,0,1-.531,1.264A1.734,1.734,0,0,1,22.206-567.332ZM5.348-573.39a.852.852,0,0,0-.617.263.841.841,0,0,0-.262.611.834.834,0,0,0,.263.617.85.85,0,0,0,.611.257.845.845,0,0,0,.617-.257.837.837,0,0,0,.256-.611.856.856,0,0,0-.256-.617A.828.828,0,0,0,5.348-573.39Zm4.7-1.544v1.412h9.314v-1.412ZM4.636-581.81v6.254H6.048v-6.254Zm5.414,1.651v1.412h9.314v-1.412Z"
                                            transform="translate(8719 7105.666)" fill="#5d4bdf"></path>
                                    </g>
                                </svg>
                                <span
                                    class="page-heading d-flex text-info flex-grow-1 fw-bold fs-3 flex-column justify-content-center my-0 p-4">
                                    {{ __('Overall report') }}</span>
                                <div class="card-toolbar">
                                    <div class="d-flex align-items-center gap-2 gap-lg-0">
                                        <span class="ms-3 fs-5"> {{ __('Filters:') }} </span>
                                        <div class="card-toolbar">
                                            <div>
                                                <button type="button"
                                                    class="btn btn-light btn-flex btn-center btn-white justify-content-center ms-5"
                                                    data-kt-menu-trigger="{default: 'click', lg: 'click'}"
                                                    data-kt-menu-placement="bottom-end">
                                                    {{ __('Order') }}
                                                    <span class="me-3"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                                        viewBox="0 0 11 6">
                                                        <path id="Path_6" data-name="Path 8"
                                                            d="M.251.263a.858.858,0,0,1,1.168,0l4.09,3.879L9.6.263a.858.858,0,0,1,1.168,0,.755.755,0,0,1,0,1.108L6.093,5.8a.858.858,0,0,1-1.168,0L.251,1.371a.755.755,0,0,1,0-1.108Z"
                                                            transform="translate(-0.009 -0.033)" fill="#264653"></path>
                                                    </svg>
                                                </button>
                                                <div class="menu menu-sub menu-sub-dropdown menu-column w-auto"
                                                    data-kt-menu="true">
                                                    <div class="card card-body w-auto p-3">
                                                        <div class="menu-item">
                                                            <div class="p-2 cursor-pointer">
                                                                <span class="order-option"
                                                                    data-value="all">{{ __('All') }}</span>
                                                            </div>
                                                            <div class="p-2 cursor-pointer order-option"
                                                                data-value="e_kiosk">
                                                                <span>{{ __('E-kiosk') }}</span>
                                                            </div>
                                                            <div class="p-2 cursor-pointer order-option" data-value="pos">
                                                                <span>{{ __('POS') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" id="orderButton"
                                                    class="btn btn-light btn-flex btn-center btn-white justify-content-center ms-5 text-nowrap"
                                                    data-kt-menu-trigger="{default: 'click', lg: 'click'}"
                                                    data-kt-menu-placement="bottom-end">
                                                    {{ __('By order type') }}
                                                    <span class="me-2"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                                        viewBox="0 0 11 6">
                                                        <path id="Path_6" data-name="Path 6"
                                                            d="M.251.263a.858.858,0,0,1,1.168,0l4.09,3.879L9.6.263a.858.858,0,0,1,1.168,0,.755.755,0,0,1,0,1.108L6.093,5.8a.858.858,0,0,1-1.168,0L.251,1.371a.755.755,0,0,1,0-1.108Z"
                                                            transform="translate(-0.009 -0.033)" fill="#264653"></path>
                                                    </svg>
                                                </button>
                                                <div class="menu menu-sub menu-sub-dropdown menu-column w-auto"
                                                    data-kt-menu="true">
                                                    <div class="card card-body w-auto p-3">
                                                        <div class="menu-item">
                                                            <div class="p-2 cursor-pointer">
                                                                <span class="filter-option"
                                                                    data-order-type="all">{{ __('All') }}</span>
                                                            </div>
                                                            <div class="p-2 cursor-pointer">
                                                                <span class="filter-option"
                                                                    data-order-type="dine_in">{{ __('Dine in') }}</span>
                                                            </div>
                                                            <div class="p-2 cursor-pointer">
                                                                <span class="filter-option"
                                                                    data-order-type="take_away">{{ __('Take away') }}</span>
                                                            </div>
                                                            <div class="p-2 cursor-pointer">
                                                                <span class="filter-option"
                                                                    data-order-type="delivery">{{ __('Delivery') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" id="paidWithButton"
                                                    class="btn btn-light btn-flex btn-center btn-white justify-content-center ms-5 text-nowrap"
                                                    data-kt-menu-trigger="{default: 'click', lg: 'click'}"
                                                    data-kt-menu-placement="bottom-end">
                                                    {{ __('Paid with') }}
                                                    <span class="me-2"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                                        viewBox="0 0 11 6">
                                                        <path id="Path_6" data-name="Path 6"
                                                            d="M.251.263a.858.858,0,0,1,1.168,0l4.09,3.879L9.6.263a.858.858,0,0,1,1.168,0,.755.755,0,0,1,0,1.108L6.093,5.8a.858.858,0,0,1-1.168,0L.251,1.371a.755.755,0,0,1,0-1.108Z"
                                                            transform="translate(-0.009 -0.033)" fill="#264653">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <div class="menu menu-sub menu-sub-dropdown menu-column w-auto"
                                                    data-kt-menu="true">
                                                    <div class="card card-body w-auto p-3">
                                                        <div class="menu-item">
                                                            <div class="p-2 cursor-pointer">
                                                                <span class="payment-method-option"
                                                                    data-payment-method="all">{{ __('All') }}</span>
                                                            </div>
                                                            <div class="p-2 cursor-pointer payment-method-option"
                                                                data-payment-method="cash">
                                                                <span>{{ __('Cash') }}</span>
                                                            </div>
                                                            <div class="p-2 cursor-pointer payment-method-option"
                                                                data-payment-method="card">
                                                                <span>{{ __('Card') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" id="byTakerButton"
                                                    class="btn btn-light btn-flex btn-center btn-white justify-content-center ms-5"
                                                    data-kt-menu-trigger="{default: 'click', lg: 'click'}"
                                                    data-kt-menu-placement="bottom-end">
                                                    {{ __('By taker') }}
                                                    <span class="me-2"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                                        viewBox="0 0 11 6">
                                                        <path id="Path_6" data-name="Path 8"
                                                            d="M.251.263a.858.858,0,0,1,1.168,0l4.09,3.879L9.6.263a.858.858,0,0,1,1.168,0,.755.755,0,0,1,0,1.108L6.093,5.8a.858.858,0,0,1-1.168,0L.251,1.371a.755.755,0,0,1,0-1.108Z"
                                                            transform="translate(-0.009 -0.033)" fill="#264653"></path>
                                                    </svg>
                                                </button>
                                                <div class="menu menu-sub menu-sub-dropdown menu-column w-auto"
                                                    data-kt-menu="true">
                                                    <div class="card card-body w-auto p-3">
                                                        <div class="menu-item">
                                                            <div class="p-2 cursor-pointer">
                                                                <span class="order-taker-option"
                                                                    data-order-taker-id="all">{{ __('All') }}
                                                                </span>
                                                            </div>
                                                            @foreach ($orderTakers as $orderTaker)
                                                                <div class="p-2 cursor-pointer order-taker-option"
                                                                    data-order-taker-id="{{ $orderTaker->id }}">
                                                                    {{ $orderTaker->name }}
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" id="byChefButton"
                                                    class="btn btn-light btn-flex btn-center btn-white justify-content-center ms-5"
                                                    data-kt-menu-trigger="{default: 'click', lg: 'click'}"
                                                    data-kt-menu-placement="bottom-end">
                                                    {{ __('By chef') }}
                                                    <span class="me-2"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                                        viewBox="0 0 11 6">
                                                        <path id="Path_6" data-name="Path 8"
                                                            d="M.251.263a.858.858,0,0,1,1.168,0l4.09,3.879L9.6.263a.858.858,0,0,1,1.168,0,.755.755,0,0,1,0,1.108L6.093,5.8a.858.858,0,0,1-1.168,0L.251,1.371a.755.755,0,0,1,0-1.108Z"
                                                            transform="translate(-0.009 -0.033)" fill="#264653">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <div class="menu menu-sub menu-sub-dropdown menu-column w-auto"
                                                    data-kt-menu="true">
                                                    <div class="card card-body w-auto p-3">
                                                        <div class="menu-item">
                                                            <div class="p-2 cursor-pointer">
                                                                <span class="chef-option"
                                                                    data-chef-id="all">{{ __('All') }}</span>
                                                            </div>
                                                            @foreach ($chefs as $chef)
                                                                <div class="p-2 cursor-pointer chef-option"
                                                                    data-chef-id="{{ $chef->id }}">
                                                                    {{ $chef->name }}
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center position-relative my-1 ms-5 me-5">
                                            <div class="input-group w-275px">
                                                <input
                                                    class="form-control form-control-solid rounded rounded-end-0 bg-light"
                                                    placeholder="{{__("Select starting & ending date")}}"
                                                    id="kt_ecommerce_sales_flatpickr" />
                                                <button class="btn btn-icon btn-light"
                                                    id="kt_ecommerce_sales_flatpickr_clear">
                                                    <i class="ki-duotone ki-cross fs-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </button>
                                            </div>
                                        </div>
                                        {{-- <div class="d-flex justify-content-end me-3" data-kt-customer-table-toolbar="base">
                                            <a href="{{ url(Request::url()) }}" class="btn btn-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="ms-2"
                                                    viewBox="0 0 24 24" fill="#ffffff" style="margin-right: 5px;">
                                                    <g id="import-export" transform="translate(-8677 -6517)">
                                                        <rect id="Rectangle_95" data-name="Rectangle 95" width="24"
                                                            height="24" transform="translate(8677 6517)" fill="none" />
                                                        <path id="cached_FILL0_wght300_GRAD0_opsz48"
                                                            d="M67.488-770.384a8.337,8.337,0,0,1-6.187-2.654,8.774,8.774,0,0,1-2.587-6.346v-1.72l-2.411,2.49-.84-.864,3.892-4.013,3.9,4.013-.84.864L60-781.1v1.72a7.5,7.5,0,0,0,2.2,5.428,7.127,7.127,0,0,0,5.295,2.254,7.841,7.841,0,0,0,1.488-.139,6.873,6.873,0,0,0,1.362-.408l.921.947a7.859,7.859,0,0,1-1.857.7A8.392,8.392,0,0,1,67.488-770.384Zm8.072-4.951-3.9-4.013.869-.882,2.393,2.472v-1.626a7.5,7.5,0,0,0-2.2-5.428,7.115,7.115,0,0,0-5.284-2.255,7.625,7.625,0,0,0-1.493.143,9.639,9.639,0,0,0-1.367.375l-.91-.947a7.219,7.219,0,0,1,1.851-.685,9.044,9.044,0,0,1,1.919-.2,8.328,8.328,0,0,1,6.182,2.649,8.778,8.778,0,0,1,2.581,6.351v1.662l2.422-2.479.84.853Z"
                                                            transform="translate(8621.537 7308.384)" fill="#ffffff" />
                                                    </g>
                                                </svg>
                                            </a>
                                        </div> --}}
                                        <button type="button" class="btn btn-primary flex-shrink-0 border-0"
                                            data-kt-menu-trigger="{default: 'click', lg: 'click'}"
                                            data-kt-menu-placement="bottom-end">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21"
                                                viewBox="0 0 24 24">
                                                <path id="download_FILL0_wght300_GRAD0_opsz48"
                                                    d="M192-761.734l-6.628-6.628,1.3-1.289,4.415,4.4V-780h1.815v14.748l4.415-4.4,1.3,1.289ZM182.309-756a2.219,2.219,0,0,1-1.624-.683,2.219,2.219,0,0,1-.683-1.624v-5.012h1.815v5.012a.471.471,0,0,0,.154.339.471.471,0,0,0,.339.154h19.385a.471.471,0,0,0,.339-.154.471.471,0,0,0,.154-.339v-5.012H204v5.012a2.219,2.219,0,0,1-.683,1.624,2.219,2.219,0,0,1-1.624.683Z"
                                                    transform="translate(-180.001 779.999)" fill="#fff" />
                                            </svg>
                                            {{ __('Save') }}
                                            <span class="me-2"></span>
                                        </button>
                                        <div class="menu menu-sub menu-sub-dropdown menu-column w-auto"
                                            data-kt-menu="true">
                                            <div class="card card-body w-auto p-3">
                                                <div class="menu-item">
                                                    <div class="p-2 cursor-pointer" id="pdfTable">
                                                        <span>{{ __('Save as a PDF file') }}</span>
                                                    </div>
                                                    <div class="p-2 cursor-pointer" id="excelTable">
                                                        <span>{{ __('Save as an Excel file') }}</span>
                                                    </div>
{{--                                                    <div class="p-2 cursor-pointer" id="printTable">--}}
{{--                                                        <span>{{ __('Print report') }}</span>--}}
{{--                                                    </div>--}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-none bg-transparent mt-6">
                        <div class="row">
                            <div class="col-xl-4 m-0">
                                <div class="card card-xl-stretch statistics-widget-1 mb-xl-6">
                                    <div class="card-header border-0 pt-5">
                                        <h3 class="card-title flex-column">
                                            <div class="d-flex align-items-center mb-1">
                                                <span id="total_cart_price" class="card-label fw-bold fs-2">
{{--                                                    @price($totals['total_cart_price'], $settings)--}}
                                                </span>
{{--                                                <div class="badge badge-light-success ms-2">^2.6%</div>--}}
                                            </div>
                                            <p class="fs-5 m-0 pt-2">{{ __('Total sale amount') }}</p>
                                        </h3>
                                    </div>
                                    <div class="card-body pb-0">
                                        <div id="your-chart-placeholder"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 m-0">
                                <div class="card card-xl-stretch">
                                    <div class="card-header border-0 pt-5">
                                        <h3 class="card-title flex-column">
                                            <div class="d-flex align-items-center mb-1">
                                                <span id="total_cart_cost" class="card-label fw-bold fs-2">
{{--                                                    @price($totals['total_cart_cost'], $settings)--}}
                                                </span>
{{--                                                <div class="badge badge-light-success ms-2">^2.6%</div>--}}
                                            </div>
                                            <p class="fs-5 m-0 pt-2">{{ __('Total cost amount') }}</p>
                                        </h3>
                                    </div>
                                    <div class="card-body pb-0">
                                        <div id="your-chart-placeholder"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 m-0">
                                <div class="card card-xl-stretch">
                                    <div class="card-header border-0 pt-5">
                                        <h3 class="card-title flex-column">
                                            <div class="d-flex align-items-center mb-1">
                                                <span id="total_discount" class="card-label fw-bold fs-2">
{{--                                                    @price($totals['total_discount'], $settings)--}}
                                                </span>
{{--                                                <div class="badge badge-light-success ms-2">^2.6%</div>--}}
                                            </div>
                                            <p class="fs-5 m-0 pt-2">{{ __('Total discount amount') }}</p>
                                        </h3>
                                    </div>
                                    <div class="card-body pb-0">
                                        <div id="your-chart-placeholder"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 m-0">
                                <div class="card card-xl-stretch">
                                    <div class="card-header border-0 pt-5">
                                        <h3 class="card-title flex-column">
                                            <div class="d-flex align-items-center mb-1">
                                                <span id="total_profit" class="card-label fw-bold fs-2">
{{--                                                    @price($totals['total_profit'], $settings)--}}
                                                </span>
{{--                                                <div class="badge badge-light-success ms-2">^2.6%</div>--}}
                                            </div>
                                            <p class="fs-5 m-0 pt-2">{{ __('Total profit amount') }}</p>
                                        </h3>
                                    </div>
                                    <div class="card-body pb-0">
                                        <div id="your-chart-placeholder"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 m-0">
                                <div class="card card-xl-stretch">
                                    <div class="card-header border-0 pt-5">
                                        <h3 class="card-title flex-column">
                                            <div class="d-flex align-items-center mb-1">
                                                <span id="total_tax" class="card-label fw-bold fs-2">
{{--                                                    @price($totals['total_tax'], $settings)--}}
                                                </span>
{{--                                                <div class="badge badge-light-success ms-2">^2.6%</div>--}}
                                            </div>
                                            <p class="fs-5 m-0 pt-2">{{ __('Total tax amount') }}</p>
                                        </h3>
                                    </div>
                                    <div class="card-body pb-0">
                                        <!-- Content or Chart Placeholder -->
                                        <div id="your-chart-placeholder"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 m-0">
                                <div class="card card-xl-stretch">
                                    <div class="card-header border-0 pt-5">
                                        <h3 class="card-title flex-column">
                                            <div class="d-flex align-items-center mb-1">
                                                <span id="total_payable" class="card-label fw-bold fs-2">
{{--                                                    @price($totals['total_payable'], $settings)--}}
                                                </span>
{{--                                                <div class="badge badge-light-success ms-2">^2.6%</div>--}}
                                            </div>
                                            <p class="fs-5 m-0 pt-2">{{ __('Total payable amount') }}</p>
                                        </h3>
                                    </div>
                                    <div class="card-body pb-0">
                                        <!-- Content or Chart Placeholder -->
                                        <div id="your-chart-placeholder"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-none bg-transparent mt-7">
                        <div class="card card-body pt-0">
                            <table class="table order-filter-table align-middle table-row-dashed fs-6 gy-5" id="kt_customers_table" csl="{{$settings->currency_symbol_on_left ? 1 : 0}}" sym="{{$settings->currency_symbol}}">
                                <thead>
                                    <tr class="text-start text-gray-600 fs-6 gs-0">
                                        <th class="text-gray-900 fw-bold min-w-90x pt-10 pb-10">
                                            {{ __('Receipt #') }}
                                        </th>
                                        <th class="text-gray-900 fw-bold pt-10 pb-10">{{ __('Order') }}
                                        </th>
                                        <th class="text-gray-900 fw-bold min-w-125px pt-10 pb-10">
                                            {{ __('Order type') }}
                                        </th>
                                        <th class="text-gray-900 fw-bold min-w-100px pt-10 pb-10">
                                            {{ __('POS/eKiosk') }}
                                        </th>
                                        <th class="text-gray-900 fw-bold min-w-100px pt-10 pb-10">
                                            {{ __('Paid with') }}
                                        </th>
                                        <th class="text-gray-900 fw-bold min-w-100px pt-10 pb-10">{{ __('Cost') }}
                                        </th>
                                        <th class="text-gray-900 fw-bold min-w-100px pt-10 pb-10">{{ __('Discount') }}
                                        </th>
                                        <th class="text-gray-900 fw-bold min-w-100px pt-10 pb-10">{{ __('Profit') }}
                                        </th>
                                        <th class="text-gray-900 fw-bold min-w-100px pt-10 pb-10">{{ __('Tax amount') }}
                                        </th>
                                        <th class="text-gray-900 fw-bold min-w-125px pt-10 pb-10">{{ __('Payable') }}
                                        </th>
                                        <th class="text-gray-900 fw-bold min-w-140px pt-10 pb-10">
                                            {{ __('Date created') }}
                                        </th>
                                        <th class="text-gray-900 fw-bold min-w-140px pt-10 pb-10">
                                            {{ __('Date updated') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600 py-4">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
