@extends('layouts.main-view')
@section('title', 'Queue')
@section('setup-script')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
    @vite('resources/assets/js/custom/apps/queue-management/list/list.js')
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
                                    <g id="kiosk-active" transform="translate(-8677 -6517)">
                                        <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24"
                                            transform="translate(8677 6517)" fill="none" />
                                        <path id="jamboard_kiosk_FILL0_wght300_GRAD0_opsz48"
                                            d="M105.284-798.525v-1.433h6v-4.251h-9.462a1.752,1.752,0,0,1-1.282-.539,1.752,1.752,0,0,1-.539-1.282v-12.146a1.752,1.752,0,0,1,.539-1.282,1.752,1.752,0,0,1,1.282-.539h20.356a1.752,1.752,0,0,1,1.282.539,1.752,1.752,0,0,1,.539,1.282v12.146a1.752,1.752,0,0,1-.539,1.282,1.752,1.752,0,0,1-1.282.539h-9.462v4.251h6v1.433Z"
                                            transform="translate(8576.999 7338.262)" fill="#5d4bdf" />
                                    </g>
                                </svg>
                                <span
                                class="px-4 page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0">
                                    {{ __('Queue Management') }}</span>
                            </div>
                            <div class="card-toolbar d-flex h-45px">
                                @include('settings.search', ['label' =>'Search queue by name, or queue ID'])
                                <div class="d-flex justify-content-end w-125px h-45px " data-kt-customer-table-toolbar="base">
                                    <div class="menu menu-sub menu-sub-dropdown w-300px" data-kt-menu="true"
                                         id="kt-toolbar-filter">
                                        <div class="separator border-gray-200"></div>
                                    </div>
                                    <a href="{{ route('queueManagement.create') }}" class="btn btn-primary text-nowrap w-100 border-0">{{ __('Add new') }}</a>
                                </div>
                                <div class="d-flex justify-content-end align-items-center d-none"
                                     data-kt-customer-table-toolbar="selected">
                                    <div class="fw-bold me-5">
                                        <span class="me-2" data-kt-customer-table-select="selected_count"></span>{{__('Selected')}}
                                    </div>
                                    <button type="button" class="btn btn-danger"
                                            data-kt-customer-table-select="delete_selected">{{__('Delete Selected')}}
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
                                    <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Queue name') }}</th>
                                    <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Created by') }}</th>
                                    <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Key') }}</th>
                                    <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Created Date') }}</th>
                                    <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Authentication Code') }}</th>
                                    <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Queue URL') }}</th>
                                    <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Location') }}</th>
                                    <th class="text-gray-900 fw-bold min-w-125px py-10">{{ __('Status') }}</th>
                                    <th class="text-gray-900 fw-bold pe-12 text-end min-w-70px py-10">{{ __('Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                @foreach($queueManagement as $queue)
                                    <tr>
                                        <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="{{ $queue->id }}"/>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('queueManagement.edit', ['queueManagement' => $queue->id ]) }}"
                                               class="text-gray-800 text-hover-primary mb-1">{{ $queue->name }}</a>
                                        </td>
                                        <td>
                                            <div
                                            class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                <a href="{{ route('user.edit', ['user' => $user->id]) }}"
                                                    class="text-gray-800 text-hover-primary mb-1">{{ $queue->user?->name }}</a>
                                            </div>
                                        </td>
                                        <td>
                                            <div
                                            class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                {{ $queue->key }}
                                            </div>
                                        </td>
                                        <td>
                                            <div
                                            class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                {{ \Carbon\Carbon::parse($queue->created_at)->format('d.m.Y') }}
                                            </div>
                                        </td>
                                        <td class="text-dark" style="-webkit-text-security: disc;">{{ substr($queue->authentication_code, 0, 30) }}</td>
                                        <td>
                                            <a target="_blank" href="{{ route('queue.results' , ['locationName' => strtolower(preg_replace('/\W+/', '_', $queue->location->name)), 'eKioskKey' => $queue->key]) }}" class="text-dark">{{ rtrim(getenv('APP_URL'), '/') }}/{{ $queue->url }}</a>
                                            <svg class="cursor-pointer" onclick="copyLink('{{ rtrim(getenv('APP_URL'), '/') }}/{{ $queue->url }}')" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000" height="20px" width="20px" version="1.1" id="Layer_1" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve">
                                                <g id="Text-files">
                                                    <path d="M53.9791489,9.1429005H50.010849c-0.0826988,0-0.1562004,0.0283995-0.2331009,0.0469999V5.0228   C49.7777481,2.253,47.4731483,0,44.6398468,0h-34.422596C7.3839517,0,5.0793519,2.253,5.0793519,5.0228v46.8432999   c0,2.7697983,2.3045998,5.0228004,5.1378999,5.0228004h6.0367002v2.2678986C16.253952,61.8274002,18.4702511,64,21.1954517,64   h32.783699c2.7252007,0,4.9414978-2.1725998,4.9414978-4.8432007V13.9861002   C58.9206467,11.3155003,56.7043495,9.1429005,53.9791489,9.1429005z M7.1110516,51.8661003V5.0228   c0-1.6487999,1.3938999-2.9909999,3.1062002-2.9909999h34.422596c1.7123032,0,3.1062012,1.3422,3.1062012,2.9909999v46.8432999   c0,1.6487999-1.393898,2.9911003-3.1062012,2.9911003h-34.422596C8.5049515,54.8572006,7.1110516,53.5149002,7.1110516,51.8661003z    M56.8888474,59.1567993c0,1.550602-1.3055,2.8115005-2.9096985,2.8115005h-32.783699   c-1.6042004,0-2.9097996-1.2608986-2.9097996-2.8115005v-2.2678986h26.3541946   c2.8333015,0,5.1379013-2.2530022,5.1379013-5.0228004V11.1275997c0.0769005,0.0186005,0.1504021,0.0469999,0.2331009,0.0469999   h3.9682999c1.6041985,0,2.9096985,1.2609005,2.9096985,2.8115005V59.1567993z"/>
                                                    <path d="M38.6031494,13.2063999H16.253952c-0.5615005,0-1.0159006,0.4542999-1.0159006,1.0158005   c0,0.5615997,0.4544001,1.0158997,1.0159006,1.0158997h22.3491974c0.5615005,0,1.0158997-0.4542999,1.0158997-1.0158997   C39.6190491,13.6606998,39.16465,13.2063999,38.6031494,13.2063999z"/>
                                                    <path d="M38.6031494,21.3334007H16.253952c-0.5615005,0-1.0159006,0.4542999-1.0159006,1.0157986   c0,0.5615005,0.4544001,1.0159016,1.0159006,1.0159016h22.3491974c0.5615005,0,1.0158997-0.454401,1.0158997-1.0159016   C39.6190491,21.7877007,39.16465,21.3334007,38.6031494,21.3334007z"/>
                                                    <path d="M38.6031494,29.4603004H16.253952c-0.5615005,0-1.0159006,0.4543991-1.0159006,1.0158997   s0.4544001,1.0158997,1.0159006,1.0158997h22.3491974c0.5615005,0,1.0158997-0.4543991,1.0158997-1.0158997   S39.16465,29.4603004,38.6031494,29.4603004z"/>
                                                    <path d="M28.4444485,37.5872993H16.253952c-0.5615005,0-1.0159006,0.4543991-1.0159006,1.0158997   s0.4544001,1.0158997,1.0159006,1.0158997h12.1904964c0.5615025,0,1.0158005-0.4543991,1.0158005-1.0158997   S29.0059509,37.5872993,28.4444485,37.5872993z"/>
                                                </g>
                                            </svg>
                                            <script>
                                                function copyLink(link) {
                                                    let tempInput = document.createElement('input');
                                                    tempInput.value = link;
                                                    document.body.appendChild(tempInput);
                                                    tempInput.select();
                                                    document.execCommand('copy');
                                                    document.body.removeChild(tempInput);
                                                }
                                            </script>
                                        </td>
                                        <td class="text-gray-800 text-hover-primary mb-1">
                                            {{$queue->location?->name }} - {{ $queue->location?->location }}</td>
                                        <td><div class="badge {{ $queue->status ? 'badge-light-success' : 'badge-light-danger'}}">{{ $queue->status ? __('Active') : __('Inactive') }}</div></td>
                                        <td class="text-end">
                                            <a href="#"
                                               class="btn btn-sm btn-light btn-flex btn-center border-0 btn-active-light-primary"
                                               data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">{{__('Actions')}}
                                                <i class="ki-duotone ki-right fs-5 ms-1"></i></a>
                                            <div
                                                class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                data-kt-menu="true">
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('queueManagement.edit', ['queueManagement' => $queue->id ]) }}"
                                                       class="menu-link px-3">{{ __('View') }}</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3 text-danger"
                                                       data-queueManagement-id = {{ $queue->id }}
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
