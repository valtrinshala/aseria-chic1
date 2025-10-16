@extends('layouts.main-view')
@section('title', 'Modifiers')
@section('setup-script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    @vite('resources/assets/js/custom/apps/modifiers/list/list.js')
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
                                    <g id="extras-active" transform="translate(-8719 -6517)">
                                        <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24"
                                              transform="translate(8719 6517)" fill="none"/>
                                        <path id="Subtraction_11" data-name="Subtraction 11"
                                              d="M12.011-493a11.65,11.65,0,0,1-4.67-.945,12.252,12.252,0,0,1-3.813-2.58,12.259,12.259,0,0,1-2.582-3.813A11.674,11.674,0,0,1,0-505.013a11.692,11.692,0,0,1,.945-4.669,12.051,12.051,0,0,1,2.58-3.806,12.38,12.38,0,0,1,3.814-2.566A11.667,11.667,0,0,1,12.014-517a11.691,11.691,0,0,1,4.669.945,12.183,12.183,0,0,1,3.806,2.564,12.175,12.175,0,0,1,2.565,3.81,11.7,11.7,0,0,1,.946,4.67,11.658,11.658,0,0,1-.945,4.671,12.337,12.337,0,0,1-2.565,3.807,12.161,12.161,0,0,1-3.809,2.582A11.658,11.658,0,0,1,12.011-493ZM6-505.626H6v1.433h5.353V-499h1.434v-5.194H18v-1.433H12.787V-511H11.354v5.374Z"
                                              transform="translate(8719 7034)" fill="#5d4bdf"/>
                                    </g>
                                </svg>
                                <span
                                    class="px-4 page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0">
                                    {{ __('Modifiers') }}</span>
                            </div>
                            <div class="card-toolbar d-flex">
                                <div class="d-flex align-items-center gap-2 gap-lg-0">
                                    @include('settings.search', [
                                        'label' => 'Search Modifiers by name or modifier ID',
                                    ])
                                    <div class="d-flex justify-content-end w-125px"
                                         data-kt-customer-table-toolbar="base">
                                        <a href="{{ route('modifier.create') }}"
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
                                                   value="1"/>
                                        </div>
                                    </th>
                                    <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Modifier name') }}</th>
                                    <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Cost') }}</th>
                                    <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Price') }}</th>
                                    <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Status') }}</th>
                                    <th class="text-gray-900 fw-bold pe-12 text-end min-w-70px py-10">{{ __('Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                @foreach ($modifiers as $modifier)
                                    <tr>
                                        <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox"
                                                       value="{{ $modifier->id }}"/>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="row">
{{--                                                <div class="h-25px w-25px rounded col-1"--}}
{{--                                                     style="background-color: {{ $modifier->category?->color }}"></div>--}}
                                                <div class="col  align-self-center">
                                                    <a href="{{ route('modifier.edit', ['modifier' => $modifier->id]) }}"
                                                       class="text-gray-800 fs-10.5 flex-column justify-content-center my-0 text-hover-primary mb-1">{{ $modifier->title }}</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div
                                            class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                @if(!$settings->currency_symbol_on_left)
                                                    {{ number_format($modifier->cost, 2) }}
                                                    {{ $settings->currency_symbol }}
                                                @else
                                                    {{ $settings->currency_symbol }}
                                                    {{ number_format($modifier->cost, 2) }}
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div
                                            class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                @if(!$settings->currency_symbol_on_left)
                                                    {{ number_format($modifier->price, 2) }}
                                                    {{ $settings->currency_symbol }}
                                                @else
                                                    {{ $settings->currency_symbol }}
                                                    {{ number_format($modifier->price, 2) }}
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div
                                                class="badge {{ $modifier->status ? 'badge-light-success' : 'badge-light-danger' }}">
                                                {{ $modifier->status ? __("Published") : __("Inactive") }}</div>
                                        </td>
                                        <td class="text-end">
                                            <a href="#"
                                               class="btn btn-sm btn-flex btn-center btn-active-light-primary border-0"
                                               data-kt-menu-trigger="click"
                                               data-kt-menu-placement="bottom-end">{{ __('Actions') }}
                                                <i class="ki-duotone ki-right fs-5 ms-1"></i></a>
                                            <div
                                                class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                data-kt-menu="true">
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('modifier.edit', ['modifier' => $modifier->id]) }}"
                                                       class="menu-link px-3">{{ __('View') }}</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3 text-danger"
                                                       data-modifier-id={{ $modifier->id }}
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
