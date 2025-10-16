@extends('layouts.main-view')
@section('title', 'Customers')
@section('setup-script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    @vite('resources/assets/js/custom/apps/customers/list/list.js')
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 24 24">
                                    <g id="customers-active" transform="translate(-8719 -6517)">
                                        <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24"
                                            transform="translate(8719 6517)" fill="none" />
                                        <path id="manage_accounts_FILL0_wght300_GRAD0_opsz48"
                                            d="M109.726-752.934a4.193,4.193,0,0,1-3.087-1.208,4.176,4.176,0,0,1-1.215-3.093,4.182,4.182,0,0,1,1.215-3.087,4.182,4.182,0,0,1,3.087-1.215,4.182,4.182,0,0,1,3.087,1.215,4.182,4.182,0,0,1,1.215,3.087,4.176,4.176,0,0,1-1.215,3.093A4.193,4.193,0,0,1,109.726-752.934ZM100-743.028v-2.461a3.167,3.167,0,0,1,.511-1.793,3.471,3.471,0,0,1,1.481-1.219,23.115,23.115,0,0,1,3.935-1.391,16,16,0,0,1,3.8-.441h.323q.173,0,.323-.012a4.55,4.55,0,0,0-.217.7q-.072.332-.125.785h-.3a16.567,16.567,0,0,0-3.636.38,14.767,14.767,0,0,0-3.481,1.3,2.017,2.017,0,0,0-.878.754,1.846,1.846,0,0,0-.259.944v.99h8.57a5.071,5.071,0,0,0,.277.785,4.266,4.266,0,0,0,.389.686Zm18.28,1.052-.237-1.865a4.575,4.575,0,0,1-1.162-.451,3.68,3.68,0,0,1-.962-.741l-1.571.426-.574-.95,1.361-1.2a3.064,3.064,0,0,1-.127-.96,3.186,3.186,0,0,1,.127-.972l-1.349-1.214.573-.95,1.558.439a3.249,3.249,0,0,1,.956-.747,4.82,4.82,0,0,1,1.168-.445l.237-1.865h1.227l.237,1.865a4.868,4.868,0,0,1,1.156.445,3.544,3.544,0,0,1,.969.734l1.558-.426.561.938L122.64-748.7a3.042,3.042,0,0,1,.14.981,2.893,2.893,0,0,1-.14.964l1.361,1.2-.561.95-1.571-.426a3.894,3.894,0,0,1-.975.741,4.616,4.616,0,0,1-1.15.451l-.237,1.865ZM110.042-744.5Z"
                                            transform="translate(8618.999 7280.756)" fill="#5d4bdf" />
                                    </g>
                                </svg>

                                <span
                                class="px-4 page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0">
                                    {{ __('Customers') }}</span>
                            </div>
                            <div class="card-toolbar d-flex">
                                <div class="d-flex align-items-center gap-2 gap-lg-0">
                                    @include('settings.search', [
                                        'label' => __('Search customer by name or customer ID'),
                                    ])
                                    <div class="d-flex justify-content-end w-125px"
                                        data-kt-customer-table-toolbar="base">
                                        <a href="{{ route('customer.create') }}"
                                            class="btn btn-primary w-100 border-0">{{ __('Add new') }}</a>
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
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_customers_table">
                                <thead>
                                    <tr class="text-start text-gray-600 fs-6 gs-0">
                                        <th class="w-10px pe-2 pt-10 pb-10">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                    data-kt-check-target="#kt_customers_table .form-check-input"
                                                    value="1" />
                                            </div>
                                        </th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Customer') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Date created') }}</th>
                                        <th class="text-gray-900 fw-bold pe-12 text-end min-w-70px py-10">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach ($customers as $customer)
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $customer->id }}" />
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">

                                                    <a href="{{ route('customer.edit', ['customer' => $customer->id]) }}"
                                                        class="text-gray-800 text-hover-primary mb-1">{{ $customer->name }}</a>

                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    {{ optional($customer->created_at)->format('d.m.Y') ?? '' }}
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="#"
                                                    class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary border-0"
                                                    data-kt-menu-trigger="click"
                                                    data-kt-menu-placement="bottom-end">{{ __('Actions') }}
                                                    <i class="ki-duotone ki-right fs-5 ms-1"></i></a>
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                    data-kt-menu="true">
                                                    <div class="menu-item px-3">
                                                        <a href="{{ route('customer.edit', ['customer' => $customer->id]) }}"
                                                            class="menu-link px-3">{{ __('View') }}</a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3 text-danger"
                                                            data-customer-id={{ $customer->id }}
                                                            data-kt-customer-table-filter="delete_row">{{ __('Delete') }}</a>
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
    @endsection
