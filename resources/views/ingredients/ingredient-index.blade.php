@extends('layouts.main-view')
@section('title', 'Ingredients')
@section('setup-script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    @vite('resources/assets/js/custom/apps/ingredients/list/list.js')
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
                                    <g id="ingredients-active" transform="translate(-8719 -6517)">
                                        <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24"
                                            transform="translate(8719 6517)" fill="none" />
                                        <path id="nutrition_FILL0_wght300_GRAD0_opsz48"
                                            d="M228.979-811.059a8.66,8.66,0,0,1-6.363-2.615A8.66,8.66,0,0,1,220-820.037a8.772,8.772,0,0,1,1.964-5.65,8.726,8.726,0,0,1,5.088-3.134,8.139,8.139,0,0,1-1.633-.461,4.006,4.006,0,0,1-1.392-.931,4.545,4.545,0,0,1-1.215-2.147,11.017,11.017,0,0,1-.315-2.527,2.4,2.4,0,0,1,.5-.151,1.009,1.009,0,0,1,.445.008,5.817,5.817,0,0,1,4.035,1.865A5.8,5.8,0,0,1,229.117-829a10.28,10.28,0,0,1,1.258-2.523,15.269,15.269,0,0,1,1.813-2.183.754.754,0,0,1,.552-.231.754.754,0,0,1,.552.231.746.746,0,0,1,.231.546.746.746,0,0,1-.231.546,13.373,13.373,0,0,0-1.474,1.778,11.91,11.91,0,0,0-1.108,1.98,8.685,8.685,0,0,1,5.213,3.09,8.761,8.761,0,0,1,2.033,5.731,8.66,8.66,0,0,1-2.615,6.363A8.66,8.66,0,0,1,228.979-811.059Z"
                                            transform="translate(8502.021 7352.059)" fill="#5d4bdf" />
                                    </g>
                                </svg>
                                <span
                                    class="px-4 page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0">
                                    {{ __('Ingredients') }}</span>
                            </div>
                            <div class="card-toolbar d-flex">
                                <div class="d-flex align-items-center gap-2 gap-lg-0">
                                    @include('settings.search', [
                                        'label' => 'Search ingredients by name, or ingredient ID',
                                    ])
                                    <div class="d-flex justify-content-end w-125px" data-kt-customer-table-toolbar="base">
                                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true"
                                            id="kt-toolbar-filter">
                                            <div class="px-7 py-5">
                                                <div class="fs-4 text-gray-900 fw-bold">{{ __('Filter Options') }}</div>
                                            </div>

                                        </div>
                                        <a href="{{ route('ingredient.create') }}"
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
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">
                                            {{ __('Ingredient name') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">
                                            {{ __('Price') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">
                                            {{ __('Cost') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">
                                            {{ __('Unit') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">
                                            {{ __('Available Quantity') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">
                                            {{ __('Created Date') }}</th>
                                        <th class="text-gray-900 fw-bold pe-12 text-end min-w-70px py-10">
                                            {{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach ($ingredients as $ingredient)
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $ingredient->id }}" />
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    <a href="{{ route('ingredient.edit', ['ingredient' => $ingredient->id]) }}"
                                                        class="text-gray-800 text-hover-primary mb-1">{{ $ingredient->name }}</a>
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="me-3 text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    @price($ingredient->price, $settings)
                                                </div>
                                            </td>
                                            <td data-filter="mastercard">
                                                <div
                                                    class="me-3 text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    @price($ingredient->cost, $settings)
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="me-3 text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    {{$ingredient->unit}}
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="me-3 text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    {{$ingredient->quantity}}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">

                                                    {{ \Carbon\Carbon::parse($ingredient->created_at)->format('d.m.Y') }}
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
                                                        <a href="{{ route('ingredient.edit', ['ingredient' => $ingredient->id]) }}"
                                                            class="menu-link px-3">{{ __('View') }}</a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3 text-danger"
                                                            data-ingredient-id={{ $ingredient->id }}
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
