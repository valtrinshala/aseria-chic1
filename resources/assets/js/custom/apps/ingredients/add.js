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
                                message: window.keys.ingredientNameRequired
                            }
                        }
                    },
                    'cost': {
                        validators: {
                            notEmpty: {
                                message: window.keys.ingredientCostRequired
                            },
                            regexp: {
                                regexp: /^[0-9]+(\.[0-9]{1,7})?$/,
                                message: window.keys.regexRequired
                            }
                        }
                    },
                    'price': {
                        validators: {
                            notEmpty: {
                                message: window.keys.ingredientPriceRequired
                            },
                            regexp: {
                                regexp: /^[0-9]+(\.[0-9]{1,7})?$/,
                                message: window.keys.regexRequired
                            }
                        }
                    },
                    'unit': {
                        validators: {
                            notEmpty: {
                                message: window.keys.ingredientUnitRequired
                            }
                        }
                    },
                    'quantity': {
                        validators: {
                            notEmpty: {
                                message: window.keys.ingredientQuantityRequired
                            },
                            regexp: {
                                regexp: /^[0-9]+(\.[0-9]{1,7})?$/,
                                message: window.keys.regexRequired
                            }
                        }
                    },
                    'alert_quantity': {
                        validators: {
                            notEmpty: {
                                message: window.keys.ingredientAlertRequired
                            },
                            regexp: {
                                regexp: /^[0-9]+(\.[0-9]{1,7})?$/,
                                message: window.keys.regexRequired
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

        $('#submitButton').on('click', function () {
            $('#kt_ecommerce_add_ingredient_form').submit();
        });
        $('#kt_ecommerce_add_ingredient_form').submit(function (e) {
            var formData = new FormData($('#kt_ecommerce_add_ingredient_form')[0]);
            if ($('#method').val() === 'create') {
                var url = '/admin/ingredient';
            } else if ($('#method').val() === 'update') {
                url = '/admin/ingredient/' + $('#page-id').val();
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
                                    text: window.keys.ingredientSuccessfullyAdded,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: window.keys.confirmButtonOk,
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function () {
                                    if ($('#method').val() === 'create') {
                                        $('#kt_ecommerce_add_ingredient_form')[0].reset();
                                        $('#kt_ecommerce_add_product_status_select').val('').trigger('change');
                                        // location.reload();
                                    }else {
                                        location.href = '/admin/ingredient'
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
            form = document.querySelector('#kt_ecommerce_add_ingredient_form');

            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTModalCustomersAdd.init();
});
