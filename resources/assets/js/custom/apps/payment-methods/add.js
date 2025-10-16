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
                    'name': {
                        validators: {
                            notEmpty: {
                                message: window.keys.paymentRequired
                            }
                        }
                    }
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
        let colorDiv = $('#colorDiv');
        let infoSpanColor = $('#infoSpanColor')
        let iconDiv = $('#iconDiv');
        let infoSpanDiv = $('#infoSpanDiv');

        colorDiv.show();
        iconDiv.hide();

        $('#kt_security_summary_tabs a[data-bs-toggle="tab"]').click(function () {
            let targetTabId = $(this).attr('href');

            switch (targetTabId) {
                case "#kt_security_summary_tab_pane_hours":
                    colorDiv.show();
                    infoSpanColor.show();
                    iconDiv.hide();
                    infoSpanDiv.hide();
                    break;
                case "#kt_security_summary_tab_pane_day":
                    colorDiv.hide();
                    infoSpanColor.hide();
                    iconDiv.show();
                    infoSpanDiv.show();
                    break;
                default:
                    colorDiv.show();
                    infoSpanColor.show();
                    iconDiv.hide();
                    infoSpanDiv.hide();
            }
        });
        $('.paymentCategories').children().click(function () {
            $('.input-color').val($(this).data('color'))
            $('.paymentMethod-color').css('background-color', $(this).data('color'));
        });
        $('#submitButton').on('click', function () {
            $('#kt_ecommerce_add_paymentMethod_form').submit();
        });
        $('.delete-button').click(function (event) {
            event.preventDefault();
            const recordId = $(this).data('id');
            const recordName = $(this).data('name');
            Swal.fire({
                text: window.keys.deleteText + ' ' + recordName + "?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: window.keys.createButton,
                cancelButtonText: window.keys.cancelButtonText,
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: "/admin/paymentMethod/" + recordId,
                        method: "DELETE",
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (result) {
                            location.href = '/admin/paymentMethod'
                        },
                        statusCode: {
                            422: function (response) {
                                Swal.fire({
                                    text: response.responseJSON.error,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                })
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: recordName + " " + window.keys.notDeleted,
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
        $('#kt_ecommerce_add_paymentMethod_form').submit(function (e) {
            var formData = new FormData($('#kt_ecommerce_add_paymentMethod_form')[0]);
            if ($('#method').val() === 'create') {
                var url = '/admin/paymentMethod';
            } else if ($('#method').val() === 'update') {
                url = '/admin/paymentMethod/' + $('#page-id').val();
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
                                    text: "The payment method has been successfully added!",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function () {
                                    if ($('#method').val() === 'create') {
                                        // $('#kt_ecommerce_add_paymentMethod_form')[0].reset();
                                        location.reload();
                                    } else {
                                        location.href = '/admin/paymentMethod';
                                    }
                                });
                            },
                            statusCode: {
                                422: function (response) {
                                    let errorMessage = "Sorry, looks like there are some errors detected:\n\n";
                                    for (let field in response.responseJSON.errors) {
                                        errorMessage += `${response.responseJSON.errors[field].join(", ")}\n`;
                                    }
                                    Swal.fire({
                                        text: errorMessage,
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, got it!",
                                        customClass: {
                                            confirmButton: "btn btn-primary"
                                        }
                                    });
                                }
                            },
                            error: function (error) {
                                Swal.fire({
                                    text: "Sorry, looks like there are some errors in server, please try again.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
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
            form = document.querySelector('#kt_ecommerce_add_paymentMethod_form');

            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTModalCustomersAdd.init();
});
