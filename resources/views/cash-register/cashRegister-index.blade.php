@extends('layouts.main-view')
@section('title', 'Cash registers')
@section('setup-script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    @vite('resources/assets/js/custom/apps/cash-registers/list/list.js')
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <g id="icon-active" transform="translate(-8677 -6517)">
                                      <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24" transform="translate(8677 6517)" fill="none"/>
                                      <g id="cash-register-svgrepo-com" transform="translate(8671.435 6517)">
                                        <path id="add_business_FILL0_wght300_GRAD0_opsz48" d="M89.313-768v-3.635H85.8V-773h3.514v-3.616h1.329V-773h3.514v1.368H90.641V-768Zm-17.637-3.334v-7.606h-1.52v-1.368l1.345-6.076H88.349l1.374,6.18v1.263h-1.5v4.813H86.892v-4.813h-6.2v7.606ZM73-772.7h6.358v-6.238H73Zm-1.5-15.931V-790H88.367v1.368Z" transform="translate(-64.59 790.999)" fill="#5d4bdf"/>
                                      </g>
                                    </g>
                                  </svg>
                                <span
                                    class="px-4 page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0">
                                    {{ __('Cash registers') }}</span>
                            </div>
                            <div class="card-toolbar d-flex">
                                <div class="d-flex align-items-center gap-2 gap-lg-0">
                                    @include('settings.search', [
                                        'label' => 'Search cash registers by name, or cash register ID',
                                    ])
                                    <div class="d-flex justify-content-end w-125px" data-kt-customer-table-toolbar="base">
                                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true"
                                            id="kt-toolbar-filter">
                                            <div class="px-7 py-5">
                                                <div class="fs-4 text-gray-900 fw-bold">{{ __('Filter Options') }}</div>
                                            </div>

                                        </div>
                                        <a href="{{ route('cashRegister.create') }}"
                                            class="btn btn-primary w-100 border-0">{{ __('Add new') }}</a>
                                    </div>
                                </div>
                                {{-- <div class="d-flex justify-content-end align-items-center d-none"
                                    data-kt-customer-table-toolbar="selected">
                                    <div class="fw-bold me-5">
                                        <span class="me-2"
                                            data-kt-customer-table-select="selected_count"></span>{{ __('Selected') }}
                                    </div>
                                    <button type="button" class="btn btn-danger"
                                        data-kt-customer-table-select="delete_selected">{{ __('Delete Selected') }}
                                    </button>
                                </div> --}}
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
                                                    {{-- data-kt-check-target="#kt_customers_table .form-check-input" --}}
                                                    value="1" />
                                            </div>
                                        </th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">
                                            {{ __('Cash register name') }}</th>
                                            <th class="text-gray-900 fw-bold min-w-125px py-10">
                                            {{ __('Created by') }}</th>
                                            <th class="text-gray-900 fw-bold min-w-125px py-10">
                                            {{ __('Key') }}</th>
                                            <th class="text-gray-900 fw-bold min-w-125px py-10">
                                            {{ __('Created Date') }}</th>
                                            <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Location') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">
                                            {{ __('Status') }}</th>
                                            <th class="text-gray-900 fw-bold pe-12 text-end min-w-70px py-10">
                                            {{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach ($cashRegisters as $cashRegister)
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $cashRegister->id }}" />
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    <a href="{{ route('cashRegister.edit', ['cashRegister' => $cashRegister->id]) }}"
                                                        class="text-gray-800 text-hover-primary mb-1">{{ $cashRegister->name }}</a>
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    <a href="{{ route('user.edit', ['user' => $user->id]) }}"
                                                        class="text-gray-800 text-hover-primary mb-1">{{ $cashRegister->user?->name }}</a>
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    {{ $cashRegister->key }}
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    {{ \Carbon\Carbon::parse($cashRegister->created_at)->format('d.m.Y') }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    {{ $cashRegister->location?->name }}
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="badge text-end {{ $cashRegister->status ? 'badge-light-success' : 'badge-light-danger' }}">
                                                    {{ $cashRegister->status ? __('Active') : __('Inactive') }}
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="#"
                                                    class="btn btn-sm btn-light btn-flex btn-center border-0 btn-active-light-primary"
                                                    data-kt-menu-trigger="click"
                                                    data-kt-menu-placement="bottom-end">{{ __('Actions') }}
                                                    <i class="ki-duotone ki-right fs-5 ms-1"></i></a>

                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                    data-kt-menu="true">
                                                    <div class="menu-item px-3">
                                                        <a href="{{ route('cashRegister.edit', ['cashRegister' => $cashRegister->id]) }}"
                                                            class="menu-link px-3">{{ __('View') }}</a>
                                                    </div>
                                                    {{--                                                    <div class="menu-item px-3"> --}}
                                                    {{--                                                        <a href="#" class="menu-link px-3 text-danger" --}}
                                                    {{--                                                            data-cashRegister-id={{ $cashRegister->id }} --}}
                                                    {{--                                                            data-kt-customer-table-filter="delete_row">{{ __('Delete') }}</a> --}}
                                                    {{--                                                    </div> --}}
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
