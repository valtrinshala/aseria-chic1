@extends('layouts.main-view')
@section('title', 'Food-Categories')
@section('setup-script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    @vite('resources/assets/js/custom/apps/food-categories/list/list.js')
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
                                    <g id="food-category-active" transform="translate(-8719 -6517)">
                                        <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24"
                                            transform="translate(8719 6517)" fill="none" />
                                        <path id="network_node_FILL0_wght300_GRAD0_opsz48"
                                            d="M103.531-836a3.394,3.394,0,0,1-2.494-1.04,3.414,3.414,0,0,1-1.036-2.5,3.4,3.4,0,0,1,1.039-2.494,3.408,3.408,0,0,1,2.5-1.036,3.408,3.408,0,0,1,1.1.177,3.379,3.379,0,0,1,.958.508l5.687-5.667v-4.939a3.611,3.611,0,0,1-2.009-1.24,3.379,3.379,0,0,1-.809-2.236,3.4,3.4,0,0,1,1.04-2.5,3.409,3.409,0,0,1,2.5-1.039,3.4,3.4,0,0,1,2.494,1.039,3.408,3.408,0,0,1,1.036,2.5,3.394,3.394,0,0,1-.8,2.236,3.585,3.585,0,0,1-2.015,1.24v4.939l5.675,5.667a3.578,3.578,0,0,1,.977-.508,3.412,3.412,0,0,1,1.1-.177,3.4,3.4,0,0,1,2.5,1.04,3.409,3.409,0,0,1,1.039,2.5,3.393,3.393,0,0,1-1.04,2.494,3.413,3.413,0,0,1-2.5,1.036,3.4,3.4,0,0,1-2.494-1.039,3.408,3.408,0,0,1-1.036-2.5,3.364,3.364,0,0,1,.128-.937,3.482,3.482,0,0,1,.362-.834L112-846.746l-5.422,5.441a3.482,3.482,0,0,1,.362.834,3.364,3.364,0,0,1,.128.937,3.4,3.4,0,0,1-1.04,2.5A3.409,3.409,0,0,1,103.531-836Z"
                                            transform="translate(8618.999 7376.999)" fill="#5d4bdf" />
                                    </g>
                                </svg>

                                <span
                                    class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 p-4">
                                    {{ __('Categories') }}</span>
                            </div>
                            <div class="card-toolbar d-flex">
                                <div class="d-flex align-items-center gap-2 gap-lg-0">
                                    @include('settings.search', ['label' => 'Search category by name'])
                                    <div class="d-flex justify-content-end w-125px"
                                        data-kt-customer-table-toolbar="base">
                                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true"
                                            id="kt-toolbar-filter">
                                            <div class="separator border-gray-200"></div>
                                        </div>
                                        <a href="{{ route('foodCategory.create') }}"
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
                                        <th class="text-gray-900 fw-bold min-w-125px pt-10 pb-10">{{ __('Category name') }}</th>
                                        <th class="text-end text-gray-900 fw-bold text-end min-w-125px pt-10 pb-10">{{ __('Date created') }}</th>
                                        <th class="text-gray-900 fw-bold text-end min-w-125px pt-10 pb-10">{{ __('Status') }}</th>
                                        <th class="text-gray-900 fw-bold text-end pe-12 min-w-70px pt-10 pb-10">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach ($foodCategories as $foodCategory)
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $foodCategory->id }}" />
                                                </div>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="h-25px w-25px rounded col-1"
                                                        style="background-color: {{ $foodCategory->color }}"></div>
                                                    <div class="col  align-self-center">
                                                        <a href="{{ route('foodCategory.edit', ['foodCategory' => $foodCategory->id]) }}"
                                                            class="text-gray-800 fs-10.5 flex-column justify-content-center my-0 text-hover-primary mb-1">{{ $foodCategory->name }}</a>
                                                    </div>

                                                </div>

                                            </td>
                                            <td class="text-end">
                                                <div
                                                    class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    {{ \Carbon\Carbon::parse($foodCategory->created_at)->format('d.m.Y') }}

                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div
                                                    class="badge {{ $foodCategory->status ? 'badge-light-success' : 'badge-light-danger' }}">
                                                    {{ $foodCategory->status ? __('Published') : __('Inactive') }}
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
                                                        <a href="{{ route('foodCategory.edit', ['foodCategory' => $foodCategory->id]) }}"
                                                            class="menu-link px-3">{{ __('View') }}</a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3 text-danger"
                                                            data-food-category-id={{ $foodCategory->id }}
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
