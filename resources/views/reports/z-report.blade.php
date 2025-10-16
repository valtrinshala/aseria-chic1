@extends('layouts.main-view')
@section('title', 'Z report')
@section('setup-script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    @vite('resources/assets/js/custom/apps/reports/z-report-list.js')
@endsection
@section('page-style')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0 p-0">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="shadow-none bg-transparent border-0">
                        <div class="border-0 px-0 py-7 d-flex justify-content-between">
                            <div class="card-title d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <g id="orders-active" transform="translate(-28.416 -671.188)">
                                        <rect id="Rectangle_96" data-name="Rectangle 96" width="24" height="24"
                                              transform="translate(28.416 671.188)" fill="none"/>
                                        <path id="Subtraction_12" data-name="Subtraction 12"
                                              d="M-3100.737-4229.812h0l-1.848-1.6-1.849,1.6-1.912-1.652-1.911,1.652-1.866-1.613-1.866,1.613-1.912-1.652-1.911,1.652-1.911-1.652-1.86,1.652v-22.062a1.873,1.873,0,0,1,.572-1.366,1.872,1.872,0,0,1,1.365-.572h18.959a1.876,1.876,0,0,1,1.365.572,1.876,1.876,0,0,1,.572,1.366v22.06l-2.074-1.65-1.911,1.652Zm-14.969-8.551v1.478h15.076v-1.478Zm0-5.2v1.478h15.076v-1.478Zm0-5.171v1.478h15.076v-1.478Z"
                                              transform="translate(3148.584 4925)" fill="#5d4bdf"/>
                                    </g>
                                </svg>

                                <span
                                    class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 p-4">
                                    {{ __('Z report') }}</span>
                            </div>
                            <div class="card-toolbar d-flex">
                                <div class="d-flex align-items-center gap-2 gap-lg-0">
                                    <div class="d-flex align-items-center position-relative my-1 ms-5 me-5">
                                        <div class="input-group w-275px">
                                            <input
                                                class="form-control form-control-solid rounded rounded-end-0 bg-light"
                                                placeholder="{{__("Select starting & ending date")}}"
                                                id="kt_ecommerce_sales_flatpickr"/>
                                            <button class="btn btn-icon btn-light"
                                                    id="kt_ecommerce_sales_flatpickr_clear">
                                                <i class="ki-duotone ki-cross fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </button>
                                        </div>
                                    </div>
                                    @include('settings.search', [
                                        'label' => __('Search reports by name, or report date'),
                                    ])
                                    <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                                        <a href="{{ url(Request::url()) }}" class="btn btn-primary h-45px">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                 class="ms-2" viewBox="0 0 24 24" fill="#ffffff"
                                                 style="margin-right: 5px;">
                                                <g id="import-export" transform="translate(-8677 -6517)">
                                                    <rect id="Rectangle_95" data-name="Rectangle 95" width="24"
                                                          height="24" transform="translate(8677 6517)" fill="none"/>
                                                    <path id="cached_FILL0_wght300_GRAD0_opsz48"
                                                          d="M67.488-770.384a8.337,8.337,0,0,1-6.187-2.654,8.774,8.774,0,0,1-2.587-6.346v-1.72l-2.411,2.49-.84-.864,3.892-4.013,3.9,4.013-.84.864L60-781.1v1.72a7.5,7.5,0,0,0,2.2,5.428,7.127,7.127,0,0,0,5.295,2.254,7.841,7.841,0,0,0,1.488-.139,6.873,6.873,0,0,0,1.362-.408l.921.947a7.859,7.859,0,0,1-1.857.7A8.392,8.392,0,0,1,67.488-770.384Zm8.072-4.951-3.9-4.013.869-.882,2.393,2.472v-1.626a7.5,7.5,0,0,0-2.2-5.428,7.115,7.115,0,0,0-5.284-2.255,7.625,7.625,0,0,0-1.493.143,9.639,9.639,0,0,0-1.367.375l-.91-.947a7.219,7.219,0,0,1,1.851-.685,9.044,9.044,0,0,1,1.919-.2,8.328,8.328,0,0,1,6.182,2.649,8.778,8.778,0,0,1,2.581,6.351v1.662l2.422-2.479.84.853Z"
                                                          transform="translate(8621.537 7308.384)" fill="#ffffff"/>
                                                </g>
                                            </svg>
                                        </a>
                                    </div>
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
                            <table class="table align-middle z-report-filter-table table-row-dashed fs-6 gy-5"
                                   id="kt_customers_table" csl="{{$settings->currency_symbol_on_left ? 1 : 0}}" sym="{{$settings->currency_symbol}}">
                                <thead>
                                <tr class="text-start text-gray-600 fs-6 gs-0">
                                    {{--                                        <th class="w-10px pe-2 pt-10 pb-10">--}}
                                    {{--                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">--}}
                                    {{--                                                <input class="form-check-input" type="checkbox" data-kt-check="true"--}}
                                    {{--                                                    data-kt-check-target="#kt_customers_table .form-check-input"--}}
                                    {{--                                                    value="1" />--}}
                                    {{--                                            </div>--}}
                                    {{--                                        </th>--}}
                                    <th class="text-gray-900 fw-bold pt-10 pb-10">
                                        {{ __('Report No.') }}
                                    </th>
                                    <th class="text-gray-900 fw-bold min-w-125px pt-10 pb-10">
                                        {{ __('Cash register') }}
                                    </th>
                                    <th class="text-gray-900 fw-bold min-w-125px pt-10 pb-10">
                                        {{ __('Cash register ID') }}
                                    </th>
                                    <th class="text-gray-900 fw-bold min-w-125px pt-10 pb-10">
                                        {{ __('Location') }}
                                    </th>
                                    <th class="text-gray-900 fw-bold min-w-125px pt-10 pb-10">
                                        {{ __('From ') }}
                                    </th>
                                    <th class="text-gray-900 fw-bold min-w-125px pt-10 pb-10">
                                        {{ __('To') }}
                                    </th>
                                    <th class="text-gray-900 fw-bold min-w-125px pt-10 pb-10">
                                        {{ __('Total sales') }}
                                    </th>
                                    <th class="text-gray-900 fw-bold min-w-125px pt-10 pb-10">
                                        {{ __('Status') }}
                                    </th>
                                    <th class="text-end text-gray-900 pe-12 fw-bold min-w-70px pt-10 pb-10">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                @foreach ($zReports as $zReport)
                                    <tr>
                                        <td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                            {{ (int)$zReport->report_number }}
                                        </td>
                                        <td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                            {{ optional($zReport->cashRegister)->name }}
                                        </td>
                                        <td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                {{ optional($zReport->cashRegister)->key }}
                                        </td>
                                        <td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                {{ $zReport->location?->name }}
                                        </td>
                                        <td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                            {{ $zReport->start_z_report }}</td>
                                        <td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                            {{ $zReport->end_z_report }}</td>

                                        <td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                            {{ (int)$zReport->total_sales }}
                                        </td>
                                        <td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                            <div
                                                class="badge {{ $zReport->end_z_report ? 'badge-light-danger' : 'badge-light-success' }}">
                                                {{ $zReport->end_z_report ? __('Closed') : __('Opened') }}
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('print.zReport', $zReport->id) }}"
                                               class="btn btn-sm btn-light btn-flex @disabled(!$zReport->end_z_report) btn-center btn-active-light-primary border-0 bg-light-primary">{{ __('Download') }}
                                                <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
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
