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
                                message: window.keys.assetValidationName
                            }
                        }
                    },
                    'position_id': {
                        validators: {
                            notEmpty: {
                                message: window.keys.assetValidationPosition
                            }
                        }
                    },
                    'e_kiosk_id': {
                        validators: {
                            notEmpty: {
                                message: window.keys.assetValidationEkiosk
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

        $('#positions').change(function () {
            let selectedOption = $(this).find('option:selected');
            let description = selectedOption.data('description');
            $('.extend-description').text(description);
        });

        $('#submitButton').on('click', function () {
            $('#kt_ecommerce_add_eKioskAsset_form').submit();
        });
        $('#kt_ecommerce_add_eKioskAsset_form').submit(function (e) {
            var formData = new FormData($('#kt_ecommerce_add_eKioskAsset_form')[0]);
            if ($('#method').val() === 'create'){
                var url =  '/admin/eKioskAsset';
            }else if ($('#method').val() === 'update' ){
                url =  '/admin/eKioskAsset/'+$('#page-id').val();
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
                                    text: window.keys.ekioskSuccesfullyAdded,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: window.keys.confirmButtonOk,
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function (){
                                    if ($('#method').val() === 'create'){
                                        // $('#kt_ecommerce_add_eKioskAsset_form')[0].reset();
                                        location.reload();
                                    }else {
                                        location.href = '/admin/eKioskAsset'
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
            form = document.querySelector('#kt_ecommerce_add_eKioskAsset_form');

            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTModalCustomersAdd.init();
});
