<!DOCTYPE html>

<html lang="{{ session()->get('locale') ?? app()->getLocale() }}">

<head>
    <meta charset="utf-8"/>

    <title>@yield('title') | {{ getenv('APP_NAME') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo/favicon.png') }}"/>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700"/>

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.bundle.css') }}"> {{--Bootstrap 5--}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/global/plugins.bundle.css') }}"> {{-- Theme css--}}

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script> {{--JQuery Library--}}
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script> {{--Theme Library--}}
    @yield('setup-script')
    @yield('page-style')
</head>

<body>


    <style>
        /* Set default rem to 16px */

        @if (!isset($default_size) || $default_size == true)
        html, body {
            font-size: 16px !important;
        }

        @endif

        .navbar .custom-pad .nav-link {
            padding: 12px 9px;
        }

        .navbar .nav-link {
            width: 100%;
            position: relative;
            text-align: center;
            font-size: 0.875em;
            background: #404040;
        }

        .navbar .nav-link.active {
            background-color: #7145D6;
        }

        .navbar .min-custom-width {
            min-width: 4.875em;
            overflow: hidden;
        }

        .pos-navbar {
            font-size: 16px !important;
            background: #252525;
            color: #fff;
        }

        .pos-navbar svg, .pos-navbar svg * {
            fill: #fff;
        }

        /*
        .navbar .nav-link.active::before {
            content: '';
            position: absolute;
            width: 0.25em;
            background: var(--prim-color);
            top: 0;
            left: 0;
            bottom: 0;
        }
        */

        .h-100vmin {
            height: 100vmin;
        }
    </style>
    <script>
        @if($errors->first('pos_error') != null || $errors->first('kitchen_error') != null)
        Swal.fire({
            text: `{{ $errors->first('pos_error') != null ? $errors->first('pos_error') : $errors->first('kitchen_error')}}`,
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok!",
            customClass: {
                confirmButton: "btn btn-primary"
            }
        })
        @endif
    </script>

    <div class="pos-layout-container fs-16px d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <div class="d-flex flex-row flex-row-fluid mt-0" id="kt_app_wrapper">
                <div class="d-flex flex-column flex-nowrap justify-content-between align-items-center pb-0 pt-6 pos-navbar navbar">
                    <div class="top">
                        <a href="/" class="d-block">
                            <img width=40 src="{{ $external_settings['second_image'] }}">
                        </a>

                    </div>

                    <div class="custom-pad bottom d-flex flex-column align-items-center min-custom-width gap-1">
                        @if ($user->role_id == config('constants.role.adminId') || ($user && !in_array('pos_home_module', $user->userRole->permissions)))
                        <a href="" class="nav-link d-flex flex-column gap-2">
                            <form action="{{ route('logout') }}" class="logout-form" method="POST">
                                @csrf
                                <input type="hidden" name="from" value="pos" autocomplete="off">
                                <button href="#" class="btn font-span w-100 p-0">
                                    <div class="d-flex gap-2 flex-column justify-content-center align-items-center">
                                        <div class="">
                                            <svg class="logout" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                <g id="icon" transform="translate(-27 -757)">
                                                    <rect id="Rectangle_174" data-name="Rectangle 174" width="24" height="24" transform="translate(27 757)" fill="#ed1c24" opacity="0"/>
                                                    <g id="arrow-down-bold" transform="translate(26.033 764.991) rotate(90)">
                                                        <g id="Group_29" data-name="Group 29" transform="translate(-1.068 -16.167)">
                                                            <g id="Group_28" data-name="Group 28">
                                                                <path id="Path_6" data-name="Path 6" d="M.232.247a.786.786,0,0,1,1.078,0l3.775,3.62L8.861.247a.786.786,0,0,1,1.078,0,.71.71,0,0,1,0,1.034L5.625,5.419a.786.786,0,0,1-1.078,0L.232,1.281a.71.71,0,0,1,0-1.034Z" transform="translate(-0.009 9.567)" fill="#ed1c24"/>
                                                                <rect id="Rectangle_57" data-name="Rectangle 57" width="1.846" height="13.467" rx="0.923" transform="translate(4.154)" fill="#ed1c24"/>
                                                            </g>
                                                        </g>
                                                        <path id="Subtraction_3" data-name="Subtraction 3" d="M1.523,13.551H0V4C0,1.794,1.518,0,3.384,0H18.615C20.481,0,22,1.794,22,4v9.55H20.477V4a2.054,2.054,0,0,0-1.862-2.2H3.384A2.054,2.054,0,0,0,1.523,4V13.55Z" transform="translate(-6.991 -24.967)" fill="#ed1c24"/>
                                                    </g>
                                                </g>
                                            </svg>
                                        </div>
                                        <div class="text-danger">{{ __('Log out') }}</div>
                                    </div>
                                </button>
                            </form>
                        </a>
                        @endif

                        @if ($user->role_id == config('constants.role.adminId') || ($user && !in_array('pos_home_module', $user->userRole->permissions)))
                        <a href="{{ route('pos.settings') }}" class="@if(Route::current()->uri == 'admin/pos/settings') {{ 'active' }} @endif nav-link d-flex flex-column gap-2" title="" data-bs-toggle="tooltip" data-bs-placement="right" >
                            <div class="header-icon d-flex justify-content-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                  <g id="icon" transform="translate(-27 -757)">
                                    <rect id="Rectangle_174" data-name="Rectangle 174" width="24" height="24" transform="translate(27 757)" fill="#264653" opacity="0"/>
                                    <g id="settings" transform="translate(-8650 -5760)">
                                      <path id="settings_FILL0_wght300_GRAD0_opsz48" d="M115.228-836l-.58-3.8a8.6,8.6,0,0,1-1.418-.643,7.893,7.893,0,0,1-1.31-.893l-3.586,1.608-2.563-4.462,3.241-2.349a6.212,6.212,0,0,1-.093-.726q-.028-.394-.028-.726t.028-.714q.028-.393.093-.763l-3.241-2.361,2.563-4.426,3.574,1.584a9.845,9.845,0,0,1,1.322-.887,6.941,6.941,0,0,1,1.406-.617l.592-3.828h5.086l.58,3.809a10.688,10.688,0,0,1,1.416.639,6.714,6.714,0,0,1,1.275.884l3.636-1.584,2.55,4.426-3.29,2.347a4.415,4.415,0,0,1,.111.758q.022.381.022.721t-.028.7a6.944,6.944,0,0,1-.1.772l3.278,2.342-2.563,4.462-3.611-1.62a10.718,10.718,0,0,1-1.294.915,5.8,5.8,0,0,1-1.4.621l-.58,3.809Zm1.169-1.433h2.735l.461-3.525a7.512,7.512,0,0,0,1.905-.753,8.56,8.56,0,0,0,1.691-1.27l3.35,1.428,1.283-2.237-2.965-2.155q.128-.561.2-1.058a6.758,6.758,0,0,0,.074-1,7.69,7.69,0,0,0-.064-1.009,7.614,7.614,0,0,0-.212-1.021l2.99-2.179-1.283-2.237-3.387,1.428a7.059,7.059,0,0,0-1.63-1.319,5.114,5.114,0,0,0-1.966-.7l-.437-3.525H116.4l-.424,3.513a6.5,6.5,0,0,0-1.962.715,7.354,7.354,0,0,0-1.671,1.308l-3.362-1.416-1.283,2.237,2.965,2.143a7.9,7.9,0,0,0-.215,1.015,7.474,7.474,0,0,0-.074,1.064,7.047,7.047,0,0,0,.074,1.027q.074.5.2,1.015l-2.952,2.155,1.283,2.237,3.35-1.416A7.505,7.505,0,0,0,114-841.7a7.722,7.722,0,0,0,1.961.753Zm1.337-7.008a3.514,3.514,0,0,0,2.562-1.036A3.407,3.407,0,0,0,121.348-848a3.407,3.407,0,0,0-1.052-2.523,3.514,3.514,0,0,0-2.562-1.036,3.52,3.52,0,0,0-2.555,1.036A3.4,3.4,0,0,0,114.12-848a3.4,3.4,0,0,0,1.058,2.523A3.52,3.52,0,0,0,117.734-844.44ZM117.771-848Z" transform="translate(8571.229 7376.999)" fill="#264653"/>
                                    </g>
                                  </g>
                                </svg>
                            </div>
                            <div>{{ __('Settings') }}</div>
                        </a>
                        @endif

                        @if ($user->role_id == config('constants.role.adminId') || ($user && in_array('pos_history_view_module', $user->userRole->permissions)))
                        <a href="/admin/pos/history" class="@if(Route::current()->uri == 'admin/pos/history') {{ 'active' }} @endif nav-link d-flex flex-column gap-2" title="" data-bs-toggle="tooltip" data-bs-placement="right" >
                            <div class="header-icon d-flex justify-content-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                  <g id="icon" transform="translate(-27 -757)">
                                    <rect id="Rectangle_174" data-name="Rectangle 174" width="24" height="24" transform="translate(27 757)" fill="#264653" opacity="0"/>
                                    <path id="task_FILL0_wght300_GRAD0_opsz48" d="M188.058-841.489l6.77-6.758-.94-.94-5.83,5.842-3.119-3.119-.928.928ZM181.823-836a1.752,1.752,0,0,1-1.282-.539,1.752,1.752,0,0,1-.539-1.282v-20.356a1.752,1.752,0,0,1,.539-1.282,1.752,1.752,0,0,1,1.282-.539H192.8l6.151,6.151v16.028a1.752,1.752,0,0,1-.539,1.282,1.752,1.752,0,0,1-1.282.539Zm10.258-17.2v-5.364H181.823a.371.371,0,0,0-.267.121.371.371,0,0,0-.121.267v20.356a.371.371,0,0,0,.121.267.371.371,0,0,0,.267.121h15.3a.371.371,0,0,0,.267-.121.371.371,0,0,0,.121-.267V-853.2Zm-10.647-5.364v0Z" transform="translate(-150.475 1616.999)" fill="#264653"/>
                                  </g>
                                </svg>
                            </div>
                            <div>{{ __('History') }}</div>
                        </a>
                        @endif

                        @if ($user->role_id == config('constants.role.adminId') || ($user && in_array('pos_e_kiosk_module', $user->userRole->permissions)))
                            @if ($location->e_kiosk)
                            <a href="/admin/pos/eKiosk" class="@if(Route::current()->uri == 'admin/pos/eKiosk') {{ 'active' }} @endif nav-link d-flex flex-column gap-2" title="" data-bs-toggle="tooltip" data-bs-placement="right" >
                                <div class="header-icon d-flex justify-content-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                      <g id="users-active" transform="translate(-8719 -6517)">
                                        <path id="Path_167/POS" data-name="Path 167" d="M.647,24A.642.642,0,0,1,0,23.364V.636A.642.642,0,0,1,.647,0H21.353A.641.641,0,0,1,22,.636V23.364a.641.641,0,0,1-.647.636Zm.647-1.272H20.706V1.272H1.294Zm11.728-1.51a.64.64,0,0,1,0-1.272H14.64a.64.64,0,0,1,0,1.272Zm-5.662,0a.64.64,0,0,1,0-1.272H8.978a.64.64,0,0,1,0,1.272ZM3.4,18.517a.641.641,0,0,1-.647-.636V3.338A.641.641,0,0,1,3.4,2.7H18.6a.641.641,0,0,1,.647.636V17.881a.641.641,0,0,1-.647.636Zm.647-1.272H17.956V3.974H4.044Z" transform="translate(8720 6517)" fill="#264653"/>
                                      </g>
                                    </svg>
                                </div>
                                <div>{{ __('E-Kiosk') }}</div>
                            </a>
                            @endif
                        @endif

                        @if ($user->role_id == config('constants.role.adminId') || ($user && in_array('pos_ready_view_module', $user->userRole->permissions)))
                        <a href="/admin/pos/ready" class="@if(Route::current()->uri == 'admin/pos/ready') {{ 'active' }} @endif nav-link d-flex flex-column gap-2" title="" data-bs-toggle="tooltip" data-bs-placement="right" >
                            <div class="header-icon d-flex justify-content-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                  <g id="icon" transform="translate(-27 -757)">
                                    <rect id="Rectangle_174" data-name="Rectangle 174" width="24" height="24" transform="translate(27 757)" fill="#264653" opacity="0"/>
                                    <path id="task_alt_FILL0_wght300_GRAD0_opsz48" d="M111.993-836a12.051,12.051,0,0,1-4.715-.919,11.785,11.785,0,0,1-3.814-2.545,11.791,11.791,0,0,1-2.544-3.815A12.066,12.066,0,0,1,100-848a12.026,12.026,0,0,1,.919-4.7,11.8,11.8,0,0,1,2.543-3.817,11.912,11.912,0,0,1,3.813-2.556,11.934,11.934,0,0,1,4.715-.929,11.941,11.941,0,0,1,4.165.715,12.325,12.325,0,0,1,3.5,1.969l-1.017,1.042a10.2,10.2,0,0,0-3.041-1.7,10.713,10.713,0,0,0-3.6-.594,10.2,10.2,0,0,0-7.512,3.049A10.22,10.22,0,0,0,101.433-848a10.22,10.22,0,0,0,3.046,7.518,10.2,10.2,0,0,0,7.512,3.049,10.2,10.2,0,0,0,7.512-3.049A10.22,10.22,0,0,0,122.55-848a11.206,11.206,0,0,0-.148-1.839,11.22,11.22,0,0,0-.432-1.737l1.087-1.1a11.711,11.711,0,0,1,.691,2.254,12.32,12.32,0,0,1,.234,2.422,11.965,11.965,0,0,1-.928,4.719,11.924,11.924,0,0,1-2.553,3.816,11.78,11.78,0,0,1-3.812,2.546A11.992,11.992,0,0,1,111.993-836Zm-1.864-6.678-4.806-4.829,1.068-1.081,3.738,3.741L122.9-857.628l1.1,1.069Z" transform="translate(-73.001 1616.999)" fill="#264653"/>
                                  </g>
                                </svg>
                            </div>
                            <div>{{ __('Ready') }}</div>
                        </a>
                        @endif

                        @if ($user->role_id == config('constants.role.adminId') || ($user && in_array('pos_kitchen_module', $user->userRole->permissions)))
                            @if ($location->kitchen)
                                <a href="/admin/pos/kitchen" class="@if(Route::current()->uri == 'admin/pos/kitchen') {{ 'active' }} @endif nav-link d-flex flex-column gap-2" title="" data-bs-toggle="tooltip" data-bs-placement="right" >
                                    <div class="header-icon d-flex justify-content-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22.833" height="24" viewBox="0 0 22.833 24">
                                          <g id="orders" transform="translate(-29 -671.188)">
                                            <path id="Path_3" data-name="Path 3" d="M3.878,17.041V15.563H18.954v1.477Zm0-5.2V10.359H18.954v1.477Zm0-5.171V5.188H18.954V6.665Z" transform="translate(29 671.074)" fill="#264653"/>
                                            <path id="Path_131" data-name="Path 131" d="M20.759,22.347,18.847,24,17,22.4,15.149,24l-1.911-1.653L11.326,24,9.46,22.386,7.594,24,5.682,22.347,3.771,24,1.86,22.347,0,24V1.937A1.863,1.863,0,0,1,.572.572,1.863,1.863,0,0,1,1.937,0H20.9A1.863,1.863,0,0,1,22.26.572a1.863,1.863,0,0,1,.572,1.365c0,20.356,0,20.355,0,22.063Zm.55-1.046V1.937a.4.4,0,0,0-.129-.284.4.4,0,0,0-.284-.129H1.937a.4.4,0,0,0-.284.129.4.4,0,0,0-.129.284V21.3H21.309ZM1.524,1.937v0Z" transform="translate(29 671.188)" fill="#264653"/>
                                          </g>
                                        </svg>
                                    </div>
                                    <div>{{ __('Kitchen') }}</div>
                                </a>
                            @endif
                        @endif

                        @if ($user->role_id == config('constants.role.adminId') || ($user && in_array('pos_home_module', $user->userRole->permissions)))
                        <a href="/admin/pos" class="@if(Route::current()->uri == 'admin/pos') {{ 'active' }} @endif  nav-link d-flex flex-column gap-2" title="" data-bs-toggle="tooltip" data-bs-placement="right" >
                            <div class="header-icon d-flex justify-content-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                  <g id="icon" transform="translate(-27 -757)">
                                    <rect id="Rectangle_174" data-name="Rectangle 174" width="24" height="24" transform="translate(27 757)" fill="#264653" opacity="0"/>
                                    <g id="home_FILL0_wght300_GRAD0_opsz48" transform="translate(-152.001 1572.767)" fill="none">
                                      <path d="M 200.6009979248047 -793.1671142578125 L 200.6009979248047 -807.0368041992188 L 191.0009918212891 -814.0346069335938 L 181.4009857177734 -807.0368041992188 L 181.4009857177734 -793.1671142578125 L 187.6406860351562 -793.1671142578125 L 187.6406860351562 -801.8682861328125 L 194.3612670898438 -801.8682861328125 L 194.3612670898438 -793.1671142578125 L 200.6009979248047 -793.1671142578125 M 202.0009918212891 -791.76708984375 L 192.9612731933594 -791.76708984375 L 192.9612731933594 -800.46826171875 L 189.0406951904297 -800.46826171875 L 189.0406951904297 -791.76708984375 L 180.0009918212891 -791.76708984375 L 180.0009918212891 -807.748779296875 L 191.0009918212891 -815.76708984375 L 202.0009918212891 -807.748779296875 L 202.0009918212891 -791.76708984375 Z" stroke="none" fill="#264653"/>
                                    </g>
                                  </g>

                                  @if(false && Route::current()->uri == 'admin/pos')
                                  <g id="icon" class="active-part" transform="translate(-27 -757)">
                                    <rect id="Rectangle_174" data-name="Rectangle 174" width="24" height="24" transform="translate(27 757)" fill="#5d4bdf" opacity="0"/>
                                    <path id="home_FILL0_wght300_GRAD0_opsz48" d="M180-791.767v-15.982l11-8.018,11,8.018v15.982h-9.04v-8.7h-3.921v8.7Z" transform="translate(-152.001 1572.767)" fill="#5d4bdf"/>
                                  </g>
                                  @endif
                                </svg>
                            </div>
                            <div>{{ __('Home') }}</div>
                        </a>
                        @endif
                    </div>

                </div>


                <div class="app-main flex-column flex-row-fluid h-100vmin overflow-auto" id="kt_app_main" style="background-color: #ededed7a">
                    <div class="d-flex flex-column flex-column-fluid">
                        <div id="kt_app_toolbar" class="app-toolbar h-100">
                            @yield('content')
                        </div>
                    </div>
                </div>

                <script>
                const searchQueryIns = document.getElementsByClassName('search-input');
                for (let searchQueryIn of searchQueryIns) {
                    let bouncing = null;
                    searchQueryIn.addEventListener('keyup', e => {
                        clearTimeout(bouncing);
                        bouncing = setTimeout(() => {
                            const orders = document.querySelectorAll('[order]');
                            for (let order of orders) {
                                const terms = order.querySelectorAll('.searchable-text');
                                order.classList.add('d-none');
                                for (let term of terms) {
                                    if (term.textContent.includes(searchQueryIn.value)) order.classList.remove('d-none');
                                }
                            }
                        }, 200);
                    })
                }

                </script>
            </div>
        </div>
    </div>
    <script>

        window.invoicePrintSettings = JSON.parse(`{!! json_encode(App\Helpers\Helpers::invoicePrintSettings()); !!}`);
        window.keys = [];
        window.keys.confirmBtn = `{{ __('Confirm order') }}`;
        window.keys.printBtn = `{{ __('Print order') }}`;

        window.keys.confirmButtonOk = `{{__('Ok, got it!')}}`,
        window.keys.waitingOrder = `{{ __('Waiting') }}`;
        window.keys.progressOrder = `{{ __('In Progress') }}`;
        window.keys.completedOrder = `{{ __('Completed') }}`;
        window.keys.assignmentMe = `{{ __('Assign to @me') }}`;
        window.keys.assignment = `{{ __('Assigned to') }}`;
        window.keys.note = `{{ __('Note') }}`;
        window.keys.extraAdds = `{{ __('Adds') }}`;
        window.keys.extraRemoved = `{{ __('No') }}`;
        window.keys.ordered = `{{ __('Ordered') }}`;
        window.keys.printOrder = `{{ __('Print order') }}`;
        window.keys.editOrder = `{{ __('Edit order') }}`;
        window.keys.cancelOrder = `{{ __('Cancel order') }}`;
        window.keys.refundOrder = `{{ __('Refund order') }}`;
        window.keys.acknowledge = `{{ __('Acknowledge') }}`;
        window.keys.payment = `{{ __('Continue to payment') }}`;
        window.keys.com_orders = `{{ __('orders completed') }}`;
        window.keys.formsDeliveryProceed = `{{ __('You cannot proceed without selecting a form of delivery') }}`;
        window.keys.failedConnection = `{{ __('failed to connect, please reconfigure your devices.') }}`;
        window.keys.failedConnectionReason = `{{ __('failed to connect for external reasons, please contact support and show this message') }}`;
        window.keys.printerConigurationConfirmed = `{{ __('Printer configuration has been confirmed') }}`;
        window.keys.unexpectedError = `{{ __('Unexpected error has occured') }}`;
        window.keys.comingSoon = `{{ __('Coming soon') }}`;
        window.keys.connected = `{{ __('Connected') }}`;
        window.keys.connectionFailReason = `{{ __('was not able to connect because') }}`;
        window.keys.printerOrderDoesNotExist = `{{ __("Print order does not exist, alert development team as this error should never appear") }}`;
        window.keys.readybtn = `{{ __('Ready') }}`;
        window.keys.printerNotConfigured = `{{ __("Printer is not configured") }}`;

        window.routes = {};
        window.routes.pos_index = `{{ route('pos.index')}}`;
    </script>
    @yield('page-script')
</body>

</html>
