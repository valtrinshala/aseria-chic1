@extends('layouts.main-view')
@section('title', 'Update device settings')
@section('setup-script')
    @vite('resources/assets/js/custom/apps/printer-settings/add.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar px-0 py-8">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                        <div class="page-title d-flex justify-content-center flex-wrap me-3">
                            <a href="{{ route('settings') }}"
                               class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Settings') }}
                                > </a>
                            <a href="{{ route('printerSettings.index') }}"
                               class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0 m-4">{{ __('Device settings') }}
                                > </a>
                            <span
                                class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ $printerSetting->device_name }}
                            </span>
                        </div>

                        <div class="d-flex align-items-center gap-2 gap-lg-3">
                            <a href="{{ route('printerSettings.index') }}"
                               class="btn btn-light btn-flex btn-center btn-white w-125px justify-content-center border-0">{{ __('Discard') }}</a>
                            <button id="submitButton" class="btn btn-primary w-125px border-0">{{ __('Save') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <form id="kt_ecommerce_add_printer_settings_form" class="form d-flex flex-column flex-lg-row">
                        <input type="hidden" id="method" value="update">
                        <input type="hidden" id="page-id" value="{{ $printerSetting->id }}">
                        <div class="d-flex flex-column gap-7 gap-lg-10 w-450px mb-7 me-lg-7">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                     role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{__('Device settings')}}</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column w-100 flex-row-fluid gap-7 gap-lg-10">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general"
                                     role="tab-panel">
                                    <div class="d-flex flex-column gap-7 gap-lg-10">
                                        <div class="card card-flush py-4">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h2>{{__('General')}}</h2>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 pb-0">
                                                <div class="row">
                                                    <div class="mb-7 fv-row">
                                                        <div class="fv-row">
                                                            <div class="row">
                                                                <label for="device_type"
                                                                       class="col required form-label fw-bold">{{ __('Device type') }}</label>
                                                                <span
                                                                    class="text-end col text-gray-900 fs-7">{{ __('Set the device type.') }}</span>
                                                            </div>
                                                        </div>

                                                        <select name="device_type" class="form-select mb-2"
                                                                data-control="select2"
                                                                data-hide-search="true"
                                                                data-placeholder="{{ __('Select an option') }}"
                                                                id="device_type">
                                                            <option
                                                                @selected($printerSetting->device_type == 'sticker_printer') value="sticker_printer">{{ __('Sticker printer') }}</option>
                                                            <option
                                                                @selected($printerSetting->device_type == 'printer') value="printer">{{ __('Printer') }}</option>
                                                            <option
                                                                @selected($printerSetting->device_type == 'terminal') value="terminal">{{ __('Terminal') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-7 fv-row">
                                                        <label for="device_name"
                                                               class="required form-label fw-bold">{{__('Device name')}}</label>
                                                        <input id="device_name" type="text" name="device_name"
                                                               value="{{ $printerSetting->device_name }}"
                                                               class="form-control mb-2"
                                                               placeholder="{{__('Device name')}}"/>
                                                    </div>

                                                    <div class="mb-7 fv-row">
                                                        <label for="device_ip"
                                                               class="required form-label fw-bold">{{__('Device ip')}}</label>
                                                        <input id="device_ip" type="text" name="device_ip"
                                                               value="{{ $printerSetting->device_ip }}"
                                                               class="form-control mb-2"
                                                               placeholder="{{__('Device ip')}}"/>
                                                    </div>
                                                    <div class="mb-7 fv-row">
                                                        <div class="fv-row">
                                                            <div class="row">
                                                                <label for="device_port" class="col required form-label fw-bold">{{ __('Device port') }}</label>
                                                                <span
                                                                    class="text-end col text-gray-900 fs-7">{{ __('Device port') }}</span>
                                                            </div>
                                                        </div>

                                                        <select name="device_port" class="form-select mb-2" data-control="select2"
                                                                data-hide-search="true" data-placeholder="{{ __('Select an option') }}"
                                                                id="device_port">
                                                            <option {{ $printerSetting->device_port == '4100' ? 'selected' : ''}} value="4100">{{ __('4100') }}</option>
                                                            <option {{ $printerSetting->device_port == '9100' ? 'selected' : ''}} value="9100">{{ __('9100') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="input-terminal" @if(!$printerSetting->device_type == "terminal") style="display: none" @endif >
                                                        <div class="mb-7 fv-row">
                                                            <div class="fv-row">
                                                                <div class="row">
                                                                    <label for="terminal_type" class="col required form-label fw-bold">{{ __('Terminal type') }}</label>
                                                                    <span
                                                                        class="text-end col text-gray-900 fs-7">{{ __('Terminal type') }}</span>
                                                                </div>
                                                            </div>

                                                            <select name="terminal_type" class="form-select mb-2" data-control="select2"
                                                                    data-hide-search="true" data-placeholder="{{ __('Select an option') }}"
                                                                    id="terminal_type">
                                                                <option {{ $printerSetting->terminal_type == 'ATTENDED_OPI_CH' ? 'selected' : ''}} value="ATTENDED_OPI_CH">{{ __('ATTENDED_OPI_CH') }}</option>
                                                                <option {{ $printerSetting->terminal_type == 'ATTENDED_OPI_DE' ? 'selected' : ''}} value="ATTENDED_OPI_DE">{{ __('ATTENDED_OPI_DE') }}</option>
                                                                <option {{ $printerSetting->terminal_type == 'UNATTENDED_OPI_DE' ? 'selected' : ''}} value="UNATTENDED_OPI_DE">{{ __('UNATTENDED_OPI_DE') }}</option>
                                                                <option {{ $printerSetting->terminal_type == 'ATTENDED_OPI_NL' ? 'selected' : ''}} value="ATTENDED_OPI_NL">{{ __('ATTENDED_OPI_NL') }}</option>
                                                                <option {{ $printerSetting->terminal_type == 'UNATTENDED_OPI_NL' ? 'selected' : ''}} value="UNATTENDED_OPI_NL">{{ __('UNATTENDED_OPI_NL') }}</option>
                                                                <option {{ $printerSetting->terminal_type == 'UNATTENDED_OPI_CH' ? 'selected' : ''}} value="UNATTENDED_OPI_CH">{{ __('UNATTENDED_OPI_CH') }}</option>
                                                            </select>
                                                        </div>
{{--                                                        <div class="mb-7 fv-row">--}}
{{--                                                            <label for="terminal_type" class="terminal-inputs required form-label fw-bold">{{__('Terminal type')}}</label>--}}
{{--                                                            <input id="terminal_type" type="text" name="terminal_type" value="{{ $printerSetting->terminal_type }}"--}}
{{--                                                                   class="form-control mb-2" placeholder="{{__('Terminal type')}}"/>--}}
{{--                                                        </div>--}}
                                                        <div class="mb-7 fv-row">
                                                            <label for="terminal_id" class="terminal-inputs required form-label fw-bold">{{__('Terminal ID')}}</label>
                                                            <input id="terminal_id" type="text" name="terminal_id" value="{{ $printerSetting->terminal_id }}"
                                                                   class="form-control mb-2" placeholder="{{__('Terminal ID')}}"/>
                                                        </div>

                                                        <div class="mb-7 fv-row">
                                                            <div class="fv-row">
                                                                <div class="row">
                                                                    <label for="terminal_compatibility_port" class="col required form-label fw-bold">{{ __('Terminal compatibility port') }}</label>
                                                                    <span
                                                                        class="text-end col text-gray-900 fs-7">{{ __('Terminal compatibility port') }}</span>
                                                                </div>
                                                            </div>

                                                            <select name="terminal_compatibility_port" class="form-select mb-2" data-control="select2"
                                                                    data-hide-search="true" data-placeholder="{{ __('Select an option') }}"
                                                                    id="terminal_compatibility_port">
                                                                <option {{ $printerSetting->terminal_compatibility_port == '4102' ? 'selected' : ''}} value="4102">{{ __('4102') }}</option>
                                                            </select>
                                                        </div>
{{--                                                        <div class="mb-7 fv-row">--}}
{{--                                                            <label for="terminal_compatibility_port" class="terminal-inputs required form-label fw-bold">{{__('Terminal compatibility port')}}</label>--}}
{{--                                                            <input id="terminal_compatibility_port" type="text" name="terminal_compatibility_port" value="{{ $printerSetting->terminal_compatibility_port }}"--}}
{{--                                                                   class="form-control mb-2" placeholder="{{__('Terminal compatibility port')}}"/>--}}
{{--                                                        </div>--}}
                                                        <div class="mb-7 fv-row">
                                                            <div class="fv-row">
                                                                <div class="row">
                                                                    <label for="terminal_socket_mode" class="col required form-label fw-bold">{{ __('Terminal socket mode') }}</label>
                                                                    <span
                                                                        class="text-end col text-gray-900 fs-7">{{ __('Terminal socket mode') }}</span>
                                                                </div>
                                                            </div>

                                                            <select name="terminal_socket_mode" class="form-select mb-2" data-control="select2"
                                                                    data-hide-search="true" data-placeholder="{{ __('Select an option') }}"
                                                                    id="device_type">
                                                                <option {{ $printerSetting->terminal_socket_mode == 'DUAL_SOCKET' ? 'selected' : ''}} value="DUAL_SOCKET">{{ __('DUAL SOCKET ') }}</option>
                                                                <option {{ $printerSetting->terminal_socket_mode == 'SINGLE_SOCKET' ? 'selected' : ''}} value="SINGLE_SOCKET">{{ __('SINGLE SOCKET') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="fv-row">
                                                        <div class="col">
                                                            <label class="form-check-label mb-4"
                                                                   for="device_status"><span
                                                                    class="fw-bold text-gray-900">{{ __('Device status') }}</span>
                                                                <span
                                                                    class="small p-5 text-muted text-nowrap">{{ __('When the status is deactivated, the device will be invisible and cannot be accessed from anywhere.') }}</span></label>
                                                            <div class="form-check form-switch">
                                                                <label class="form-check-label mb-4"
                                                                       for="device_status">{{ __('Activated') }}</label>
                                                                <input
                                                                    @checked($printerSetting->device_status) class="form-check-input"
                                                                    id="device_status" name="device_status"
                                                                    type="checkbox" role="checkbox" value="1">
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-check-label mt-7"><span
                                                                    class="fw-bold text-gray-900">{{ __('Clear device status') }}</span>
                                                                <span
                                                                    class="small p-5 text-muted text-nowrap">{{ __('Clear the device from the cash register or kiosk.') }}</span></label>
                                                            <div>
                                                                <a href="javascript:void(0)"
                                                                   id="clear-device-status"
                                                                   data-id="{{ $printerSetting->id }}"
                                                                   data-name="{{ $printerSetting->device_name }}"
                                                                   class="btn btn-secondary mt-2 btn-flex btn-center btn-white w-125px justify-content-center border-0">{{ __('Clear') }}</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#device_type').change(function() {
            var selectedDeviceType = $(this).val();
            console.log('Selected Device Type: ' + selectedDeviceType);

            if (selectedDeviceType !== 'terminal') {
                $('.input-terminal').hide();
            } else {
                $('.input-terminal').show();
            }
        });
        $('#device_type').trigger('change');
    </script>
@endsection
