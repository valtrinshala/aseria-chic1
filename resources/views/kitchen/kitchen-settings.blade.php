@extends('layouts.kitchen')
@section('title', 'Kitchen Settings')
@section('page-script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <!--<script src="{{ asset('assets/js/custom-js.js') }}"></script>-->
    @vite('resources/assets/js/custom/apps/pos/custom.js')
    @vite('resources/assets/js/custom/apps/pos/z-report.js')
    @vite('resources/assets/js/custom/apps/utils.js')
    @vite('resources/assets/js/custom/apps/sales/sales-print.js')
    @vite('resources/assets/js/custom/apps/printer-settings/device_settings.js')
@endsection
@section('page-style')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/horizon.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/kitchen.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
<style>
    body {
        background: #F6F8F8;
    }
</style>
<input type="hidden" id="device-type" value="kitchen">
<div class="d-flex m-4 w-100 gap-4 settings-p">
    <div class="main-content flex-grow-1">
        <div class="settings-card bg-white rounded p-6">
            <div class="card-header d-flex justify-content-between">
                <div class="left">
                    <h4>{{ __('Printers') }} </h4>
                </div>
                <div class="right d-flex gap-5">
                    <div class="edit-btn clickable px-5">
                        <span class="text" active="{{ __('Save') }}" inactive="{{ __('Edit') }}">{{ __('Edit') }}</span>
                    </div>
                    <div onclick="location.reload();" class="refresh clickable px-5">
                        <span class="text">{{ __('Refresh') }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="pos-section section-list">
                    <p class="title">{{ __('Kitchen') }}</p>
                    <div class="content p-6 rounded">
                        <div class="row-item">
                            <p class="row-name">{{ __('Order printer') }}</p>

                            <div class="w-50">
                                <select id="order_printer" name="order_printer" class="form-select mb-2"
                                        data-control="select2" data-placeholder="{{ __('Select printer') }}"
                                        data-allow-clear="true">
                                    <option selected></option>
                                    <option value=" ">{{ __('No Printer') }}</option>
                                    @foreach($printers as $printer)
                                        @if($printer->device_type == 'printer')
                                            <option value="{{ $printer->id }}">{{ $printer->device_name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row-item">
                            <p class="row-name">{{ __('Sticker printer') }}</p>

                            <div class="w-50">
                                <select id="sticker_printer" name="sticker_printer" class="form-select mb-2"
                                        data-control="select2" data-placeholder="{{ __('Select printer') }}"
                                        data-allow-clear="true">
                                    <option selected></option>
                                    <option value=" ">{{ __('No Printer') }}</option>
                                    @foreach($printers as $printer)
                                        @if($printer->device_type == 'printer')
                                            <option value="{{ $printer->id }}">{{ $printer->device_name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pos-section section-list">
                    <p class="title">{{ __('Manage all devices') }}</p>
                    <div class="content p-6 rounded">
                        <div class="row-item">
                            <p class="row-name">{{ __('Order printer') }}</p>

                            <div>
                                <div class="form-switch p-0 ps-3">
                                    <input class="form-check-input m-0" id="order_printer_status" name="order_printer_status" type="checkbox" role="checkbox">
                                </div>
                            </div>
                        </div>

                        <div class="row-item">
                            <p class="row-name">{{ __('Sticker printer') }}</p>

                            <div>
                                <div class="form-switch p-0 ps-3">
                                    <input class="form-check-input m-0" id="sticker_printer_status" name="sticker_printer_status" type="checkbox" role="checkbox">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class='sidebar'>
        <div class="settings-card bg-white rounded p-6">
            <div class="card-header mb-6">
                <h4>{{ __('Other settings') }}</h4>
            </div>

            <div class="card-body">
                <div class="row-item flex-column">
                    <p class="row-name">{{ __('Kitchen language') }}</p>

                    <button type="button" class="btn btn-secondary px-3 h-45px w-100"
                            data-kt-menu-trigger="{default: 'click', lg: 'click'}"
                            data-kt-menu-placement="bottom-start">
                        {{ __('Language') }}
                    </button>
                    <div class="menu menu-sub menu-sub-dropdown menu-column w-auto" data-kt-menu="true">
                        <div class="card card-body w-auto">
                            <div class="menu-item">
                                @foreach (\App\Helpers\Helpers::languages() as $language)
                                    <div class="p-2"
                                            @if ($language->locale == app()->getLocale()) style="background-color: #F1F1F4;" @endif>
                                        <a
                                            href="{{ route('set.language.storage', ['language' => $language->id]) }}">{{ $language->name }}</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.printers = {};
    window.sticker_printers = {};
    window.terminals = {};

    @foreach($printers as $printer)
        @if($printer->device_type == 'printer')
            window.printers[`{{$printer->id}}`] = {
                id: `{{$printer->id}}`,
                name: `{{$printer->device_name}}`,
                ip: `{{$printer->device_ip}}`,
                port: `{{$printer->device_port}}`,
                type: `{{$printer->device_type}}`,
            }
        @endif
    @endforeach

    @foreach($printers as $printer)
        @if($printer->device_type == 'sticker_printer')
            window.sticker_printers[`{{$printer->id}}`] = {
                id: `{{$printer->id}}`,
                name: `{{$printer->device_name}}`,
                ip: `{{$printer->device_ip}}`,
                port: `{{$printer->device_port}}`,
                type: `{{$printer->device_type}}`,
            }
        @endif
    @endforeach

    @foreach($printers as $printer)
        @if($printer->device_type == 'terminal')
            window.terminals[`{{$printer->id}}`] = {
                id: `{{$printer->id}}`,
                name: `{{$printer->device_name}}`,
                ip: `{{$printer->device_ip}}`,
                port: `{{$printer->device_port}}`,
                type: `{{$printer->device_type}}`,
            }
        @endif
    @endforeach

</script>
@endsection
