@extends('layouts.main-view')
@section('title', 'Overall report')
@section('page-script')
    @vite('resources/assets/js/custom/apps/reports/tax-report-chart.js')
@endsection
@section('content')
    <script>
        window.taxData = {
            "taxForMonth": {!! json_encode($taxes) !!}
        };
    </script>
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar py-3 my-4 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24.001">
                            <g id="tax-report-active" transform="translate(-8719 -6517)">
                                <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24"
                                      transform="translate(8719 6517)" fill="none"></rect>
                                <path id="Subtraction_13" data-name="Subtraction 13"
                                      d="M5763.964,1751h-19.928a2.053,2.053,0,0,1-2.036-2.037v-19.926a2.053,2.053,0,0,1,2.036-2.037h13.534l8.429,8.431v13.533a2.056,2.056,0,0,1-2.036,2.037Zm-16.692-7.529v1.6h13.455v-1.6Zm0-5.272v1.6h13.455v-1.6Zm0-5.273v1.6h8.887v-1.6Z"
                                      transform="translate(2977 4790)" fill="#5d4bdf"></path>
                            </g>
                        </svg>
                        <span
                            class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Tax Report') }}</span>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-7">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                 role="tab-panel">
                                <div class="d-flex flex-column gap-7 gap-lg-7">
                                    <div class="card card-flush overflow-hidden h-xl-100">
                                        <div class="card-header pt-7 mb-2">
                                            <h3 class="card-title align-items-start flex-column">
                                                <span class="card-label fw-bold fs-1 mb-1">{{ __('Annual TAX Report') }}</span>
                                            </h3>
                                            <div class="card-toolbar">
                                                <div data-kt-daterangepicker="true" data-kt-daterangepicker-opens="left"
                                                     class="btn btn-sm btn-light d-flex align-items-center px-4"
                                                     data-kt-initialized="1">
                                                    <div class="text-gray-600 fw-bold">{{ $currentYear }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="card-body d-flex justify-content-between flex-column pt-0 pb-1 px-0">

                                            <div id="kt_charts_widget_26" class="min-h-auto ps-4 pe-6" data-kt-chart-info="Transactions" style="height: 500px"></div>
                                        </div>
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
