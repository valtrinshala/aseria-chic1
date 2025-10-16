@extends('layouts.main-view')
@section('title', 'Products')
@section('setup-script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    @vite('resources/assets/js/custom/apps/food-items/list/list.js')
@endsection
@section('page-style')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.bundle.css') }}">
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
                                    <g id="products-active" transform="translate(-8719 -6517)">
                                        <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24"
                                            transform="translate(8719 6517)" fill="none" />
                                        <path id="Path_138" data-name="Path 138"
                                            d="M22.178-316.437H7.664v-4.4h14.9v4.01a.374.374,0,0,1-.122.268A.373.373,0,0,1,22.178-316.437Zm-15.947,0H1.822a.373.373,0,0,1-.267-.121.374.374,0,0,1-.122-.268v-4.01h4.8v4.4Zm16.336-5.8H7.664v-4.485h14.9v4.484Zm-16.336,0h-4.8v-4.485h4.8v4.484Zm16.336-5.922H7.664v-4.4H22.178a.373.373,0,0,1,.267.122.374.374,0,0,1,.122.268v4.008Zm-16.336,0h-4.8v-4.009a.374.374,0,0,1,.122-.268.373.373,0,0,1,.267-.122H6.231v4.4Z"
                                            transform="translate(8719 6853.5)" fill="#5d4bdf" />
                                    </g>
                                </svg>
                                <span
                                    class="px-4 page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0">
                                    {{ __('Products') }}</span>
                            </div>
                            <div class="card-toolbar d-flex">
                                <div class="d-flex align-items-center gap-2 gap-lg-0">
                                    @include('settings.search', [
                                        'label' => 'Search Products by name, or product ID',
                                    ])
                                    <div class="d-flex justify-content-end w-125px"
                                        data-kt-customer-table-toolbar="base">

                                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true"
                                            id="kt-toolbar-filter">

                                            <div class="separator border-gray-200"></div>
                                            <div class="px-7 py-5">
                                                <div class="d-flex justify-content-end">
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route('foodItem.create') }}"
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
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Product name') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('SKU') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Cost') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Price') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Category') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Status') }}</th>
                                        <th class="text-gray-900 fw-bold pe-12 text-end min-w-70px py-10">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach ($foodItems as $foodItem)
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $foodItem->id }}" />
                                                </div>
                                            </td>
                                            <td>
                                                <div class="row">
                                                <div class="h-25px w-25px rounded col-1"
                                                    style="background-color: {{ $foodItem->category?->color }}"></div>
                                                    <div class="col  align-self-center">
                                                        <a href="{{ route('foodItem.edit', ['foodItem' => $foodItem->id]) }}"
                                                        class="text-gray-800 fs-10.5 flex-column justify-content-center my-0 text-hover-primary mb-1">{{ $foodItem->name }}</a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    {{ $foodItem->sku }}
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    @if(!$settings->currency_symbol_on_left)
                                                    {{ number_format($foodItem->cost, 2) }}
                                                    {{ $settings->currency_symbol }}
                                                @else
                                                    {{ $settings->currency_symbol }}
                                                    {{ number_format($foodItem->cost, 2) }}
                                                @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    @if(!$settings->currency_symbol_on_left)
                                                    {{ number_format($foodItem->price, 2) }}
                                                    {{ $settings->currency_symbol }}
                                                @else
                                                    {{ $settings->currency_symbol }}
                                                    {{ number_format($foodItem->price, 2) }}
                                               @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    {{ $foodItem->category?->name }}
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="badge {{ $foodItem->status ? 'badge-light-success' : 'badge-light-danger' }}">
                                                    {{ $foodItem->status ? __('Published') : __('Inactive') }}</div>
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
                                                        <a href="{{ route('foodItem.edit', ['foodItem' => $foodItem->id]) }}"
                                                            class="menu-link px-3">{{ __('View') }}</a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3 text-danger"
                                                            data-food-item-id={{ $foodItem->id }}
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
