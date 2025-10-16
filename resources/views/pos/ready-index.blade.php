@extends('layouts.main-pos')
@section('title', 'Ready')
@section('setup-script')
    <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
@endsection
@section('page-script')
    @vite('resources/assets/js/custom/apps/kitchen/cards-preview.js')
    @vite('resources/assets/js/custom/apps/sales/sales-print.js')
@endsection
@section('page-style')
    <link href="{{ asset('assets/css/kitchen.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
    <style>
        /*
        .food-items > div:not(:last-child) {
            --bottom-bit: 1em;
            margin-bottom: var(--bottom-bit);
            position: relative;
        }
        .food-items > div:not(:last-child)::after {
            content: '';
            position: absolute;
            bottom: calc(calc(var(--bottom-bit) * .5) * -1);
            left: 0;
            right: 0;
            height: 2px;
            background: lightgrey;
        }
        */

        .form-check {
            display: none;
        }
    </style>
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid p-0">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container kitchen-layout-container container-xxl h-100 p-0">
                    <div class="primary-table-content d-flex overflow-hidden position-relative">
                        <div class="no-orders-pop d-none position-absolute err-center bg-white">
                            <h1>{{ __('There are no prepared orders.') }}</h1>
                        </div>

                        @php
                        $buttons = ['ack'];

                        if ($user) {
                            /*
                            if ($user->role_id == config('constants.role.adminId') || ($user && in_array('pos_print_order_slip', $user->userRole->permissions))) {
                                $buttons[] = 'print';
                            }
                            */

                            if ($user->role_id == config('constants.role.adminId') || ($user && in_array('refund_order_module', $user->userRole->permissions))) {
                                $buttons[] = 'refund';
                            } else {
                                // $buttons[] = 'refund_disabled';
                            }

                            if ($user->role_id == config('constants.role.adminId') || ($user && in_array('cancel_order_module', $user->userRole->permissions))) {
                                // $buttons[] = 'cancel';
                            } else {
                                // $buttons[] = 'cancel_disabled';
                            }
                        }

                        $buttons_string = implode(',', $buttons);
                        @endphp
                        <div id="ordering" class="d-flex" type="ready" buttons="{{$buttons_string}}">
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

    <script>
        $(document).on('click', '.refresh', function(e) {
            location.reload();
        });

        $(document).on('click', '.ack-button', function(e) {
            const card = $(this).closest('.order-card');
            let orderId = card[0].getAttribute('order');

            $.ajax({
                method: 'POST',
                url: '/admin/pos/ack-order',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    order_id: orderId
                },
                success: resp => {
                    if (resp.status == 2) {
                        location.href = resp.redirect_uri;
                        return;
                    }

                    if (resp.status == 1) {
                        // These will change with the library in the next update
                        Swal.fire({
                            text: resp.message,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                        return;
                    }

                    if (resp.status == 0) {
                        card.remove();
                    }
                },
                error: err => {
                    console.error(err);
                    Swal.fire({
                        text: 'An unexpected error occured',
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
            });
        });

        $(document).on('click', '.ack-ready-button', function(e) {
            const card = $(this).closest('.order-card');
            let orderId = card[0].getAttribute('order');

            this.setAttribute('disabled', 'disabled');

            $.ajax({
                method: 'POST',
                url: '/admin/pos/queReady',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    ready: true,
                    order_id: orderId
                },
                success: resp => {
                    if (resp.status == 2) {
                        location.href = resp.redirect_uri;
                        return;
                    }

                    if (resp.status == 1) {
                        // These will change with the library in the next update
                        Swal.fire({
                            text: resp.message,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                        this.removeAttribute('disabled');
                        return;
                    }

                    if (resp.status == 0) {
                        this.classList.add('d-none');
                        const acknowledgeBtn = card[0].querySelectorAll('.ack-button');
                        for (let ackB of acknowledgeBtn) {
                            ackB.classList.remove('d-none');
                        }
                    } else {
                        this.removeAttribute('disabled');
                    }
                },
                error: err => {
                    console.error(err);
                    this.removeAttribute('disabled');
                    Swal.fire({
                        text: `{{ __('An unexpected error occured') }}`,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
            });
        });


        $(document).on('click', '.cancel-order', function(e) {
            const card = $(this).closest('.order-card');
            let orderId = card[0].getAttribute('order');

            const modal = $('#cancel-confirmation');
            if (modal.length == 0) {
                Swal.fire({
                    text: `{{ __('There was an issue with the layout, please try again after refreshing.') }}`,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
                return;
            }

            modal.attr('order', orderId);
            modal.attr('cancel_amount', card[0].getAttribute('cancel_amount'));
            modal.modal('show');
            modal.each((index, mod) => {
                mod.clearInputs();
                mod.eventAttached = orderId => {
                    $(`.order-card[order="${orderId}"]`).remove();
                }
            });
        });
        /*
        $(document).on('click', '.cancel-order', function(e) {
            const card = $(this).closest('.order-card');
            let orderId = card[0].getAttribute('order');

            $.ajax({
                method: 'POST',
                url: '/admin/pos/cancel-order',
                data: {
                    order_id: orderId
                },
                success: resp => {
                    if (resp.status == 2) {
                        location.href = resp.redirect_uri;
                        return;
                    }

                    if (resp.status == 1) {
                        // These will change with the library in the next update
                        Swal.fire({
                            text: resp.message,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                        return;
                    }

                    if (resp.status == 0) {
                        card.remove();
                    }
                },
                error: err => {
                    console.error(err);
                    Swal.fire({
                        text: 'An unexpected error occured',
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
            });
        });
         */

        function assign_order() {
            Swal.fire({
                text: "Work in Progress",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, got it!",
                customClass: {
                    confirmButton: "btn fw-bold btn-primary",
                }
            });
        }

        function setPagination() {}
    </script>

    @include('pos/cancel-modal')
@endsection
