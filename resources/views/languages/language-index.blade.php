@extends('layouts.main-view')
@section('title', 'Languages')
@section('setup-script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    @vite('resources/assets/js/custom/apps/languages/list/list.js')
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
                                    <g id="language-active" transform="translate(-8719 -6517)">
                                        <path id="Path_136" data-name="Path 136"
                                            d="M12-456.37h0a11.953,11.953,0,0,1-1.925-2.762,16.375,16.375,0,0,1-1.239-3.478h6.35a16,16,0,0,1-1.247,3.484A12.284,12.284,0,0,1,12-456.371Zm-2-.257h0a12.4,12.4,0,0,1-4.625-2.115,10.933,10.933,0,0,1-3.046-3.867H7.35a17.151,17.151,0,0,0,1.075,3.3,13.263,13.263,0,0,0,1.569,2.678Zm4.039-.019,0,0a14.892,14.892,0,0,0,1.492-2.666,22.494,22.494,0,0,0,1.139-3.3h5.008a11.1,11.1,0,0,1-3.157,3.858,11.334,11.334,0,0,1-4.482,2.105Zm8.1-7.4H16.892c.078-.614.136-1.158.173-1.618s.053-.924.053-1.389c0-.376-.015-.788-.043-1.223-.031-.461-.082-1.033-.151-1.7h5.215a8.75,8.75,0,0,1,.33,1.418,10.826,10.826,0,0,1,.1,1.507,11.553,11.553,0,0,1-.1,1.547,8.931,8.931,0,0,1-.33,1.459Zm-6.68,0h-6.9c-.093-.641-.155-1.187-.183-1.625-.029-.454-.044-.918-.044-1.382,0-.443.015-.889.044-1.326s.09-.962.183-1.6h6.9c.094.654.154,1.177.182,1.6s.044.87.044,1.326c0,.477-.015.942-.044,1.382s-.088.975-.182,1.625Zm-8.371,0H1.861a8.932,8.932,0,0,1-.331-1.46,11.553,11.553,0,0,1-.1-1.547,10.827,10.827,0,0,1,.1-1.507,8.758,8.758,0,0,1,.331-1.418H7.107c-.078.523-.133,1.019-.162,1.477s-.044.936-.044,1.448c0,.449.012.925.034,1.415s.074,1.014.153,1.591Zm14.589-7.366H16.649a19.1,19.1,0,0,0-1.1-3.3A14.145,14.145,0,0,0,14-477.384a10.567,10.567,0,0,1,4.585,2.022,10.456,10.456,0,0,1,3.087,3.951Zm-6.461,0H8.834a19.878,19.878,0,0,1,1.357-3.745A8.558,8.558,0,0,1,12-477.591a13.1,13.1,0,0,1,1.969,2.818,14.372,14.372,0,0,1,1.247,3.363Zm-7.846,0H2.322a10.725,10.725,0,0,1,3.092-3.947,10.6,10.6,0,0,1,4.55-2.009,15.153,15.153,0,0,0-1.552,2.733A17.674,17.674,0,0,0,7.37-471.41Z"
                                            transform="translate(8719 6996)" fill="#5d4bdf" />
                                        <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24"
                                            transform="translate(8719 6517)" fill="none" />
                                    </g>
                                </svg>
                                <span
                                class="px-4 page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0">
                                    {{ __('Languages') }}</span>
                            </div>
                            <div class="card-toolbar d-flex">
                                <div class="d-flex align-items-center gap-2 gap-lg-0">
                                    @include('settings.search', [
                                        'label' => 'Search language by name, or language ID',
                                    ])
                                    <div class="d-flex justify-content-end w-125px"
                                        data-kt-customer-table-toolbar="base">
                                        <a href="{{ route('language.create') }}"
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
                                        <th class="text-gray-900 fw-bold min-w-125px pt-10 pb-10">{{ __('Name') }}</th>
                                        <th class="text-gray-900 fw-bold text-end min-w-125px pt-10 pb-10">{{ __('Created date') }}</th>
                                        <th class="text-gray-900 fw-bold text-end pe-12 min-w-70px pt-10 pb-10">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @foreach (\App\Helpers\Helpers::languages() as $language)
                                        <tr>
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $language->id }}" />
                                                </div>
                                            </td>
                                            <td>
                                                <div
                                                    class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    <a href="{{ route('language.edit', ['language' => $language->id]) }}"
                                                        class="text-gray-800 text-hover-primary mb-1">{{ $language->name }}</a>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div
                                                    class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                    {{ optional($language->created_at)->format('d.m.Y') ?? '' }}
                                            <td class="text-end">
                                                <a href="#"
                                                    class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary border-0"
                                                    data-kt-menu-trigger="click"
                                                    data-kt-menu-placement="bottom-end">{{ __('Actions') }}
                                                    <i class="ki-duotone ki-right fs-5 ms-1"></i></a>
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                    data-kt-menu="true">
                                                    <div class="menu-item px-3">
                                                        <a href="{{ route('language.edit', ['language' => $language->id]) }}"
                                                            class="menu-link px-3">{{ __('View') }}</a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3 text-danger"
                                                            data-language-id={{ $language->id }}
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
