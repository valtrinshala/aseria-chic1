@extends('layouts.main-view')
@section('title', 'Tables')
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0 p-0">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="shadow-none bg-transparent border-0">
                        <div class="border-0 px-0 py-8 d-flex justify-content-between">
                            <div class="card-title d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 24 24">
                                    <g id="service_tables-active" data-name="service tables-active"
                                        transform="translate(-8719 -6517)">
                                        <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24"
                                            transform="translate(8719 6517)" fill="none" />
                                        <path id="Subtraction_7" data-name="Subtraction 7"
                                            d="M13.09-179a1.422,1.422,0,0,1-1.038-.434L.427-191.066a1.371,1.371,0,0,1-.335-.511A1.725,1.725,0,0,1,0-192.139v-9.429a1.39,1.39,0,0,1,.409-1.022A1.392,1.392,0,0,1,1.43-203h9.425a1.977,1.977,0,0,1,.569.086,1.313,1.313,0,0,1,.529.317l11.576,11.581A1.519,1.519,0,0,1,24-189.933a1.452,1.452,0,0,1-.447,1.069l-9.425,9.43A1.425,1.425,0,0,1,13.09-179Zm-2.313-13.388a2.072,2.072,0,0,0-1.53.643,2.133,2.133,0,0,0-.63,1.543,2.344,2.344,0,0,0,.145.826,1.915,1.915,0,0,0,.444.688l4.122,4.2,4.122-4.2a1.788,1.788,0,0,0,.45-.688,2.446,2.446,0,0,0,.139-.826,2.145,2.145,0,0,0-.624-1.543,2.054,2.054,0,0,0-1.524-.643,2.389,2.389,0,0,0-1.261.38,5.153,5.153,0,0,0-1.3,1.27A4.942,4.942,0,0,0,12.038-192,2.414,2.414,0,0,0,10.778-192.389ZM4.7-199.71a1.347,1.347,0,0,0-.986.41,1.338,1.338,0,0,0-.409.976,1.356,1.356,0,0,0,.409.981,1.339,1.339,0,0,0,.986.416,1.326,1.326,0,0,0,.975-.416,1.356,1.356,0,0,0,.41-.981,1.338,1.338,0,0,0-.41-.976A1.334,1.334,0,0,0,4.7-199.71Z"
                                            transform="translate(8719 6720)" fill="#5d4bdf" />
                                    </g>
                                </svg>
                                <span
                                class="px-4 page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0">
                                    {{ __('Service tables') }}</span>
                            </div>
                            <div class="card-toolbar">
                                <div class="d-flex justify-content-end gap-2 gap-lg-3"
                                    data-kt-customer-table-toolbar="base">
                                    @include('settings.goback-button')
                                    <a href="{{ route('serviceTable.create') }}"
                                        class="btn btn-primary w-125px border-0">{{ __('Add new') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="background: transparent">
                        <h3 class="mt-3 mb-3">{{ __('Service tables') }}</h3>
                        <div class="mt-1 row g-5 g-xl-6">
                            @foreach ($serviceTables as $serviceTable)
                                <div class="col-xl-4 m-0">
                                    <div class="card statistics-widget-1 mb-xl-6">
                                        <div class="row">
                                            <div class="col-xl-7">
                                                <div class="card-body">
                                                    <div>
                                                        <a href="{{route('serviceTable.edit', $serviceTable->id)}}"
                                                            class="card-title fw-bold fs-4">{{ $serviceTable->title }}</a>
                                                        <p class="fs-5 m-0">
                                                            {{ date('d-m-Y, H:i:s', strtotime($serviceTable->created_at)) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-5 d-flex align-items-center justify-content-center">
                                                <div class="card-body m-0 text-end">
                                                    <a href="{{ route('serviceTable.edit', ['serviceTable' => $serviceTable->id]) }}"
                                                        class="mt-auto text-dark">{{ __('Table detalis >') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
