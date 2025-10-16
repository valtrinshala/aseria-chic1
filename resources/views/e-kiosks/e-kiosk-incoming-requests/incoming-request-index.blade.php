@extends('layouts.main-view')
@section('title', 'Incoming request')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/incoming-requests/remove-incoming-requests.js')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
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
                        <div class="border-0 px-0 py-8 d-flex justify-content-between">
                            <div class="card-title d-flex align-items-center">
                                <div class="page-title d-flex justify-content-center flex-wrap me-3">
                                    <a href="{{ route('eKiosk.index') }}"
                                       class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('e-Kiosk >') }}</a>
                                    <span
                                        class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Incoming requests') }}</span>
                                </div>
                            </div>
                            <div class="card-toolbar">
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
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_incomingRequests_table">
                                <thead>
                                <tr class="text-start text-gray-600 fs-6 gs-0">
                                    <th class="w-10px pe-2 pt-10 pb-10">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input disabled class="form-check-input" type="checkbox" data-kt-check="true"
                                                   data-kt-check-target="#kt_customers_table .form-check-input"
                                                   value="1"/>
                                        </div>
                                    </th>
                                    <th class="text-gray-900 fw-bold min-w-125px pt-10 pb-10">{{ __('eKiosk ID') }}</th>
                                    <th class="text-gray-900 fw-bold text-end min-w-125px pt-10 pb-10">{{ __('Authentication Code') }}</th>
                                    <th class="text-gray-900 fw-bold text-end min-w-70px pe-12 pt-10 pb-10">{{ __('Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                @foreach($incomingRequests as $incomingRequest)
                                    <tr>
                                        <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input disabled class="form-check-input" type="checkbox" value="{{ $incomingRequest->id }}"/>
                                            </div>
                                        </td>
                                        <td class="text-gray-800 mb-1">
                                            {{ $incomingRequest->device_id }}
                                        </td>
                                        <td class="text-dark text-end pe-8"
                                            style="-webkit-text-security: disc;">{{ $incomingRequest->authentication_code }}</td>
                                        </td>
                                        <td class="text-end">
                                            <a href="#"
                                               class="btn btn-sm btn-flex btn-center btn-active-light-primary border border-0 border-gray align-items-center"
                                               data-kt-menu-trigger="click"
                                               data-kt-menu-placement="bottom-end">{{__('Actions')}}
                                                <i class="ki-duotone ki-right fs-5 ms-1"></i></a>
                                            <div
                                                class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-state-bg-light-primary fs-7 w-auto py-4"
                                                data-kt-menu="true">
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('eKiosk.create', ['incomingRequestId' => $incomingRequest->id ]) }}"
                                                       class="menu-link px-3 text-nowrap">{{ __('Approve request') }}</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3 text-danger"
                                                       data-incomingRequest-id = {{ $incomingRequest->id }}
                                                           data-kt-customer-table-filter="delete_row">{{ __('Remove request') }}</a>
                                                </div>
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
