@extends('layouts.main-view')
@section('title', 'Users')
@section('setup-script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    @vite('resources/assets/js/custom/apps/users/list/list.js')
@endsection
@section('page-style')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css">
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
                                    <g id="users-active" transform="translate(-8719 -6517)">
                                        <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24"
                                            transform="translate(8719 6517)" fill="none" />
                                        <path id="group_FILL0_wght300_GRAD0_opsz48"
                                            d="M70.31-741.593v-2.224a2.866,2.866,0,0,1,.477-1.635,3.027,3.027,0,0,1,1.334-1.088,23.539,23.539,0,0,1,3.475-1.245,13.906,13.906,0,0,1,3.507-.41,13.733,13.733,0,0,1,3.492.41,24.233,24.233,0,0,1,3.472,1.245,3.1,3.1,0,0,1,1.332,1.088,2.828,2.828,0,0,1,.486,1.635v2.224Zm19.489,0v-2.19a4.454,4.454,0,0,0-.728-2.609,5.7,5.7,0,0,0-1.9-1.71,23.928,23.928,0,0,1,2.963.609,12.413,12.413,0,0,1,2.348.871,3.88,3.88,0,0,1,1.331,1.2,2.841,2.841,0,0,1,.5,1.641v2.19Zm-10.7-8.94a3.786,3.786,0,0,1-2.795-1.1,3.786,3.786,0,0,1-1.1-2.795,3.767,3.767,0,0,1,1.1-2.789,3.8,3.8,0,0,1,2.795-1.092,3.777,3.777,0,0,1,2.789,1.092,3.777,3.777,0,0,1,1.092,2.789,3.8,3.8,0,0,1-1.092,2.795A3.767,3.767,0,0,1,79.1-750.533Zm9.255-3.892a3.8,3.8,0,0,1-1.092,2.795,3.772,3.772,0,0,1-2.793,1.1,3.6,3.6,0,0,1-.426-.027,1.891,1.891,0,0,1-.425-.1,4.562,4.562,0,0,0,.953-1.644,6.569,6.569,0,0,0,.325-2.118,5.907,5.907,0,0,0-.338-2.068,6.626,6.626,0,0,0-.94-1.7q.187-.043.425-.079a2.861,2.861,0,0,1,.425-.036,3.784,3.784,0,0,1,2.795,1.092A3.777,3.777,0,0,1,88.357-754.425Z"
                                            transform="translate(8648.69 7278.949)" fill="#5d4bdf" />
                                    </g>
                                </svg>
                                <span
                                class="px-4 page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0">
                                    {{ __('Users') }}</span>
                            </div>
                            <div class="card-toolbar d-flex">
                                <div class="d-flex align-items-center gap-2 gap-lg-0">
                                    @include('settings.search', [
                                        'label' => 'Search users by name or user ID',
                                    ])
                                    <div class="d-flex justify-content-end w-125px"
                                        data-kt-customer-table-toolbar="base">
                                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true"
                                            id="kt-toolbar-filter">
                                            <div class="px-7 py-5">
                                                <div class="fs-4 text-gray-900 fw-bold">{{ __('Filter Options') }}</div>
                                            </div>
                                            <div class="separator border-gray-200"></div>
                                        </div>
                                        <a href="{{ route('user.create') }}"
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
                                        <th class="w-10px pe-2 w-10px pe-2 pt-10 pb-10">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                    data-kt-check-target="#kt_customers_table .form-check-input"
                                                    value="1" />
                                            </div>
                                        </th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Users name') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Email') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Role') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Status') }}</th>
                                        <th class="text-gray-900 fw-bold pe-12 text-end min-w-70px py-10">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $user->id }}" />
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    <a href="{{ route('user.edit', ['user' => $user->id]) }}"
                                                        class="text-gray-800 text-hover-primary mb-1">{{ $user->name }}</a>
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">

                                                    {{ $user->email }}
                                                </div>

                                            </td>
                                            <td>
                                                <div
                                                    class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    {{ $user->userRole->name }} </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="badge {{ $user->status ? 'badge-light-success' : 'badge-light-danger' }}">
                                                    {{ $user->status ? __('Active') : __('Inactive') }}
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
                                                        <a href="{{ route('user.edit', ['user' => $user->id]) }}"
                                                            class="menu-link px-3">{{ __('View') }}</a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3 text-danger"
                                                            data-user-id={{ $user->id }}
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
    </div>
@endsection
