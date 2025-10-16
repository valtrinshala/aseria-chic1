@extends('layouts.main-view')
@section('title', 'User roles')
@section('setup-script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    @vite('resources/assets/js/custom/apps/user-roles/list/list.js')
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
                                    <g id="user-roles-active" transform="translate(-8719 -6517)">
                                        <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24"
                                            transform="translate(8719 6517)" fill="none" />
                                        <path id="group_add_FILL0_wght300_GRAD0_opsz48"
                                            d="M45.976-751.282a4.781,4.781,0,0,0,.9-1.579,6,6,0,0,0,.274-1.882,5.949,5.949,0,0,0-.274-1.877,4.8,4.8,0,0,0-.9-1.573,3.648,3.648,0,0,1,3.017.634,3.315,3.315,0,0,1,1.283,2.821,3.3,3.3,0,0,1-1.283,2.816A3.687,3.687,0,0,1,45.976-751.282Zm5.572,8.323v-2.011a4.506,4.506,0,0,0-.554-2.2,4.185,4.185,0,0,0-1.955-1.686,12.708,12.708,0,0,1,5.068,1.452,2.854,2.854,0,0,1,1.429,2.431v2.011Zm2.534-7.235v-2.545H51.538v-1.221h2.545V-756.5H55.3v2.545h2.545v1.221H55.3v2.545Zm-12.264-.974a3.459,3.459,0,0,1-2.561-1.008,3.486,3.486,0,0,1-1-2.566,3.468,3.468,0,0,1,1-2.561,3.468,3.468,0,0,1,2.561-1,3.486,3.486,0,0,1,2.566,1,3.459,3.459,0,0,1,1.008,2.561,3.476,3.476,0,0,1-1.008,2.566A3.476,3.476,0,0,1,41.819-751.168Zm-7.971,8.21V-745a2.626,2.626,0,0,1,.446-1.48A2.8,2.8,0,0,1,35.5-747.5a20.434,20.434,0,0,1,3.27-1.155,12.627,12.627,0,0,1,3.048-.366,12.463,12.463,0,0,1,3.034.366A20.965,20.965,0,0,1,48.12-747.5a2.908,2.908,0,0,1,1.21,1.019,2.572,2.572,0,0,1,.459,1.48v2.042H33.848Z"
                                            transform="translate(8685.152 7279.632)" fill="#5d4bdf" />
                                    </g>
                                </svg>
                                <span
                                    class="px-4 page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0">
                                    {{ __('Roles') }}</span>
                            </div>
                            <div class="card-toolbar d-flex">
                                <div class="d-flex align-items-center gap-2 gap-lg-0">
                                    @include('settings.search', [
                                        'label' => __('Search roles by name or user role ID'),
                                    ])
                                    <div class="d-flex justify-content-end w-125px" data-kt-customer-table-toolbar="base">
                                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true"
                                            id="kt-toolbar-filter">
                                            <div class="px-7 py-5">
                                                <div class="fs-4 text-gray-900 fw-bold">{{ __('Filter Options') }}</div>
                                            </div>
                                            <div class="separator border-gray-200"></div>
                                            <div class="px-7 py-5">
                                                <div class="d-flex justify-content-end">
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route('userRole.create') }}"
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
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __("User's role") }}
                                        </th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">
                                            {{ __('Date created') }}</th>
                                            <th class="text-gray-900 fw-bold min-w-125px py-10">
                                            {{ __('Users assigned') }}</th>
                                            <th class="text-gray-900 fw-bold pe-12 text-end min-w-70px py-10">
                                            {{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach ($userRoles as $userRole)
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $userRole->id }}" />
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">

                                                    <a href="{{ route('userRole.edit', ['userRole' => $userRole->id]) }}"
                                                        class="text-gray-800 text-hover-primary mb-1">{{ $userRole->name }}</a>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    {{ optional($userRole->created_at)->format('d.m.Y') ?? '' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">

                                                    {{ count($userRole->users) }} {{ __('users') }}
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="#"
                                                    class="btn btn-sm btn-flex btn-center btn-active-light-primary border-0"
                                                    data-kt-menu-trigger="click"
                                                    data-kt-menu-placement="bottom-end">{{ __('Actions') }}
                                                    <i class="ki-duotone ki-right fs-5 ms-1"></i></a>
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                    data-kt-menu="true">
                                                    <div class="menu-item px-3">
                                                        <a href="{{ route('userRole.edit', ['userRole' => $userRole->id]) }}"
                                                            class="menu-link px-3">{{ __('View') }}</a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3 text-danger"
                                                            data-userRole-id={{ $userRole->id }}
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
