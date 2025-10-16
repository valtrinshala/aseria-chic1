@extends('layouts.sections.commonMaster')

@section('page-style')
    <link href="{{ asset('assets/css/kitchen.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('masterContent')
{{--    @php $categories = json_decode(file_get_contents(base_path('resources/menu/verticalMenu.json')))->menu @endphp--}}
{{--    @php $user = auth()->user() @endphp--}}

    <div class="kitchen-layout-container fs-16px d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <div class="d-flex flex-row flex-row-fluid mt-0" id="kt_app_wrapper">
                <div class="d-flex flex-column flex-nowrap navbar justify-content-between align-items-center pt-4 pb-0">
                    <div class="top">
                        <a href="/" class="d-block">
                            <img width=40 src="{{ $external_settings['second_image'] }}">
                        </a>
                    </div>
                    <div class="custom-pad bottom d-flex flex-column align-items-center gap-1 min-custom-width">
                        <a href="" class="nav-link d-flex flex-column gap-2">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <input type="hidden" name="from" value="kitchen" autocomplete="off">
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
                        <a href="{{ route('kitchen.settings') }}" class="@if(Route::current()->uri == 'admin/kitchen/settings') {{ 'active' }} @endif nav-link d-flex flex-column gap-2" title="" data-bs-toggle="tooltip" data-bs-placement="right" >
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
                        <a href="{{ route('kitchen.list') }}" class="@if(Route::current()->uri == 'admin/kitchen-list') {{ 'active' }} @endif nav-link d-flex flex-column gap-2" title="" data-bs-toggle="tooltip" data-bs-placement="right" >
                            <div class="header-icon d-flex justify-content-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                  <g id="icon" transform="translate(-27 -757)">
                                    <rect id="Rectangle_174" data-name="Rectangle 174" width="24" height="24" transform="translate(27 757)" fill="#264653" opacity="0"/>
                                    <path id="task_FILL0_wght300_GRAD0_opsz48" d="M188.058-841.489l6.77-6.758-.94-.94-5.83,5.842-3.119-3.119-.928.928ZM181.823-836a1.752,1.752,0,0,1-1.282-.539,1.752,1.752,0,0,1-.539-1.282v-20.356a1.752,1.752,0,0,1,.539-1.282,1.752,1.752,0,0,1,1.282-.539H192.8l6.151,6.151v16.028a1.752,1.752,0,0,1-.539,1.282,1.752,1.752,0,0,1-1.282.539Zm10.258-17.2v-5.364H181.823a.371.371,0,0,0-.267.121.371.371,0,0,0-.121.267v20.356a.371.371,0,0,0,.121.267.371.371,0,0,0,.267.121h15.3a.371.371,0,0,0,.267-.121.371.371,0,0,0,.121-.267V-853.2Zm-10.647-5.364v0Z" transform="translate(-150.475 1616.999)" fill="#264653"/>
                                  </g>

                                  @if (false)
                                  <g id="icon" class="active-part" transform="translate(-27 -757)">
                                    <rect id="Rectangle_174" data-name="Rectangle 174" width="24" height="24" transform="translate(27 757)" fill="#fff" opacity="0"/>
                                    <path id="task_FILL0_wght300_GRAD0_opsz48" d="M181.823-836a1.752,1.752,0,0,1-1.282-.539,1.752,1.752,0,0,1-.539-1.282v-20.356a1.752,1.752,0,0,1,.539-1.282,1.752,1.752,0,0,1,1.282-.539H192.8l6.151,6.151v16.028a1.752,1.752,0,0,1-.539,1.282,1.752,1.752,0,0,1-1.282.539Z" transform="translate(-150.475 1616.999)" fill="#5d4bdf"/>
                                    <path id="task_FILL0_wght300_GRAD0_opsz48-2" data-name="task_FILL0_wght300_GRAD0_opsz48" d="M188.058-841.489l6.77-6.758-.94-.94-5.83,5.842-3.119-3.119-.928.928Z" transform="translate(-150.475 1616.999)" fill="#fff"/>
                                  </g>
                                  @endif
                                </svg>
                            </div>
                            <div>{{ __('History') }}</div>
                        </a>
                        <a href="{{ route('kitchen.index') }}" class="@if(Route::current()->uri == 'admin/kitchen') {{ 'active' }} @endif  nav-link d-flex flex-column gap-2" title="" data-bs-toggle="tooltip" data-bs-placement="right" >
                            <div class="header-icon d-flex justify-content-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                  <g id="icon" transform="translate(-27 -757)">
                                    <rect id="Rectangle_174" data-name="Rectangle 174" width="24" height="24" transform="translate(27 757)" fill="#264653" opacity="0"/>
                                    <g id="home_FILL0_wght300_GRAD0_opsz48" transform="translate(-152.001 1572.767)" fill="none">
                                      <path d="M 200.6009979248047 -793.1671142578125 L 200.6009979248047 -807.0368041992188 L 191.0009918212891 -814.0346069335938 L 181.4009857177734 -807.0368041992188 L 181.4009857177734 -793.1671142578125 L 187.6406860351562 -793.1671142578125 L 187.6406860351562 -801.8682861328125 L 194.3612670898438 -801.8682861328125 L 194.3612670898438 -793.1671142578125 L 200.6009979248047 -793.1671142578125 M 202.0009918212891 -791.76708984375 L 192.9612731933594 -791.76708984375 L 192.9612731933594 -800.46826171875 L 189.0406951904297 -800.46826171875 L 189.0406951904297 -791.76708984375 L 180.0009918212891 -791.76708984375 L 180.0009918212891 -807.748779296875 L 191.0009918212891 -815.76708984375 L 202.0009918212891 -807.748779296875 L 202.0009918212891 -791.76708984375 Z" stroke="none" fill="#264653"/>
                                    </g>
                                  </g>

                                  @if (false) 
                                  <g id="icon" class="active-part" transform="translate(-27 -757)">
                                    <rect id="Rectangle_174" data-name="Rectangle 174" width="24" height="24" transform="translate(27 757)" fill="#5d4bdf" opacity="0"/>
                                    <path id="home_FILL0_wght300_GRAD0_opsz48" d="M180-791.767v-15.982l11-8.018,11,8.018v15.982h-9.04v-8.7h-3.921v8.7Z" transform="translate(-152.001 1572.767)" fill="#5d4bdf"/>
                                  </g>
                                  @endif
                                </svg>
                            </div>
                            <div>{{ __('Home') }}</div>
                        </a>
                    </div>

                </div>


                <div class="app-main flex-column flex-row-fluid" id="kt_app_main" style="background-color: #ededed7a">
                    <div class="d-flex flex-column flex-column-fluid">
                        <div id="kt_app_toolbar" class="app-toolbar h-100">
                            @yield('content')
                        </div>
                    </div>
                </div>

                <script>
                {
                    const searchQueryIns = document.getElementsByClassName('search-input');
                    for (let searchQueryIn of searchQueryIns) {
                        let bouncing = null;
                        searchQueryIn.addEventListener('keyup', e => {
                            clearTimeout(bouncing);
                            bouncing = setTimeout(() => {
                                const orders = document.querySelectorAll('[order].holder');
                                for (let order of orders) {
                                    const terms = order.querySelectorAll('.searchable-text');
                                    order.classList.add('d-none');
                                    for (let term of terms) {
                                        if (term.textContent.toLowerCase().includes(searchQueryIn.value.toLowerCase())) order.classList.remove('d-none');
                                    }
                                }
                            }, 200);
                        })
                    }
                }
                </script>
            </div>
        </div>
    </div>

    <script>
        window.keys.confirmBtn = `{{ __('Confirm order') }}`;
        window.keys.printBtn = `{{ __('Print order') }}`;

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

        window.keys.formsDeliveryProceed = `{{ __('You cannot proceed without selecting a form of delivery') }}`;
        window.keys.failedConnection = `{{ __('failed to connect, please reconfigure your devices.') }}`;
        window.keys.failedConnectionReason = `{{ __('failed to connect for external reasons, please contact support and show this message') }}`;
        window.keys.printerConigurationConfirmed = `{{ __('Printer configuration has been confirmed') }}`;
        window.keys.unexpectedError = `{{ __('Unexpected error has occured') }}`;
        window.keys.comingSoon = `{{ __('Coming soon') }}`;
        window.keys.connected = `{{ __('Connected') }}`;
        window.keys.connectionFailReason = `{{ __('was not able to connect because') }}`;
        window.keys.printerOrderDoesNotExist = `{{ __("Print order does not exist, alert development team as this error should never appear") }}`;

    </script>
@endsection
