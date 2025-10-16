@extends('layouts.main-pos')
@section('title', 'POS')
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
@endsection
@section('content')
<style>
    body {
        background: #F6F8F8;
    }
</style>

<input type="hidden" id="device-type" value="pos">
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
                    <p class="title">{{ __('POS') }}</p>
                    <div class="content p-6 rounded">
                        <div class="row-item">
                            <p class="row-name">{{ __('Receipt printer') }}</p>

                            <div class="w-50">
                                <select id="receipt_printer" name="receipt_printer" class="form-select mb-2"
                                        data-control="select2" data-placeholder="{{ __('Select printer') }}"
                                        data-allow-clear="true">
                                    <option selected></option>
                                    <option value=" ">{{ __('No Printer') }}</option>
                                    @foreach($printers as $printer)
                                        @if($user->role_id == config('constants.role.adminId') || ($user && in_array('pos_home_module', $user->userRole->permissions)))
                                            @if($printer->device_type == 'printer' || $printer->device_type == 'sticker_printer')
                                                <option value="{{ $printer->id }}">{{ $printer->device_name }}</option>
                                            @endif
                                        @else
                                            @if($printer->device_type == 'printer' )
                                                <option value="{{ $printer->id }}">{{ $printer->device_name }}</option>
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row-item">
                            <p class="row-name">{{ __('Payment terminal') }}</p>

                            <div class="w-50">
                                <select id="payment_terminal" name="payment_terminal" class="form-select mb-2"
                                        data-control="select2" data-placeholder="{{ __('Select terminal') }}"
                                        data-allow-clear="true">
                                    <option selected></option>
                                    <option value=" ">{{ __('No Terminal') }}</option>
                                    @if ($manualPayment)
                                        <option value="1">{{ __('Manual') }}</option>
                                    @endif
                                    @foreach($printers as $printer)

                                        @if($printer->device_type == 'terminal')
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
                            <p class="row-name">{{ __('Receipt printer') }}</p>

                            <div>
                                <div class="form-switch p-0 ps-3">
                                    <input class="form-check-input m-0" id="receipt_printer_status" name="receipt_printer_status" type="checkbox" role="checkbox">
                                </div>
                            </div>
                        </div>

                        <div class="row-item">
                            <p class="row-name">{{ __('Payment terminal') }}</p>

                            <div>
                                <div class="form-switch p-0 ps-3">
                                    <input class="form-check-input m-0" id="payment_terminal_status" name="payment_terminal_status" type="checkbox" role="checkbox">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($settingsPin)
            <div class="settings-card bg-white rounded p-6 mt-5">
                <div class="card-header d-flex justify-content-between">
                    <div class="left">
                        <h4>{{ __('Terminal Options') }} </h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="pos-section section-list">
                        <a id="startup-terminal" href="#" type="button" class="btn btn-secondary h-45px">
                            {{ __('Start up') }}
                        </a>

                        <a id="activate-terminal" href="#" type="button" class="btn btn-secondary h-45px">
                            {{ __('Activate Terminal') }}
                        </a>

                        <a id="restart-terminal" href="#" type="button" class="btn btn-secondary h-45px">
                            {{ __('Restart') }}
                        </a>

                        <a id="manual-reprint" href="#" type="button" class="btn btn-secondary h-45px">
                            {{ __('Manual Reprint') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif


    </div>
    <div class='sidebar'>
        <div class="settings-card bg-white rounded p-6">
            <div class="card-header mb-6">
                <h4>{{ __('Other settings') }}</h4>
            </div>

            <div class="card-body">
                <div class="row-item flex-column">
                    <p class="row-name">{{ __('POS language') }}</p>

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

                <div class="row-item flex-column mt-5">
                    <p class="row-name">{{ __('Terminal') }}</p>

                    <a id="open-pin" type="button" class="btn btn-secondary px-3 h-45px w-100">
                        {{ __('Options') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="pinModal" class="modal fade" data-backdrop="true">
    <div class="modal-dialog mh-100 mh-padd-3 my-0">
        <div class="modal-content py-10 px-10">
            <form action="{{ route('pos.settings.post') }}" method="POST">
                @csrf
                <div class="input-group rounded  justify-content-center mb-2 mt-2 w-100">
                    <div class="input-group rounded position-relative justify-content-center w-100">
                        <label class="position-absolute pe-none left-0 added-input-text">{{ __('Pin') }}:</label>
                        <input type="password" immune="true" name="pin" class="py-2 text-black form-control text-center rounded custom-input-bg" />
                    </div>
                </div>

                <div class="input-group rounded  justify-content-center mb-2 mt-2 w-100">
                    <div class="input-group rounded position-relative justify-content-center w-100">
                        <input type="submit" value="Open" immune="true" class="py-2 text-black form-control text-center rounded custom-input-bg" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    window.printers = {};
    window.terminals = {};

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById("open-pin").addEventListener('click', e => {
            $('#pinModal').modal('show');
        });
    })

    function display_test_message(message) {
        Swal.fire({
            text: message,
            icon: "success",
            buttonsStyling: false,
            confirmButtonText: `{{ __("Ok, got it!") }}`,
            customClass: {
                confirmButton: "btn btn-primary"
            }
        });
    }


    @if($settingsPin)
        window.connected_terminal = true;
        window.terminal_ip = '';

        let printerSettings = localStorage.getItem('printer_settings');
        if (printerSettings != null) {
            try {
                printerSettings = JSON.parse(printerSettings);

                if ('terminal' in printerSettings && printerSettings.terminal && printerSettings.terminal.status == 1) {
                    window.terminal_ip = printerSettings.terminal.terminal.ip;
                    startTerminalDiscovery();
                } else {
                    // No terminal or the terminal status is 0
                }
            } catch(err) {
                // No printer connected
            }
        } else {
            // No printer selected (no messages here until instructed otherwise)
        }

        function terminal_confirmation(message) {
            Swal.fire({
                text: message,
                icon: "success",
                buttonsStyling: false,
                confirmButtonText: `{{ __("Ok, got it!") }}`,
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
        }

        function connected_terminals(terminal_ips_str) {
            const terminal_ips = terminal_ips_str.split(',');
            if (terminal_ips.includes(window.terminal_ip)) {
                if (window.terminal_alert) {
                    Swal.fire({
                        text: nonce + ` {{ __("terminal connected") }}`,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: `{{ __("Ok, got it!") }}`,
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }

                window.connected_terminal = true;
            } else {
                Swal.fire({
                    text: `{{ __('Terminal') }}` + ` {{ __('with the configured ip did not exist') }}`,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                })
            }
        }


        const startUpTerminal = document.getElementById('startup-terminal');
        const activateTerminal = document.getElementById('activate-terminal');
        const restartTerminal = document.getElementById('restart-terminal');
        const manualReprint = document.getElementById('manual-reprint');

        startUpTerminal.addEventListener('click', e => {
            if (!window.connected_terminal) {
                Swal.fire({
                    text: `{{ __('Terminal') }}` + ` {{ __('with the configured ip did not exist') }}`,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                })

                return;
            }

            if ('Mine' in window) {
                window.Mine.postMessage(`terminal_stp:${window.terminal_ip}`);
            } else {
                Swal.fire({
                    text: `terminal_stp:${window.terminal_ip}`,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
            }
        });

        restartTerminal.addEventListener('click', e => {
            if (!window.connected_terminal) {
                Swal.fire({
                    text: `{{ __('Terminal') }}` + ` {{ __('with the configured ip did not exist') }}`,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                })
                return;
            }

            if ('Mine' in window) {
                window.Mine.postMessage(`terminal_rst:${window.terminal_ip}`);
            } else {
                Swal.fire({
                    text: `terminal_rst:${window.terminal_ip}`,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
            }
        });

        activateTerminal.addEventListener('click', e => {
            if (!window.connected_terminal) {
                Swal.fire({
                    text: `{{ __('Terminal') }}` + ` {{ __('with the configured ip did not exist') }}`,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                })
                return;
            }

            if ('Mine' in window) {
                window.Mine.postMessage(`terminal_act:${window.terminal_ip}`);
            } else {
                Swal.fire({
                    text: `terminal_act:${window.terminal_ip}`,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
            }
        });


        manualReprint.addEventListener('click', e => {
            if (!window.connected_terminal) {
                Swal.fire({
                    text: `{{ __('Terminal') }}` + ` {{ __('with the configured ip did not exist') }}`,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                })
                return;
            }

            if ('Mine' in window) {
                window.Mine.postMessage(`terminal_manrprt:${window.terminal_ip}`);
            } else {
                Swal.fire({
                    text: `terminal_manrprt:${window.terminal_ip}`,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
            }
        });
    @endif

    @foreach($printers as $printer)
        @if($printer->device_type == 'printer' || $printer->device_type == 'sticker_printer')
            printers[`{{$printer->id}}`] = {
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
            terminals[`{{$printer->id}}`] = {
                id: `{{$printer->id}}`,
                name: `{{$printer->device_name}}`,
                ip: `{{$printer->device_ip}}`,
                port: `{{$printer->device_port}}`,
                type: `{{$printer->device_type}}`,
                compatibility_port: `{{$printer->terminal_compatibility_port}}`,
                socket_mode: `{{$printer->terminal_socket_mode}}`,
                terminal_type: `{{$printer->terminal_type}}`
            }
        @endif
    @endforeach

</script>
@endsection
