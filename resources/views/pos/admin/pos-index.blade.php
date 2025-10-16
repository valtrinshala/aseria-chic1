@extends('layouts.main-view')
@section('title', __('Pos'))
@section('setup-script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    @vite('resources/assets/js/custom/apps/pos/admin/list/list.js')
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
                                    <g id="users-active" transform="translate(-8719 -6517)">
                                      <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24" transform="translate(8719 6517)" fill="none"/>
                                      <path id="Path_168" data-name="Path 168" d="M21.353,24H.647A.642.642,0,0,1,0,23.364V.636A.642.642,0,0,1,.647,0H21.353A.642.642,0,0,1,22,.636V23.364A.642.642,0,0,1,21.353,24Zm-8.331-4.053a.529.529,0,0,0-.4.2.648.648,0,0,0,0,.866.529.529,0,0,0,.4.2H14.64a.529.529,0,0,0,.4-.2.648.648,0,0,0,0-.866.529.529,0,0,0-.4-.2Zm-5.662,0a.529.529,0,0,0-.4.2.648.648,0,0,0,0,.866.529.529,0,0,0,.4.2H8.978a.529.529,0,0,0,.4-.2.648.648,0,0,0,0-.866.529.529,0,0,0-.4-.2ZM4.044,3.974V17.245H17.956V3.974Z" transform="translate(8720 6517)" fill="#5d4bdf"/>
                                    </g>
                                  </svg>
                                <span class="px-4 page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0">
                                    {{ __('Pos') }}</span>
                            </div>
                            <div class="card-toolbar d-flex h-45px">
                                @include('settings.search', ['label' =>'Search pos by name or pos ID'])
                                <div class="d-flex justify-content-end w-125px" data-kt-customer-table-toolbar="base">
                                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true"
                                         id="kt-toolbar-filter">
                                        <div class="separator border-gray-200"></div>
                                    </div>
                                    <a href="{{ route('devices-pos.create') }}" class="btn btn-primary w-100 border-0">{{ __('Add new') }}</a>
                                </div>
                                <div class="d-flex justify-content-end align-items-center d-none"
                                     data-kt-customer-table-toolbar="selected">
                                    <div class="fw-bold me-5">
                                        <span class="me-2" data-kt-customer-table-select="selected_count"></span>{{__('Selected')}}
                                    </div>
                                    <button type="button" class="btn btn-danger"
                                            data-kt-customer-table-select="delete_selected">{{__('Delete Selected')}}
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
                                                   value="1"/>
                                        </div>
                                    </th>
                                    <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Pos name') }}</th>
                                    <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Created by') }}</th>
                                    <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Pos ID') }}</th>
                                    <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Authentication Code') }}</th>
                                    <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Created date') }}</th>
                                    <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Location') }}</th>
                                    <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Status') }}</th>
                                    <th class="text-gray-900 fw-bold pe-12 text-end min-w-70px py-10">{{ __('Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                @foreach($poses as $pos)
                                    <tr>
                                        <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="{{ $pos->id }}"/>
                                            </div>
                                        </td>

                                        <td>
                                            <a href="{{ route('devices-pos.edit', ['devices_po' => $pos->id ]) }}"
                                               class="text-gray-800 text-hover-primary mb-1 fs-10.5 flex-column justify-content-center my-0">{{ $pos->name }}</a>
                                        </td>
                                        <td>
                                            <div
                                                class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                <a href="{{ route('user.edit', $pos->user?->id ?? '') }}"
                                                    class="text-gray-800 text-hover-primary mb-1">{{ $pos->user?->name }}</a>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-gray-800 mb-1 fs-10.5 flex-column justify-content-center my-0">{{ $pos->pos_id }}</div>
                                        </td>
                                        <td  class="text-gray-800 fs-10.5 flex-column justify-content-center my-0"
                                        style="-webkit-text-security: disc;">{{ substr($pos->authentication_code, 0, 20) }}</td>
                                        <td>
                                            <div
                                            class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                {{ \Carbon\Carbon::parse($pos->created_at)->format('d.m.Y') }}
                                            </div>
                                        </td>
                                        <td>
                                            <div
                                                    class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                            {{ $pos->location?->name }}
                                            </div>
                                        </td>
                                        <td><div class="badge {{ $pos->status ? 'badge-light-success' : 'badge-light-danger'}}">{{ $pos->status ? __('Active') : __('Inactive') }}</div></td>
                                        <td class="text-end">
                                            <a href="#"
                                            class="btn btn-sm btn-flex btn-center btn-active-light-primary border border-0 border-gray fw-bold"
                                               data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">{{__('Actions')}}
                                                <i class="ki-duotone ki-right fs-5 ms-1"></i></a>
                                            <div
                                                class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                data-kt-menu="true">
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('devices-pos.edit', ['devices_po' => $pos->id ]) }}"
                                                       class="menu-link px-3">{{ __('View') }}</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3 text-danger"
                                                       data-pos-id = {{ $pos->id }}
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
