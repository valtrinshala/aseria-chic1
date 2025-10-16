@extends('layouts.main-view')
@section('title', 'Dashboard')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/dashboard-charts/chart.js')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0 p-0">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="shadow-none bg-transparent border-0">
                        <div class="border-0 px-0 py-8 d-flex justify-content-between">
                            <div class="card-title d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 24 24">
                                    <g id="dashboard-active" transform="translate(-8719 -6517)">
                                        <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24"
                                            transform="translate(8719 6517)" fill="none" />
                                        <path id="dashboard_FILL0_wght300_GRAD0_opsz48"
                                            d="M153.508-811.461V-820H164v8.538ZM140-807.551V-820h10.493v12.448ZM153.508-796v-12.448H164V-796ZM140-796v-8.538h10.493V-796Z"
                                            transform="translate(8578.999 7336.999)" fill="#5d4bdf" />
                                    </g>
                                </svg>
                                <h1
                                    class="px-4 page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0">
                                    {{ __('Dashboard') }}</h1>
                            </div>
                            <div class="card-toolbar">
                                <div class="d-flex align-items-center gap-2 gap-lg-5">
                                    <button type="button"
                                        class="btn btn-light d-flex align-items-center gap-2 px-3"
                                        data-kt-menu-trigger="{default: 'click', lg: 'click'}"
                                        data-kt-menu-placement="bottom-end">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 24 24">
                                            <path id="calendar_today_FILL0_wght300_GRAD0_opsz48"
                                                d="M141.863-840.614a1.815,1.815,0,0,1-1.311-.536,1.718,1.718,0,0,1-.551-1.275v-18.181a1.718,1.718,0,0,1,.551-1.275,1.815,1.815,0,0,1,1.311-.536h3.411v-2.2H146.9v2.2h11.28v-2.2h1.589v2.2h2.371a1.815,1.815,0,0,1,1.311.536,1.718,1.718,0,0,1,.551,1.275v18.181a1.718,1.718,0,0,1-.551,1.275,1.815,1.815,0,0,1-1.311.536Zm0-1.425h20.276a.384.384,0,0,0,.273-.121.365.365,0,0,0,.124-.266V-854.9H141.466v12.471a.365.365,0,0,0,.124.266A.384.384,0,0,0,141.863-842.039Zm-.4-14.282h21.071v-4.286a.365.365,0,0,0-.124-.266.384.384,0,0,0-.273-.121H141.863a.384.384,0,0,0-.273.121.365.365,0,0,0-.124.266Zm0,0v0Z"
                                                transform="translate(-140.001 864.614)" fill="#264653" />
                                        </svg>
                                        <span class="btn-current">
                                            {{ __('Current Month') }}
                                        </span>
                                        <span class="me-3"></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                            viewBox="0 0 11 6">
                                            <path id="Path_6" data-name="Path 6"
                                                d="M.251.263a.858.858,0,0,1,1.168,0l4.09,3.879L9.6.263a.858.858,0,0,1,1.168,0,.755.755,0,0,1,0,1.108L6.093,5.8a.858.858,0,0,1-1.168,0L.251,1.371a.755.755,0,0,1,0-1.108Z"
                                                transform="translate(-0.009 -0.033)" fill="#264653"></path>
                                        </svg>
                                    </button>
                                    <div id="show-hidden-click" class="menu menu-sub menu-sub-dropdown menu-column w-auto"
                                        data-kt-menu="true">
                                        <div class="card card-body w-auto">
                                            <div class="menu-item dr-down">
                                                <div class="p-2">
                                                    <a href="javascript:void(0)">{{ __('All') }}</a>
                                                </div>
                                                <div class="p-2">
                                                    <a id="year" data-name="{{ __('Last Year') }}"
                                                        data-current-name="{{ __('Current Year') }}" data-key="year"
                                                        href="javascript:void(0)">{{ __('Current Year') }}</a>
                                                </div>
                                                <div class="p-2">
                                                    <a id="last_month" data-name="{{ __('Last Month') }}"
                                                        data-current-name="{{ __('Current Month') }}" data-key="month"
                                                        href="javascript:void(0)">{{ __('Current Month') }}</a>
                                                </div>
                                                <div class="p-2">
                                                    <a data-name="{{ __('Yesterday') }}"
                                                        data-current-name="{{ __('Today') }}" data-key="day"
                                                        href="javascript:void(0)">{{ __('Today') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                                        <a href="{{ url(Request::url()) }}" class="btn btn-primary h-45px">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                class="ms-2" viewBox="0 0 24 24" fill="#ffffff"
                                                style="margin-right: 5px;">
                                                <g id="import-export" transform="translate(-8677 -6517)">
                                                    <rect id="Rectangle_95" data-name="Rectangle 95" width="24"
                                                        height="24" transform="translate(8677 6517)" fill="none" />
                                                    <path id="cached_FILL0_wght300_GRAD0_opsz48"
                                                        d="M67.488-770.384a8.337,8.337,0,0,1-6.187-2.654,8.774,8.774,0,0,1-2.587-6.346v-1.72l-2.411,2.49-.84-.864,3.892-4.013,3.9,4.013-.84.864L60-781.1v1.72a7.5,7.5,0,0,0,2.2,5.428,7.127,7.127,0,0,0,5.295,2.254,7.841,7.841,0,0,0,1.488-.139,6.873,6.873,0,0,0,1.362-.408l.921.947a7.859,7.859,0,0,1-1.857.7A8.392,8.392,0,0,1,67.488-770.384Zm8.072-4.951-3.9-4.013.869-.882,2.393,2.472v-1.626a7.5,7.5,0,0,0-2.2-5.428,7.115,7.115,0,0,0-5.284-2.255,7.625,7.625,0,0,0-1.493.143,9.639,9.639,0,0,0-1.367.375l-.91-.947a7.219,7.219,0,0,1,1.851-.685,9.044,9.044,0,0,1,1.919-.2,8.328,8.328,0,0,1,6.182,2.649,8.778,8.778,0,0,1,2.581,6.351v1.662l2.422-2.479.84.853Z"
                                                        transform="translate(8621.537 7308.384)" fill="#ffffff" />
                                                </g>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="currency" data-currency="{{ $settings->currency_symbol }}"
                        data-direction="{{ $settings->currency_symbol_on_left ? 'start' : 'end' }}" class="card-body">
                        <div class="row g-5 g-xl-8 fw-bold mb-xl-6">
                            @php
                                $className = [
                                    ['sale', 'last_sale', 'percentage_sale'],
                                    ['cost', 'last_cost', 'percentage_cost'],
                                    ['discount', 'last_discount', 'percentage_discount'],
                                    ['profit', 'last_profit', 'percentage_profit'],
                                    ['tax', 'last_tax', 'percentage_tax'],
                                    ['payable', 'last_payable', 'percentage_payable'],
                                ];
                                $totalSaleAmounts = [
                                    __('Total sale amount'),
                                    __('Total cost amount'),
                                    __('Total discount amount'),
                                    __('Total profit amount'),
                                    __('Total tax amount'),
                                    __('Total payable amount'),
                                ];
                            @endphp
                            <span id="title-for-js" class="d-none"
                                data-title="{{ json_encode($totalSaleAmounts) }}"></span>
                            @foreach ($totalSaleAmounts as $key => $cardName)
                                <div class="col-xl-6">
                                    <div class="card">
                                        <div
                                            class="card-header border-0 pt-5 d-flex justify-content-between align-items-start">
                                            <h3 class="w-100 card-title flex-column ">
                                                <span class="w-100 card-label fw-bold fs-2 mb-1">
                                                    <div class="d-flex justify-content-between flex-wrap">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div id="{{ $className[$key][0] }}">
                                                            </div>
                                                            <div id="{{ $className[$key][2] }}">
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <div id="{{ $className[$key][1] }}" class="fs-7">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="fw-semibold fs-7">{{ __($totalSaleAmounts[$key]) }}</span>
                                                    </div>
                                                </span>
                                            </h3>
                                        </div>
                                        <div class="card-body pb-0">
                                            <div class="h-150px" id="kt_charts_widget_{{ $key + 1 }}_chart"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-xl-12">
                            <div class="card card-xl-stretch mb-xl-8">
                                <div class="card-header border-0 pt-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bold fs-1 mb-1">{{ __('Annual Graphic View') }}</span>
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="h-350px" id="kt_charts_widget_7_chart"
                                        style="height: 350px; min-height: 365px;"
                                        data-value-payable-after-all="{{ json_encode($data[0]) }}"
                                        data-value-cost-after-all="{{ json_encode($data[1]) }}"
                                        data-sale="{{ __('Sale') }}" data-cost="{{ __('Cost') }}"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
