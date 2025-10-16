<style>
    .tran-danger-bg {
        background: #FEEAEA;
    }

    .max-h-50 {
        max-height: 50vh;
    }

    .checkbox-background {
        background-color: #5D4BDF;
        border-radius: 8px;
        border: 2px solid transparent;
    }

    .checkbox-background:has(input:not(:checked)) {
        background-color: transparent;
        border: 2px solid #777;
    }

    .checkbox-background input {
        border: none;
        width: 1em;
        height: 1em;
    }

    .payment-loader {
        position: absolute;
        inset: 0;
        background: #0003;
        z-index: 9999999
    }

    .payment-loader.hidden {
        display: none !important;
    }

    .payment-loader::before {
        position: relative;
        content: '';
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid var(--prim-color);
        width: 80px;
        height: 80px;
        top: 50%;
        left: 50%;
        display: block;

        -webkit-animation: spin 2s linear infinite; /* Safari */
        animation: spin 2s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
        0% { -webkit-transform: translate(-50%, -50%) rotate(0deg); }
        100% { -webkit-transform: translate(-50%, -50%) rotate(360deg); }
    }

    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }
</style>
<div id="cancel-confirmation" order="none" class="modal fade" data-backdrop="true">
    <div class="payment-loader hidden"></div>
    <div class="modal-dialog mh-100 mh-padd-3 my-0">
        <div class="modal-content p-10">
            <div>
                <h2 class="text-center">{{ __('Cancel order') }}</h2>
            </div>
            <div>
                <p class="text-center">{{ __("When cancelled, the order'll not be able to undone, What is the reason behind cancelling order?") }}</p>
            </div>
            <div class="input-group rounded  justify-content-center w-100">
                <textarea name="description" class="form-control max-h-50" rows="4" placeholder="{{ __('Type a message') }}"></textarea>
            </div>

            <label for="agreement" class="form-check-sm form-check-custom form-check-solid my-3">
                <div class="checkbox-background d-flex p-2 me-2">
                    <input id="agreement" name="agreement" class="form-check-input p-0" type="checkbox" value="true"/>
                </div>
                <span>{{ __('Agreed') }}</span>
            </label>
            <div class="d-flex justify-content-between buttons-discount">
                <div class="">
                    <a href="#" class="btn font-span confirm-cancel-order fw-bold py-3 tran-danger-bg border-danger text-danger">{{ __('Confirm cancel') }}</a>
                </div>
                <div class="">
                    <a href="#" class="btn font-span discard-cancel-order nfw-bold add-disc btn-violet py-3 gray-border text-white">{{ __('Discard this action') }}</a>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
    const paymentLoader = {
        load: () => {
            const loaders = document.getElementsByClassName('payment-loader');
            for (let loader of loaders) {
                loader.classList.remove('hidden');
            }
        },
        close: () => {
            const loaders = document.getElementsByClassName('payment-loader');
            for (let loader of loaders) {
                loader.classList.add('hidden');
            }
        }
    }

    function send_terminal_logs(message) {
        $.ajax({
            method: 'POST',
            url: '/logs',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            data: {
                message: message
            },
            success: resp => {
                if ('message' in resp) {
                    if (window.log_alert) {
                        Swal.fire({
                            text: 'Log was saved',
                            icon: 'success',
                            confirmButtonText: `{{ __("Ok, got it!") }}`,
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        })
                    }
                } else {
                    if (window.log_alert) {
                        Swal.fire({
                            text: "Log couldn't save",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: `{{ __("Ok, got it!") }}`,
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                }
            },
            error: err => {
                if (window.log_alert) {
                    Swal.fire({
                        text: "Log couldn't save",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: `{{ __("Ok, got it!") }}`,
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
            }
        })
    }

    function payment_complete(data) {
        const requiredKeys = [
            "title", "type", "card_type",
            "card_number", "date", "trm_id", "aid",
            "seq_cnt", "acq_id", "total",
            "ref_no", "auth_code"
        ];

        let backData = {};
        try {
            backData = JSON.parse(data);
            for (let key of requiredKeys) {
                if (!(key in backData)) {
                    backData[key] = null;
                }
            }

        } catch(err) {
            backData = {
                raw_full_data: data,
            }

            for (let key of requiredKeys) {
                backData[key] = null;
            }
        }


        paymentLoader.close();
        // console.log(data);
        let note = null;
        if ('note' in window.terminal_data && window.terminal_data.note != null) {
            note = window.terminal_data.note;
        }

        confirmed_cancelation(window.terminal_data.orderId, window.terminal_data.message, backData, note);
    }

    function failed_terminal(data) {
        paymentLoader.close();
        Swal.fire({
            text: data,
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: `{{ __("Ok, got it!") }}`,
            customClass: {
                confirmButton: "btn fw-bold btn-primary",
            }
        });
    }

    function confirmed_cancelation(orderId, message, payment_cancel_data = '', note = null) {
        const data = {
            order_id: orderId,
            message: message.trim(),
            agreement: 'on',
        }

        if (note != null)
            data.url_from = note;

        if (payment_cancel_data != '')
            data.payment_data_canceled = payment_cancel_data;

        $.ajax({
            url: "/admin/pos/cancelOrder",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            data: data,
            success: function(resp) {
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
                    if ('print_order' in resp && resp.print_order != '')
                        window.invoicePrinting(resp.print_order);

                    const orderCancelConfirmModal = $('#cancel-confirmation');
                    orderCancelConfirmModal.each((index, mod) => {
                        orderCancelConfirmModal.modal('hide');
                        if ('eventAttached' in mod && typeof(mod.eventAttached) == 'function') {
                            mod.eventAttached(orderId);
                        }
                    });
                }

                if (resp.message != '') {
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
            error: function(error) {
                Swal.fire({
                    text: "{{ __('There was an internal issue, please refresh and try again, if this does not work please contact administration') }}",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
            }
        });
    }

    {
        const orderCancelConfirmModal = $('#cancel-confirmation');
        orderCancelConfirmModal.each((index, mod) => {
            mod.clearInputs = () => {
                $(mod).find('textarea').val('');
                $(mod).find('input[type="checkbox"]').prop('checked', false);
            }
        })

        window.terminal_data = {orderId: '', message: '', cancelAmount: 0};
        $(document).on('click', '.confirm-cancel-order', function() {
            const orderId = orderCancelConfirmModal.attr('order');
            const cancelAmount = orderCancelConfirmModal.attr('cancel_amount');
            const cancelNote = orderCancelConfirmModal.attr('note');

            if (orderId == 'none') {
                Swal.fire({
                    text: `{{ __('There was an issue with getting the order clicked, please try again.') }}`,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
                return;
            }

            const agree = orderCancelConfirmModal.find('[name="agreement"]').is(':checked');
            if (!agree) {
                Swal.fire({
                    text: `{{ __('You need to agree with the cancelation to continue') }}`,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
                return;
            }

            const message = orderCancelConfirmModal.find('[name="description"]').val();
            if (message.trim() == '') {
                Swal.fire({
                    text: `{{ __('Reason message must be filled to cancel an order') }}`,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: `{{ __("Ok, got it!") }}`,
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
                return;
            }

            if (!cancelAmount || parseFloat(cancelAmount) == 0) {
                let note = null;
                if (cancelNote && cancelNote != '') note = cancelNote;

                confirmed_cancelation(orderId, message, '', note);
            } else {
                if (!window.terminal_confirmation) {
                    Swal.fire({
                        text: `{{ __('This order is paid by card (orders that are paid by card cannot be canceled without refund), the terminal is disabled or not configured, please configure the terminal and refresh the app from POS') }}`,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: `{{ __("Ok, got it!") }}`,
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });

                    return;
                }


                let note = null;
                if (cancelNote && cancelNote != '') note = cancelNote;

                window.terminal_data = {
                    orderId: orderId,
                    message: message,
                    cancelAmount: cancelAmount
                }

                if (note != null) {
                    window.terminal_data.note = note;
                }

                if ('Mine' in window) {
                    paymentLoader.load();
                    setTimeout(() => {
                        paymentLoader.close();
                    }, 30000);
                    window.Mine.postMessage('ria:' + cancelAmount + ':' + getTerminalDetails());
                } else console.log('ria:' + cancelAmount + ':' + getTerminalDetails());
            }
        })


        $(document).on('click', '.discard-cancel-order', function() {
            orderCancelConfirmModal.attr('order', 'none');
            orderCancelConfirmModal.modal('hide');
        })
    }
    window.terminal_ip = '';
    window.terminal_confirmation = false;

    function getTerminalDetails() {
        const config = window.terminal_coniguration;

        for (key in config) {
            // Ensure splits are not broken, it's better not to work then crash
            config[key] = config[key].replaceAll('--', '__');
        }

        return config.ip + "--" + config.terminal_type + "--" + config.socket_mode + "--" + config.port + "--" + config.compatibility_port
    }

    let printerSettings = localStorage.getItem('printer_settings');
    if (printerSettings != null) {
        try {
            printerSettings = JSON.parse(printerSettings);

            if ('terminal' in printerSettings && printerSettings.terminal && printerSettings.terminal.status == 1) {
                window.terminal_ip = printerSettings.terminal.terminal.ip;
                window.terminal_coniguration = printerSettings.terminal.terminal;
                window.terminal_confirmation = true;
            } else {
                // No terminal or the terminal status is 0
            }
        } catch(err) {
            // No printer connected
        }
    } else {
        // No printer selected (no messages here until instructed otherwise)
    }
</script>
