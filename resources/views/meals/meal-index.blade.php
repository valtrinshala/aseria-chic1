@extends('layouts.main-view')
@section('title', 'Meals')
@section('setup-script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    @vite('resources/assets/js/custom/apps/meals/list/list.js')
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
                                    <path id="fastfood_FILL1_wght300_GRAD0_opsz48"
                                        d="M68.848-875.553a4.663,4.663,0,0,1,2.58-4.293,11.45,11.45,0,0,1,5.743-1.461,11.441,11.441,0,0,1,5.749,1.461,4.664,4.664,0,0,1,2.574,4.293Zm0,3.618v-1.473H85.493v1.473Zm.819,3.629a.794.794,0,0,1-.579-.238.775.775,0,0,1-.24-.572v-.663H85.493v.663a.784.784,0,0,1-.235.572.789.789,0,0,1-.585.238Zm17.734,0v-7.486a6.463,6.463,0,0,0-2.007-4.783,9.2,9.2,0,0,0-4.852-2.514L80-887.271h5.714v-5.035H87.04v5.035h5.808l-1.861,17.463a1.6,1.6,0,0,1-.551,1.08,1.67,1.67,0,0,1-1.133.421Z"
                                        transform="translate(-68.848 892.306)" fill="#5d4bdf" />
                                </svg>
                                <span
                                    class="px-4 page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0">
                                    {{ __('Deals') }}</span>
                            </div>
                            <div class="card-toolbar d-flex">
                                <div class="d-flex align-items-center gap-2 gap-lg-0">
                                    @include('settings.search', ['label' => 'Search deals by name or SKU'])
                                    <div class="d-flex justify-content-end w-125px"
                                        data-kt-customer-table-toolbar="base">
                                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true"
                                            id="kt-toolbar-filter">
                                            <div class="px-7 py-5">
                                                <div class="fs-4 text-gray-900 fw-bold">{{ __('Filter Options') }}</div>
                                            </div>
                                            <div class="separator border-gray-200"></div>
                                        </div>
                                        <a href="{{ route('meal.create') }}"
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
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Deal name') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('SKU') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Cost') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Price') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Category') }}</th>
                                        <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Status') }}</th>
                                        <th class="text-gray-900 fw-bold pe-12 text-end min-w-70px py-10">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach ($meals as $meal)
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $meal->id }}" />
                                                </div>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="h-25px w-25px rounded col-1"
                                                        style="background-color: {{ $meal->category?->color }}"></div>
                                                    <div class="col  align-self-center">
                                                        <a href="{{ route('meal.edit', ['meal' => $meal->id]) }}"
                                                            class="text-gray-800 fs-10.5 flex-column justify-content-center my-0 text-hover-primary mb-1">{{ $meal->name }}</a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    <div class="text-gray-800 mb-1"> {{ $meal->sku }} </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    @if(!$settings->currency_symbol_on_left)
                                                    {{ number_format($meal->cost, 2) }}
                                                    {{ $settings->currency_symbol }}
                                                @else
                                                    {{ $settings->currency_symbol }}
                                                    {{ number_format($meal->cost, 2) }}
                                                @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    @if(!$settings->currency_symbol_on_left)
                                                    {{ number_format($meal->price, 2) }}
                                                    {{ $settings->currency_symbol }}
                                                @else
                                                    {{ $settings->currency_symbol }}
                                                    {{ number_format($meal->price, 2) }}
                                                @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    {{ $meal->category?->name }}
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="badge {{ $meal->status ? 'badge-light-success' : 'badge-light-danger' }}">
                                                    {{ $meal->status ? __("Published") : __("Inactive")}}</div>
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
                                                        <a href="{{ route('meal.edit', ['meal' => $meal->id]) }}"
                                                            class="menu-link px-3">{{ __('View') }}</a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3 text-danger"
                                                            data-meal-id={{ $meal->id }}
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
