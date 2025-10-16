"use strict";

// Class definition
var KTModalCustomersAdd = function () {
    var validator;
    var form;
    var handleForm = function () {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'device_name': {
                        validators: {
                            notEmpty: {
                                message: window.keys.printerSettingsDeviceNameRequired
                            }
                        }
                    },
                    'device_ip': {
                        validators: {
                            notEmpty: {
                                message: window.keys.printerSettingsDeviceIpRequired
                            }
                        }
                    },
                    'device_port': {
                        validators: {
                            notEmpty: {
                                message: window.keys.printerSettingsDevicePortRequired
                            }
                        }
                    },
                    'device_type': {
                        validators: {
                            notEmpty: {
                                message: window.keys.printerSettingsDeviceTypeRequired
                            }
                        }
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
                }
            }
        );

        $('#clear-device-status').on('click', function () {
            const id = $(this).data('id'), name = $(this).data('name');
            Swal.fire({
                text: window.keys.askReleaseDevice,
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: window.keys.yesClear,
                cancelButtonText: window.keys.cancelButtonText,
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: "/admin/setting/clearDeviceFromCashRegisterOrEKiosk/"+id,
                        method: "POST",
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(result){
                            if (result.status == 1) {
                                // These will change with the library in the next update
                                Swal.fire({
                                    text: result.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: window.keys.confirmButtonOk,
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                });
                                return;
                            }

                            if (result.status == 0) {
                                Swal.fire({
                                    text: result.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: window.keys.confirmButtonOk,
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                });
                                return;
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                text: error,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: window.keys.confirmButtonOk,
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                            return;
                        }
                    });
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: name + ' ' + window.keys.notCleaned,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: window.keys.confirmButtonOk,
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
            });
        });

        $('#submitButton').on('click', function () {
            $('#kt_ecommerce_add_printer_settings_form').submit();
        });
        $('#kt_ecommerce_add_printer_settings_form').submit(function (e) {
            var formData = new FormData($('#kt_ecommerce_add_printer_settings_form')[0]);
            if ($('#method').val() === 'create') {
                var url = '/admin/setting/printerSettings';
            } else if ($('#method').val() === 'update') {
                url = '/admin/setting/printerSettings/' + $('#page-id').val();
                formData.append('_method', 'PUT');
            } else {
                return;
            }
            e.preventDefault();
            if (validator) {
                validator.validate().then(function (status) {
                    if (status == 'Valid') {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                Swal.fire({
                                    text: window.keys.printerSettingsSuccessfullyAdded,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: window.keys.confirmButtonOk,
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function () {
                                    if ($('#method').val() === 'create') {
                                        $('#kt_ecommerce_add_printer_settings_form')[0].reset();
                                        location.reload();
                                    }else {
                                        location.href = '/admin/setting/printerSettings'
                                    }
                                });
                            },
                            statusCode: {
                                422: function (response) {
                                    let errorMessage = window.keys.ekioskErrorMessage + "\n\n";
                                    for (let field in response.responseJSON.errors) {
                                        errorMessage += `${response.responseJSON.errors[field].join(", ")}\n`;
                                    }
                                    Swal.fire({
                                        text: errorMessage,
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: window.keys.confirmButtonOk,
                                        customClass: {
                                            confirmButton: "btn btn-primary"
                                        }
                                    });
                                }
                            },
                            error: function (error) {
                                Swal.fire({
                                    text: window.keys.ekioskServerErrors,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: window.keys.confirmButtonOk,
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            text: window.keys.ekioskErrorsDetected,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: window.keys.confirmButtonOk,
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }

                })
            }
        });

    }

    return {
        init: function () {
            form = document.querySelector('#kt_ecommerce_add_printer_settings_form');

            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTModalCustomersAdd.init();
});
