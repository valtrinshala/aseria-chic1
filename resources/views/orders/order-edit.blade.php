@extends('layouts.main-view')
@section('title', 'Edit Product')
@section('page-script')
    @vite('resources/assets/js/custom/apps/sales/sales-print.js')
@endsection
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid m-9 mt-0">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6 h-100px">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex justify-content-center flex-wrap me-3">
                        <a href="{{ route('order.index') }}"
                            class="page-heading d-flex text-gray-900 fs-3 flex-column justify-content-center my-0">{{ __('Orders') }}
                            > </a>
                        <span
                            class="page-heading d-flex text-info fw-bold fs-3 flex-column justify-content-center my-0 m-4">{{ __('Order') }}
                            #{{ $order->order_number }}</span>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex align-items-center gap-3 gap-lg-5">
                            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                                <a href="{{ route('pos.index') }}?order_id={{$order->id}}"
                                    class="btn @disabled($order->is_paid || $order->is_cancelled) @if($user->role_id != config('constants.role.adminId') && !in_array('pos_module', $user->userRole->permissions)) d-none @endif btn-light btn-flex btn-center btn-white border-0 justify-content-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21"
                                        viewBox="0 0 24 24" class="me-3">
                                        <g id="orders" transform="translate(-28.416 -671.188)">
                                            <rect id="Rectangle_96" data-name="Rectangle 96" width="24" height="24"
                                                transform="translate(28.416 671.188)" fill="none" />
                                            <path id="Path_3" data-name="Path 3"
                                                d="M3.878,17.041V15.563H18.954v1.477Zm0-5.2V10.359H18.954v1.477Zm0-5.171V5.188H18.954V6.665Z"
                                                transform="translate(29 671.074)" fill="#264653" />
                                            <path id="Path_131" data-name="Path 131"
                                                d="M20.759,22.347,18.847,24,17,22.4,15.149,24l-1.911-1.653L11.326,24,9.46,22.386,7.594,24,5.682,22.347,3.771,24,1.86,22.347,0,24V1.937A1.863,1.863,0,0,1,.572.572,1.863,1.863,0,0,1,1.937,0H20.9A1.863,1.863,0,0,1,22.26.572a1.863,1.863,0,0,1,.572,1.365c0,20.356,0,20.355,0,22.063Zm.55-1.046V1.937a.4.4,0,0,0-.129-.284.4.4,0,0,0-.284-.129H1.937a.4.4,0,0,0-.284.129.4.4,0,0,0-.129.284V21.3H21.309ZM1.524,1.937v0Z"
                                                transform="translate(29 671.188)" fill="#264653" />
                                        </g>
                                    </svg>
                                    {{ __('Edit order') }}
                                </a>
                            </div>
                            <div class="show-app d-flex justify-content-end" data-kt-customer-table-filter="print">
                                <input type="hidden" id="printer-string" value="{{ $printString }}">
                                <a class="btn btn-light btn-flex btn-center btn-white border-0 justify-content-center print-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21"
                                        viewBox="0 0 36 36" class="me-3">
                                        <g id="print-settings" transform="translate(-8719 -6517)">
                                            <rect id="Rectangle_95" data-name="Rectangle 95" width="36" height="36"
                                                transform="translate(8719 6517)" fill="none" />
                                            <path id="print_FILL0_wght300_GRAD0_opsz48"
                                                d="M136.6-799.776v-5.854H120.587v5.854h-2.1v-7.914H138.7v7.914Zm-22.959,2.061h0Zm26.343,4.366a1.476,1.476,0,0,0,1.07-.444,1.413,1.413,0,0,0,.452-1.041,1.411,1.411,0,0,0-.452-1.041,1.467,1.467,0,0,0-1.061-.444,1.457,1.457,0,0,0-1.07.444,1.426,1.426,0,0,0-.443,1.041,1.426,1.426,0,0,0,.443,1.041,1.447,1.447,0,0,0,1.061.443ZM136.6-779.435v-8.822H120.587v8.822Zm2.1,2.061H118.487v-7.8h-6.946v-10.541a3.886,3.886,0,0,1,1.2-2.887,4.062,4.062,0,0,1,2.96-1.175h25.789a4.06,4.06,0,0,1,2.963,1.175,3.89,3.89,0,0,1,1.2,2.887v10.541H138.7Zm4.845-9.86v-8.49a1.9,1.9,0,0,0-.592-1.419,2.026,2.026,0,0,0-1.466-.573H115.7a2.006,2.006,0,0,0-1.466.583,1.916,1.916,0,0,0-.592,1.418v8.48h4.845v-3.084H138.7v3.084Z"
                                                transform="translate(8608.405 7327.531)" fill="#000000" />
                                        </g>
                                    </svg>
                                    {{ __('Print') }}
                                </a>
                            </div>
                            <div class="show-app d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                                <div class="btn btn-danger" id="delete-order" data-order-id="{{ $order->id }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960"
                                        width="24">
                                        <path fill="#ffffff"
                                            d="M292.309-140.001q-29.923 0-51.115-21.193-21.193-21.192-21.193-51.115V-720h-40v-59.999H360v-35.384h240v35.384h179.999V-720h-40v507.691q0 30.308-21 51.308t-51.308 21H292.309ZM680-720H280v507.691q0 5.385 3.462 8.847 3.462 3.462 8.847 3.462h375.382q4.616 0 8.463-3.846 3.846-3.847 3.846-8.463V-720ZM376.155-280h59.999v-360h-59.999v360Zm147.691 0h59.999v-360h-59.999v360ZM280-720v520-520Z" />
                                    </svg>
                                    {{ __('Delete') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="pageId" value="{{ $order->id }}">
            <div id="kt_app_content_container" class="app-container container-xxl">
                <form id="kt_ecommerce_add_modifier_form"
                    class="form d-flex flex-column flex-lg-row justify-content-between">
                    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-7 flex-grow-1 m-0 justify-content-between">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="kt_ecommerce_add_product_general" role="tab-panel">
                                <div class="d-flex flex-column gap-7 gap-lg-7">
                                    <div class="card card-body pt-0 me-5">
                                        <table class="table align-middle table-row-dashed fs-6 gy-5"
                                            id="kt_customers_table">
                                            <thead>
                                                <tr class="text-start text-gray-600 fs-6 gs-0">
                                                    <th class="text-gray-900 fw-bold pt-7 pb-7">
                                                        {{ __('Items') }}</th>
                                                    <th class="text-gray-900 fw-bold text-end pt-7 pb-7">
                                                        {{ __('Price') }}</th>
                                                    <th class="text-gray-900 fw-bold text-end pt-7 pb-7">
                                                        {{ __('Quantity') }}</th>
                                                    <th class="text-gray-900 fw-bold text-end pt-7 pb-7">
                                                        {{ __('Tax rate') }}</th>
                                                    <th class="text-gray-900 fw-bold text-end pt-7 pb-7">
                                                        {{ __('Tax amount') }}</th>
                                                    <th class="text-gray-900 fw-bold text-end pt-7 pb-7">
                                                        {{ __('Discount amount') }}</th>
                                                    <th class="fw-bold text-end pt-7 pb-7 text-info">
                                                        {{ __('Total') }}</th>
                                                    <th class="fw-bold text-end pt-7 pb-7 text-danger">
                                                        {{ __('Cost') }}</th>
                                                    <th class="fw-bold text-end pt-7 pb-7 text-success">
                                                        {{ __('Profit') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody class="fw-semibold text-gray-600">
                                                @foreach ($order->items as $product)
                                                    <tr>
                                                        <td>
                                                            <div
                                                                class="text-gray-900 fw-bold fs-10.5 flex-column justify-content-center my-0">
                                                                {{ $product['name'] }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div
                                                                class="text-end text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                                @price($product['price_neto'], $settings)
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div
                                                                class="text-end text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                                {{ $product['quantity'] }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div
                                                                class="text-end text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                                {{ $product['tax_name'] }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div
                                                                class="text-end text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                                @price($product['tax_amount'], $settings)
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div
                                                                class="text-end text-gray-800 fs-10.5 flex-column justify-content-center my-0">
                                                                @price($product['discount_amount'], $settings)
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div
                                                                class="text-end text-info fs-10.5 flex-column justify-content-center my-0">
                                                                @price($product['sub_total'], $settings)
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div
                                                                class="text-end text-danger fs-10.5 flex-column justify-content-center my-0">
                                                                @price($product['cost'], $settings)
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div
                                                                class="text-end text-success fs-10.5 flex-column justify-content-center my-0">
                                                                @price($product['profit'], $settings)
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="border-top border-1 border-dark">
                                                </tr>
                                                <tr>
                                                    <td>

                                                    </td>
                                                    <td>
                                                        <div
                                                            class="text-end text-gray-900 fw-bold fs-10.5 flex-column justify-content-center my-0">
                                                            @price(($order->payable_after_all - $order->tax_amount) , $settings)
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div
                                                            class="text-end text-gray-900 pt-2 pb-2 fw-bold fs-10.5 flex-column justify-content-center my-0">
                                                            {{ $order->cart_total_items }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div
                                                            class="text-end text-gray-900 fw-bold fs-10.5 flex-column justify-content-center my-0">
{{--                                                            @price($order->tax_amount, $settings)--}}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div
                                                            class="text-end text-gray-900 fw-bold fs-10.5 flex-column justify-content-center my-0">
                                                            @price($order->tax_amount, $settings)
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div
                                                            class="text-end text-gray-900 fw-bold fs-10.5 flex-column justify-content-center my-0">
                                                            @price($order['discount_amount'], $settings)
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div
                                                            class="text-end text-info fw-bold fs-10.5 flex-column justify-content-center my-0">
                                                            @price($order->payable_after_all, $settings)
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div
                                                            class="text-end text-danger fw-bold fs-10.5 flex-column justify-content-center my-0">
                                                            @price($order->cart_total_cost, $settings)
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div
                                                            class="text-end text-success fw-bold fs-10.5 flex-column justify-content-center my-0">
                                                            @price($order->profit_after_all, $settings)
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-flush py-4 flex-shrink-0 w-30em">
                        <div class="card-header justify-content-center">
                            <div class="card-title ">
                                <div class="page-heading d-flex text-center text-dark fw-bold fs-3 flex-column">
                                    <img style=""
                                        src="data:image/png;base64,{{ $barcodeImage }}" alt="Barcode" />
                                    {{ (int)$order->order_receipt }}
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-1 ">
                            <thead class="fw-semibold text-gray-600">
                                <tr>
                                    <td>
                                        <div class="row">
                                            <div class="col text-gray-900 fs-6 flex-column">
                                                {{ __('Created at') }}
                                            </div>
                                            <div class="col text-end text-gray-900 fw-bold fs-6 flex-column">
                                                {{ $order->created_at }} </div>
                                        </div>
                                    </td>
                                    <hr>
                                    <td>
                                        <div class="row">
                                            <div class="col text-gray-900 fs-6 flex-column">
                                                {{ __('Last updated at') }}
                                            </div>
                                            <div class="col text-end text-gray-900 fw-bold fs-6 flex-column">
                                                {{ $order->updated_at }}</div>
                                        </div>
                                    </td>
                                    <hr>
                                    <td>
                                        <div class="row">
                                            <div class="col text-gray-900 fs-6 flex-column">
                                                {{ __('Order taken by') }}
                                            </div>
                                            <div class="col text-end text-gray-900 fw-bold fs-6 flex-column">
                                                {{ $order->taker?->name ?? ($order->eKiosk?->name. '-'. __('eKiosk') ?? 'eKiosk') }}
                                            </div>
                                        </div>
                                    </td>
                                    <hr>
                                    <td>
                                        <div class="row">
                                            <div class="col text-gray-900 fs-6 flex-column">
                                                {{ __('Cooking started') }}
                                            </div>
                                            <div class="col text-end text-gray-900 fw-bold fs-6 flex-column">
                                                {{ $order->is_preparing == 1 ? 'Yes' : 'No' }}
                                            </div>
                                        </div>
                                    </td>
                                    <hr>
                                    <td>
                                        <div class="row">
                                            <div class="col text-gray-900 fs-6 flex-column">
                                                {{ __('Checkout by') }}
                                            </div>
                                            <div class="col text-end text-gray-900 fw-bold fs-6 flex-column my-0">
                                                {{ $order->save_order ? __('Unpaid') : ($order->biller?->name ?? __("Card").'-'.__("eKiosk")) }}
                                            </div>
                                        </div>
                                    </td>
                                    <hr>
                                    <td>
                                        <div class="row">
                                            <div class="col text-gray-900 fs-6 flex-column">
                                                {{ __('Discount') }}
                                            </div>
                                            <div class="col text-end text-gray-900 fw-bold fs-6 flex-column my-0">
                                                @price($order->discount_amount, $settings)</div>
                                        </div>
                                    </td>
                                    <hr>
                                    <td>
                                        <div class="row">
                                            <div class="col text-gray-900 fs-6 flex-column">
                                                {{ __('Kitchen note') }}
                                            </div>
                                        </div>
                                    </td>
                                    <br>
                                    <span class="text-gray-900 fw-bold fs-6 flex-column justify-content-center">
                                        @if ($order->note_for_chef)
                                            @php
                                                $notes = json_decode($order->note_for_chef);
                                            @endphp
                                            @if (is_array($notes))
                                                {{ implode(', ', $notes) }}
                                            @else
                                                {{ $notes ?? '--' }}
                                            @endif
                                        @else
                                            --
                                        @endif
                                    </span>

                                </tr>
                            </thead>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
