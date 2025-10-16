@extends('layouts.main-view')
@section('title', 'Payment methods')
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid  m-9 mt-0 p-0">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="shadow-none bg-transparent border-0">
                        <div class="border-0 px-0 py-8 d-flex justify-content-between">
                            <div class="card-title d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <g id="payment-methods-active" transform="translate(-8719 -6517)">
                                        <rect id="Rectangle_95" data-name="Rectangle 95" width="24" height="24"
                                            transform="translate(8719 6517)" fill="none" />
                                        <path id="Subtraction_14" data-name="Subtraction 14"
                                            d="M5779.178,1602h-20.356a1.844,1.844,0,0,1-1.822-1.826v-8.694h24v8.694a1.844,1.844,0,0,1-1.822,1.826Zm1.822-13.839h-24v-3.333a1.844,1.844,0,0,1,1.822-1.826h20.356a1.844,1.844,0,0,1,1.822,1.826v3.332Z"
                                            transform="translate(2962 4936.5)" fill="#5d4bdf" />
                                    </g>
                                </svg>

                                <span
                                    class="px-4 page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0">
                                    {{ __('Payment methods') }}</span>
                            </div>
                            <div class="card-toolbar">

                                <div class="d-flex justify-content-end gap-2 gap-lg-3"
                                    data-kt-customer-table-toolbar="base">
                                    @include('settings.goback-button')
                                    <a href="{{ route('paymentMethod.create') }}"
                                        class="btn btn-primary w-125px text-nowrap border-0">{{ __('Add new') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="background: transparent">
                        <h3 class="mt-3 mb-3">{{ __('Payment methods') }}</h3>
                        <div class="mt-1 row g-5 g-xl-6">
                            @foreach ($paymentMethods as $paymentMethod)
                                <div class="col-xl-4 m-0">
                                    <div class="card statistics-widget-1 mb-xl-6">
                                        <div class="row">
                                            <div class="col-xl-7">
                                                <div class="card-body">
                                                    <div>
                                                        <a href="{{ route('paymentMethod.edit', $paymentMethod->id) }}"
                                                            class="card-title fw-bold fs-4">{{ __($paymentMethod->name) }}</a>
                                                        <p class=" fs-5 m-0">
                                                            {{ date('d-m-Y', strtotime($paymentMethod->created_at)) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xl-5 d-flex align-items-center justify-content-center">
                                                <div class="card-body m-0 text-end text-nowrap">
                                                    <a href="{{ route('paymentMethod.edit', ['paymentMethod' => $paymentMethod->id]) }}"
                                                        class="mt-auto text-dark">{{ __('Payment details >') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
