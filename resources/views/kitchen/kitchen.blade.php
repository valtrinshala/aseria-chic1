@extends('layouts.kitchen')
@section('title', 'Kitchen')
@section('setup-script')
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
@endsection
@section('page-script')
    @vite('resources/assets/js/custom/apps/kitchen/cards.js')
    @vite('resources/assets/js/custom/apps/sales/sales-print.js')
@endsection
@section('page-style')
    <link href="{{ asset('assets/css/kitchen.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid p-0">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container kitchen-layout-container container-xxl h-100 p-0">
                    <div class="primary-table-content d-flex overflow-scroll position-relative">
                        <div id="current-user" user="{{ $user['name'] }}"></div>
                        <div class="no-orders-pop d-none position-absolute err-center bg-white">
                            <h1>{{ __('There are no orders.') }}</h1>
                        </div>
                        <div id="ordering" class="d-flex">
                        </div>
                    </div>

                    <div class="bottom-bar">
                        <div class="bar d-flex justify-content-center mx-4 px-4 card rounded-0">
                            <div class="d-flex w-100 align-items-center justify-content-end gap-4">
                                <div class="left d-flex gap-4 align-items-center w-100 justify-content-end">
                                    <div class="name ms-4">
                                        <div class="fw-bold">{{ $user->name }}</div>
                                        <div class="secondary-text">{{ $user->random_id }}</div>
                                    </div>
                                    <div class="search w-50">
                                        <i class="ki-duotone ms-2 ki-magnifier fs-2 text-gray-500 position-absolute top-50 translate-middle-y ms-0">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <input type="text" class="search-input form-control ps-10" name="search" value="" placeholder="{{__("Search orders by name or SKU")}}" data-kt-search-element="input" />
                                        <span class="search-spinner position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-1" data-kt-search-element="spinner">
                                            <span class="spinner-border h-15px w-15px align-middle text-gray-500"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="right d-flex gap-4 align-items-center justify-content-end">
                                    <div class="card-pagination">
                                        <div class="d-flex gap-4 buttons">
                                            <button class="previous pag-btn">&lt;</button>
                                            <div class="d-flex pag-numbers gap-4">
                                            </div>
                                            <button class="next pag-btn">&gt;</button>
                                        </div>
                                    </div>
                                    <div class="options">
                                    <button onclick="location.reload()" class="btn btn-primary d-flex align-items-center gap-3">
                                        <svg id="Layer_1" class="text-white" width="20" height="20" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 118.04 122.88"><path d="M16.08,59.26A8,8,0,0,1,0,59.26a59,59,0,0,1,97.13-45V8a8,8,0,1,1,16.08,0V33.35a8,8,0,0,1-8,8L80.82,43.62a8,8,0,1,1-1.44-15.95l8-.73A43,43,0,0,0,16.08,59.26Zm22.77,19.6a8,8,0,0,1,1.44,16l-10.08.91A42.95,42.95,0,0,0,102,63.86a8,8,0,0,1,16.08,0A59,59,0,0,1,22.3,110v4.18a8,8,0,0,1-16.08,0V89.14h0a8,8,0,0,1,7.29-8l25.31-2.3Z"/></svg>
                                        {{ __('Refresh') }}
                                    </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ack-modal" tabindex="-1" aria-labelledby="acknowledge-modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content p-10">
                <div class="modal-body">
                    <div class="title mb-9 text-center">
                        <h3 class="fs-1 mb-9 d-inline-block">{{ __('Order') }} #</h3>
                        <h3 class="order-id fs-1 d-inline-block"></h3>
                        <h3 class="d-inline-block fs-1">{{ __('has been refunded') }}</h3>
                    </div>

                    <div class="mb-15">
                        <h4>{{ __('Cancellation reason') }}</h4>
                        <p class="order-reason"></p>
                    </div>

                    <div class="ack-button">
                        <button class="w-100 acknowledge-current-order text-center py-4 btn btn-primary d-flex align-items-center justify-content-center gap-3">
                            {{ __('Acknowledge') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
