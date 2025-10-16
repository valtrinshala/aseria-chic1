 @php
 $pin_keys = 4;
 @endphp

 @extends('layouts.blank-view')
 @section('page-script')
    @vite('resources/assets/js/custom/apps/sales/sales-print.js')
 @endsection

 @section('content')
     <div class="h-100 background-wraper bg-login-violet overflow-auto position-relative">
         <div class="card-header d-flex justify-content-center border-0 pt-lg-20">
             <div class="card-title">
                 <div
                     class="remove-image-pos page-heading d-flex flex-column text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                     <img height="60" alt="Logo" src="{{ $external_settings['image'] }}" class="logo">
                 </div>
             </div>
         </div>


         <div class="container card card-flush border-0 w-100-500px position-absolute start-50 top-50 t-50 bg-transparent">
             <div class="card-body p-0">
                <div class="d-flex mb-4 justify-content-between">
                    <div class="d-flex">
                        <a href="#" target="#UserLogin" {{ isset($users) ? '' : 'disabled' }} class="btn swap-primary bg-dark-violet py-4 px-7 rounded-0 rounded-start text-white">{{ __('User login') }}</a>
                        <a href="#" target="#PinLogin" {{ isset($users) ? '' : 'disabled' }} class="btn swap-primary bg-dark-violet py-4 px-7 rounded-0 text-white">{{ __('Pin login') }}</a>
                        <a href="#" target="#EmailLogin" class="btn swap-primary bg-dark-violet py-4 px-7 text-white rounded-0 rounded-end btn-primary">{{ __('Username login') }}</a>
                    </div>

                    <div class="d-none">
                        <a href="#" class="quit d-flex justify-content-center align-items-center gap-2 py-5 px-5 rem-custom-radius rounded-start btn btn-danger font-span fw-bold gray-border">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16.936" height="16.936" viewBox="0 0 16.936 16.936">
                                <path id="power_settings_new_FILL0_wght300_GRAD0_opsz48" d="M147.9-816.376v-9.546h1.13v9.546Zm.565,7.39a8.213,8.213,0,0,1-3.288-.664,8.716,8.716,0,0,1-2.694-1.8,8.432,8.432,0,0,1-1.82-2.664,8.031,8.031,0,0,1-.667-3.247,7.791,7.791,0,0,1,.809-3.507,8.936,8.936,0,0,1,2.266-2.867l.787.779a7.305,7.305,0,0,0-2.012,2.477,6.9,6.9,0,0,0-.72,3.117,6.965,6.965,0,0,0,2.141,5.136,7.107,7.107,0,0,0,5.2,2.122,7.1,7.1,0,0,0,5.2-2.122,6.972,6.972,0,0,0,2.137-5.136,7.06,7.06,0,0,0-.716-3.137,6.969,6.969,0,0,0-1.976-2.458l.8-.779a8.308,8.308,0,0,1,2.223,2.839,8.066,8.066,0,0,1,.8,3.535,8.031,8.031,0,0,1-.667,3.247,8.5,8.5,0,0,1-1.812,2.664,8.621,8.621,0,0,1-2.686,1.8A8.252,8.252,0,0,1,148.469-808.986Z" transform="translate(-140.001 825.922)" fill="#fff"/>
                            </svg>
                            {{ __('Quit') }}
                        </a>
                    </div>
                </div>
                <div id="EmailLogin" class="p-8 bg-dark-violet rounded targets">
                    <form action="{{ route('login') }}" method="post">
                        @csrf
                        <input type="hidden" name="from" value="{{ $from }}" autocomplete="off">
                        <input type="hidden" name="type" value="email">
                        <div class="fv-row col-12 ">
                            <div class="d-flex flex-column gap-4">
                                <div class="">
                                    <label for="email" class="required form-top">{{ __('Enter username') }}</label>
                                    <input type="text" id="exampleInputEmail1" aria-describedby="emailHelp" type="email" name="email" class="form-control login-input" placeholder="Enter username" />
                                    @error('email')
                                        <div class="text-danger">
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                                <div class="fv-row col-12">
                                    <label for="text" class="required form-top">{{ __('Enter password') }}</label>
                                    <input id="exampleInputEmail2" type="password" name="password" class="form-control login-input" placeholder="Enter password" />
                                    @error('password')
                                        <div class="text-danger">
                                            <span>{{ $message }}</span>
                                        </div>
                                    @enderror
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary fw-bold btn-block create-account-btn w-100 mt-2">{{ __('Login') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>


                <div id="UserLogin" class="p-8 bg-dark-violet rounded targets d-none">
                    @if(isset($users))
                    <form id="userForm" action="{{ route('login') }}" method="post">
                        @csrf
                        <input type="hidden" name="from" value="{{ $from }}" autocomplete="off">
                        <input type="hidden" name="type" value="user">
                        <input type="hidden" name="user_id">
                        <div class="user-login-section overflow-y-scroll h-45vh d-grid" style="grid-template-columns: repeat(3, 1fr); grid-auto-rows: max-content; gap: 16px">


                            @foreach($users as $user)
                            <a href="#" user-id="{{ $user->id }}" class="user w-auto aspect-1-5 rounded d-flex justify-content-center align-items-center" style="background: #F6F8F83D">
                                <div class="d-flex flex-column justify-content-center align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">
                                    <g id="Group_534" data-name="Group 534" transform="translate(-287 -206)">
                                        <rect id="BG" width="40" height="40" transform="translate(287 206)" fill="none"/>
                                        <circle id="Ellipse_1" data-name="Ellipse 1" cx="7.333" cy="7.333" r="7.333" transform="translate(299.667 206)" fill="#fff"/>
                                        <path id="Path_154" data-name="Path 154" d="M1.667,0H11a4.667,4.667,0,0,1,4.667,4.667s-.333,7.833-2,12S11.577,21.333,9,21.333H3.667c-2.577,0-3-.5-4.667-4.667s-2-12-2-12A4.667,4.667,0,0,1,1.667,0Z" transform="translate(300.667 224.667)" fill="#fff"/>
                                    </g>
                                    </svg>
                                    <p class="mt-4 text-white mb-0 fs-4">{{ $user->name }}</p>
                                </div>
                            </a>

                            @endforeach
                        </div>
                    </form>
                    @endif
                </div>

                <div id="PinLogin" class="p-8 bg-dark-violet rounded targets d-none">
                    @if(isset($users))
                    <form id="pinForm" action="{{ route('login') }}" method="post">
                        <input type="hidden" name="from" value="{{ $from }}" autocomplete="off">
                        <input type="hidden" name="type" value="pin">
                        <input type="hidden" name="pin">
                        @csrf
                        <div class="display">
                            <div class="display-text text-white fs-4 d-flex justify-content-center">
                                {{ _('Enter your PIN') }}*
                            </div>
                            <div class="display-keys d-flex justify-content-center gap-4 my-4">
                                @for($i = 0; $i < $pin_keys; $i++)
                                <span class="key d-inline-block"></span>
                                @endfor
                            </div>
                        </div>

                        <div class="key-pad">
                                <button value="7" class="btn rounded-start-2 rounded-bottom-0">7</button>
                                <button value="8" class="btn">8</button>
                                <button value="9" class="btn rounded-end-2 rounded-bottom-0">9</button>

                                <button value="4" class="btn">4</button>
                                <button value="5" class="btn">5</button>
                                <button value="6" class="btn">6</button>

                                <button value="1" class="btn">1</button>
                                <button value="2" class="btn">2</button>
                                <button value="3" class="btn">3</button>

                                <button value="0" class="btn rounded-start-2 rounded-top-0">0</button>
                                <button class="btn btn-back rounded-end-2 rounded-top-0" value="back">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="18" viewBox="0 0 24 18">
                                        <path id="backspace_FILL1_wght300_GRAD0_opsz48" d="M146.487-722,140-731l6.513-9H164v18Zm4.222-4.1,3.871-3.8,3.872,3.8,1.165-1.129L155.71-731l3.85-3.776-1.151-1.129-3.828,3.8-3.871-3.8-1.151,1.129L153.451-731l-3.893,3.776Z" transform="translate(-140.001 739.999)" fill="#fff"/>
                                    </svg>
                                </button>
                        </div>

                    </form>
                    @endif
                </div>


             </div>
         </div>

    </div>

    <div class="footer position-fixed bottom-0 text-white w-100 d-flex justify-content-center fs-5 gap-4 mb-2">
        <span class="date">{{ date('d.m.Y') }}</span> | <span class="time">{{ date('H:i:s')}}</span> | <span class="email"><a class="text-white" href="mailto:{{ $external_settings['email'] }}">{{ $external_settings['email'] }}</a></span> | <span class="phone"><a class="text-white" href="tel:{{ $external_settings['telephone'] }}">{{ $external_settings['telephone'] }}</a></span>
    </div>



    <style>
        .select2-container {
            z-index: 99999;
        }
    </style>


    <script>

        window.connected_printer = false;
        let printerSettings = localStorage.getItem('printer_settings');
        if (printerSettings != null) {
            try {
                printerSettings = JSON.parse(printerSettings);

                if ('receipt_printer' in printerSettings && printerSettings.receipt_printer && printerSettings.receipt_printer.status == 1) {
                    if ('Mine' in window) {
                        // Default port would be 9100 please pay attention 192.168.178.167
                        window.reconnect_string = `con:order-${printerSettings.receipt_printer.printer.name}:${printerSettings.receipt_printer.printer.ip}:${printerSettings.receipt_printer.printer.port}`;
                        window.Mine.postMessage(`con:order-${printerSettings.receipt_printer.printer.name}:${printerSettings.receipt_printer.printer.ip}:${printerSettings.receipt_printer.printer.port}`);
                    } else {
                        console.log(`con:order-${printerSettings.receipt_printer.printer.name}:${printerSettings.receipt_printer.printer.ip}:${printerSettings.receipt_printer.printer.port}`);
                    }
                } else {
                    // No printer or the printer status is 0
                }
            } catch(err) {
                // No printer connected
            }
        } else {
            // No printer selected (no messages here until instructed otherwise)
        }

        function connected(nonce) {
            window.connected_printer = true;
            if ('debugging_print' in window && window.debugging_print) alert(nonce + " connected");
            console.log('mutation');
            console.log(nonce + ' printer connected');
        }

        function failed_connection(nonce, reason, status) {
            // window.connected_printer = false;
            console.log('mutation');
            console.log(nonce, reason, status);

            // This would have been better but it's required to finish very sooooon
            if (reason == 'Succeed') {
                window.connected_printer = true;
                if ('debugging_print' in window && window.debugging_print) {
                    alert('Printed');
                }
                // Printing worked
                return;
            }

            if (false && status == 3) {
                Swal.fire({
                    text: nonce + ` {{ __('failed to connect, please reconfigure your devices.') }}`,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });

                window.connected_printer = false;
            } else {
                if (window.debugging_print) {
                    Swal.fire({
                        text: nonce + ` __("failed to connect for external reasons, please contact support and show this message:") ` + reason,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: `{{ __("Ok, got it!") }}`,
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }

                window.connected_printer = false;

                setTimeout(() => {
                    if ('Mine' in window && window.reconnect_string != '') {
                        window.Mine.postMessage(window.reconnect_string);
                    }
                }, 5000);
            }
        }


        let keypadHandler = {
            keysPressed: [],
            keyLimit: parseInt(`{{ $pin_keys }}`),
            filledEvents: [],
            addEventListener: function(fun) {
                if (typeof(fun) == 'function') {
                    this.filledEvents.push(fun);
                }
            },
            press: function(key) {
                if (isNaN(this.keyLimit)) {
                    alert('Back end problem');
                    return;
                }

                if (this.keysPressed.length == this.keyLimit) return;

                this.keysPressed.push(key)
                if (this.keysPressed.length == this.keyLimit) {
                    for (let event of this.filledEvents) {
                        event(this.getKeys());
                    }
                }
            },
            getKeys: function() {
                return this.keysPressed.join('');
            }
        }

        $(document).on('click', '.quit', function() {
            window.close();
            console.log('asdf');
        });

        $(document).on('click', '.swap-primary[disabled]', function(e) {
            e.preventDefault();
            return false;
        });

        $(document).on('click', '.swap-primary:not([disabled])', function(e) {
            e.preventDefault();
            const others = $('.swap-primary');
            others.removeClass('btn-primary');

            this.classList.add('btn-primary');

            let target = this.getAttribute('target');

            let elements = $(target);
            if (elements.length == 0) return;
            let element = elements[0];

            let otherElements = $('.targets');
            otherElements.addClass('d-none');

            element.classList.remove('d-none');

            return false;
        });

        $(document).on('click', '.user', function(e) {
            e.preventDefault();

            let user_id = this.getAttribute('user-id');
            let form = document.getElementById('userForm');
            $('input[name="user_id"]').val(user_id);
            form.submit();

            return false;
        })

        keypadHandler.addEventListener(code => {
            $('input[name="pin"]').val(code);
            let form = document.getElementById('pinForm');
            form.submit();
        });

        // I was going to add an event on keypadHandler for the display as well but it would be just waste
        $(document).on('click', '.key-pad button', function(e) {
            e.preventDefault();
            if (this.value == 'back') {
                const filledButtons = document.querySelectorAll('.display-keys .key.filled')
                if (filledButtons.length > 0) {
                    filledButtons[filledButtons.length - 1].classList.remove('filled');
                }

                keypadHandler.keysPressed.pop();
                return;
            }

            const key = this.getAttribute('value');
            keypadHandler.press(key);

            const nextEl = document.querySelector('.display-keys .key:not(.filled)');
            if (nextEl) nextEl.classList.add('filled');
            return false;
        });

        let monthlyReportCashRegister = null;
        let cashRegisterObj = null;

        $('.discard-btn').click(() => {
            $('#monthly-rep-register-months').modal('hide');
            $("#monthly-rep-register").modal('hide');
            $("#confirm-reprint-z-report").modal('hide');

        })

        $('.monthly-report-step').click(() => {
            const cashRegisterSelect = $('#cash_register')[0];
            if (cashRegisterSelect.options.length > 0) {
                for (let i = cashRegisterSelect.options.length - 1; i >= 0; i--) {
                    cashRegisterSelect.removeChild(cashRegisterSelect.options[i]);
                }
            }

            const monthsCashRegister = $('#months-cash')[0];
            if (monthsCashRegister.options.length > 0) {
                for (let i = monthsCashRegister.options.length - 1; i >= 0; i--) {
                    monthsCashRegister.removeChild(monthsCashRegister.options[i]);
                }
            }

            $.ajax({
                async: false,
                method: 'GET',
                url: '/getCashRegisters',
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
                            confirmButtonText: `{{ __("Ok, got it!") }}`,
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                        return;
                    }

                    if (resp.status == 0) {
                        cashRegisterObj = {}
                        const cashRegisters = resp.data;
                        for (let cashRegister of cashRegisters) {
                            cashRegisterObj[cashRegister.id] = cashRegister;

                            const option = document.createElement('option');
                            option.value = cashRegister.id;
                            option.textContent = cashRegister.name;

                            cashRegisterSelect.options.add(option);

                        }

                        $('#monthly-rep-register-months').modal('hide');
                        $("#confirm-reprint-z-report").modal('hide');
                        $("#monthly-rep-register").modal('show')
                        monthlyReportCashRegister = null;
                    }

                    if ('message' in resp && resp.message != '') {
                        Swal.fire({
                            text: resp.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: `{{ __("Ok, got it!") }}`,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        })
                    }
                },
                error: function(err) {
                    console.log(err);
                    Swal.fire({
                        text: `{{ __('An unexpected error occured') }}`,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: `{{ __('Ok, got it!') }}`,
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
            });

        });

        $('.select-cash-register').click(() => {
            monthlyReportCashRegister = $('#cash_register').val();
            if (monthlyReportCashRegister != '') {
                const months = cashRegisterObj[monthlyReportCashRegister].months;

                const monthsCashRegister = $('#months-cash')[0];
                const cashRegisterName = cashRegisterObj[monthlyReportCashRegister].name;

                for (let monthNumber in months) {

                    const option = document.createElement('option');
                    option.value = monthNumber;
                    option.textContent = months[monthNumber];

                    monthsCashRegister.options.add(option);

                }

                /*
                for (let monthNumber in months) {

                    const monthElement = document.createElement('a');
                    monthElement.setAttribute('id', monthNumber)
                    monthElement.classList = 'col-3 p-5 border border-primary month-report-print';
                    monthElement.href = '#';
                    monthElement.textContent = months[monthNumber];

                    item.appendChild(monthElement);
                }
                 */
                $('.cashregister-name').text(cashRegisterName);

                $("#monthly-rep-register").modal('hide')
                $('#monthly-rep-register-months').modal('hide');

                $('#confirm-reprint-z-report').modal('show');
            }
        });

        // Temporarily fixing broken backdrop
        $(document).on('click', '.tab-content', () => {
            $('#monthly-rep-register-months').modal('hide');
            $("#monthly-rep-register").modal('hide')
            $("#confirm-reprint-z-report").modal('hide');
        })

        $(document).on('click', '.tab-content .card', (event) => {
            event.stopPropagation();
        });


        window.printer_testing = false;

        $(document).on('click', '.print-current-month', function(e) {
            e.preventDefault();

            const month = $('#months-cash').val();
            const registerId = monthlyReportCashRegister;

            $.ajax({
                method: 'GET',
                async: false,
                url: `/zReport/month?month=${month}&cash_register_id=${registerId}`,
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
                            confirmButtonText: `{{ __("Ok, got it!") }}`,
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                        return;
                    }

                    if (resp.status == 0) {
                        if (window.printer_testing || window.connected_printer) {
                            Swal.fire({
                                text: `{{ __("Printing...") }}`,
                                icon: "info",
                                buttonsStyling: false,
                                confirmButtonText: `{{ __("Ok, got it!") }}`,
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });


                            window.invoicePrinting(resp.print_order);

                        } else {
                            Swal.fire({
                                text: `{{ __("No printer was configured") }}`,
                                icon: "info",
                                buttonsStyling: false,
                                confirmButtonText: `{{ __("Ok, got it!") }}`,
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        }
                    }

                    if ('message' in resp && resp.message != '') {
                        Swal.fire({
                            text: resp.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: `{{ __("Ok, got it!") }}`,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        })
                    }
                },
                error: function(err) {
                    console.log(err);
                    Swal.fire({
                        text: `{{ __('An unexpected error occured') }}`,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: `{{ __('Ok, got it!') }}`,
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
            });
        });

        $('.pin-submit').submit(event => {
            event.preventDefault();

            const pin = $('#pin-z-rep').val();

            /*
            $("#monthly-rep-register").modal('hide')
            $("#confirm-reprint-z-report").modal('hide');
            $('#monthly-rep-register-months').modal('show');
             */
            $.ajax({
                method: 'GET',
                async: false,
                /*
                data: {
                    pin,
                    cash_register_id: monthlyReportCashRegister
                },
                 */
                url: `/checkPinToPrintReportsForMonth?pin=${pin}&cash_register_id=${monthlyReportCashRegister}`,
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
                            confirmButtonText: `{{ __("Ok, got it!") }}`,
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                        return;
                    }

                    if (resp.status == 0) {
                        const pin = $('#pin-z-rep').val('');
                        $("#monthly-rep-register").modal('hide')
                        $("#confirm-reprint-z-report").modal('hide');
                        $('#monthly-rep-register-months').modal('show');
                    }

                    if ('message' in resp && resp.message != '') {
                        Swal.fire({
                            text: resp.message,
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: `{{ __("Ok, got it!") }}`,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        })
                    }

                },
                error: err => {
                    console.log(err);
                    Swal.fire({
                        text: `{{ __('An unexpected error occured') }}`,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: `{{ __('Ok, got it!') }}`,
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
            })

            return false;
        });
    </script>
 @endsection
