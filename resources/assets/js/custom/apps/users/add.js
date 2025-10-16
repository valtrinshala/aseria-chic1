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
                                message: window.keys.userRequired
                            }
                        }
                    },
                    'email': {
                        validators: {
                            notEmpty: {
                                message: window.keys.userEmail
                            },
                            emailAddress: {
                                message: window.keys.valueEmail
                            }
                        }
                    },
                    'role_id': {
                        validators: {
                            notEmpty: {
                                message: window.keys.userRoleIsRequired
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

        $('#roles').change(function () {
            let locationInput = $('#location_id');
            if ($(this).find('option:selected').val() === 'eb325fb3-e752-447e-9d17-1e9aa6687d00'){
                locationInput.prop('disabled', true);
            }else {
                locationInput.prop('disabled', false);
            }
        });

        $('#submitButton').on('click', function () {
            $('#kt_ecommerce_add_user_form').submit();
        });
        $('#kt_ecommerce_add_user_form').submit(function (e) {
            var formData = new FormData($('#kt_ecommerce_add_user_form')[0]);
            if ($('#method').val() === 'create'){
                var url =  '/admin/user';
            }else if ($('#method').val() === 'update' ){
                url =  '/admin/user/'+$('#page-id').val();
                formData.append('_method', 'PUT');
            }else {
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
                                    text: window.keys.userSuccess,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: window.keys.confirmButtonOk,
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function (){
                                    if ($('#method').val() === 'create'){
                                        $('#kt_ecommerce_add_user_form')[0].reset();
                                        $('#roles').val(null).trigger('change');
                                    }else {
                                        location.href = '/admin/user';
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
            form = document.querySelector('#kt_ecommerce_add_user_form');

            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTModalCustomersAdd.init();
});
