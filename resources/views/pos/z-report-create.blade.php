@extends('layouts.blank-view')
@section('title', 'Create eKiosk')
@section('page-script')
    @vite('resources/assets/js/custom/apps/utils.js')
    @vite('resources/assets/js/custom/apps/pos/z-report.js')
@endsection

@section('content')
    <div class="app-main flex-column flex-row-fluid bg-violet" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container h-100">
                    <div
                        class="tab-content d-flex justify-content-center flex-column flex-lg-row align-items-center h-100">
                        <div class="tab-pane fade show active d-flex justify-content-center"
                             id="kt_ecommerce_add_product_general"
                             role="tab-panel">
                            <div class="d-flex flex-column gap-7 gap-lg-10 ">
                                <div class="card card-flush w-50 align-self-center w-600px">
                                    <div class="card-body">
                                        <form id="create-z-rep" action="{{ route('create.zReport') }}" method="post">
                                            @csrf
                                            <div class="spacing gap-4 d-flex flex-column">
                                                <div class="top-info d-flex justify-content-between">
                                                    <div>
                                                        <h2 class="text-left">{{ __('Drawer amount') }}</h2>
                                                        <p class="mb-0">{{ __('Please write how much money you have at the start of your schedule') }}</p>
                                                    </div>
                                                    <div>
                                                        <div class="">
                                                            <a href="#" class="btn font-span fw-bold h-100 d-flex align-items-center gray-border show-key-pads">{{ __('Hide keyboard') }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="saldo" class="form-control my-3 input saldo-ui" value="0">
                                                <div class="key-pad-display text-center py-2">
                                                    <span class="display-text saldo-ui" currency-symbol="{{ $settings['currency_symbol'] }}" currency-left="{{ $settings['currency_symbol_on_left'] ? '1' : '0' }}"></span>
                                                </div>

                                                <div class="key-pad on-white gap-0" targets="saldo-ui">
                                                        <button value="7" class="btn rounded-start-2 rounded-bottom-0">7</button>
                                                        <button value="8" class="btn">8</button>
                                                        <button value="9" class="btn rounded-end-2 rounded-bottom-0">9</button>

                                                        <button value="4" class="btn">4</button>
                                                        <button value="5" class="btn">5</button>
                                                        <button value="6" class="btn">6</button>

                                                        <button value="1" class="btn">1</button>
                                                        <button value="2" class="btn">2</button>
                                                        <button value="3" class="btn">3</button>

                                                        <button value="." class="btn btn-back flex-grow-1 rounded-start-2 rounded-top-0">.</button>
                                                        <button value="0" class="btn">0</button>
                                                        <button class="btn btn-back rounded-end-2 flex-grow-1 rounded-top-0" value="back">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="18" viewBox="0 0 24 18">
                                                                <path id="backspace_FILL1_wght300_GRAD0_opsz48" d="M146.487-722,140-731l6.513-9H164v18Zm4.222-4.1,3.871-3.8,3.872,3.8,1.165-1.129L155.71-731l3.85-3.776-1.151-1.129-3.828,3.8-3.871-3.8-1.151,1.129L153.451-731l-3.893,3.776Z" transform="translate(-140.001 739.999)" />
                                                            </svg>
                                                        </button>
                                                </div>
                                                <button @disabled(true) type="submit" class="btn btn-primary w-100 disable-after-submit disabled-by-default">{{ __('Acknowledge') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

