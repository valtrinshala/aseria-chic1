@extends('layouts.main-view')
@section('title', 'eKiosks')
@section('setup-script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    @vite('resources/assets/js/custom/apps/e-kiosk-assets/list/list.js')
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
                                        class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Manage assets') }}</span>
                                </div>
                            </div>
                            <div class="card-toolbar d-flex">
                                <div class="d-flex align-items-center gap-2 gap-lg-1">
                                    @include('settings.search', [
                                        'label' => 'Search assets by name or asset ID',
                                    ])
                                    <div class="d-flex justify-content-end w-125px"
                                        data-kt-customer-table-toolbar="base">
                                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true"
                                            id="kt-toolbar-filter">
                                            <div class="separator border-gray-200"></div>
                                        </div>
                                        <a href="{{ route('eKioskAsset.create') }}"
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
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Asset name') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Asset position key') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Asset position name') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Type') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('E-kiosk') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Status') }}</th>
                                        <th class="text-gray-900 fw-bold pe-12 text-end min-w-70px py-10">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach ($eKioskAssets as $asset)
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $asset->id }}" />
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('eKioskAsset.edit', ['eKioskAsset' => $asset->id]) }}"
                                                    class="text-gray-800 text-hover-primary mb-1 fs-10.5 flex-column justify-content-center my-0">{{ $asset->name }}</a>
                                            </td>
                                            <td>
                                                <div class="text-gray-800 mb-1 fs-10.5 flex-column justify-content-center my-0">{{ $asset->position?->asset_key }}</div>
                                            </td>
                                            <td>
                                                <div class="text-gray-800 mb-1 fs-10.5 flex-column justify-content-center my-0">{{ $asset->position?->name }}</div>
                                            </td>
                                            <td
                                                class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                {{ $asset->type }}
                                            </td>
                                            <td>
                                                <div
                                                    class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                  {{ $asset->eKiosk?->name }} </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="badge {{ $asset->status ? 'badge-light-success' : 'badge-light-danger' }}">
                                                    {{ $asset->status ? __('Active') : __('Inactive') }}</div>
                                            </td>
                                            <td class="text-end">
                                                <a href="#"
                                                    class="btn btn-sm btn-flex btn-center btn-active-light-primary fw-bold"
                                                    data-kt-menu-trigger="click"
                                                    data-kt-menu-placement="bottom-end">{{ __('Actions') }}
                                                    <i class="ki-duotone ki-right fs-5 ms-1"></i></a>
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                    data-kt-menu="true">
                                                    <div class="menu-item px-3">
                                                        <a href="{{ route('eKioskAsset.edit', ['eKioskAsset' => $asset->id]) }}"
                                                            class="menu-link px-3">{{ __('View') }}</a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3 text-danger"
                                                            data-eKioskAsset-id={{ $asset->id }}
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
