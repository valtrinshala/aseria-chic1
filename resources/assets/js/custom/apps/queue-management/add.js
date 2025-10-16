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
                                message: window.keys.queueName
                            }
                        }
                    },
                    'queue_id': {
                        validators: {
                            notEmpty: {
                                message: window.keys.queueIdRequired
                            }
                        }
                    },
                    'url': {
                        validators: {
                            notEmpty: {
                                message: window.keys.urlRequired
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

        $('#name').on('input', function() {
            var nameValue = $(this).val();
            var keyValue = nameValue.replace(/\W+/g, '_').toLowerCase();
            $('#key').val(keyValue);
            $('#hidden_key').val(keyValue);
            var staticUrl = $('.create-url').data('url-static');
            var staticInputUrl = $('#url').data('url');
            updateURL(staticUrl, keyValue, staticInputUrl);
        });

        function updateURL(staticUrl, keyValue, staticInputUrl) {
            var url = staticUrl + keyValue;
            let inputUrl = staticInputUrl + keyValue
            $('.create-url').text(url);
            $('#url').val(inputUrl);
        }

        $('#submitButton').on('click', function () {
            $('#kt_ecommerce_add_queueManagement_form').submit();
        });
        $('#kt_ecommerce_add_queueManagement_form').submit(function (e) {
            var formData = new FormData($('#kt_ecommerce_add_queueManagement_form')[0]);
            if ($('#method').val() === 'create'){
                var url =  '/admin/queueManagement';
            }else if ($('#method').val() === 'update' ){
                url =  '/admin/queueManagement/'+$('#page-id').val();
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
                                    text:  window.keys.queueManagementSuccessfully,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: window.keys.confirmButtonOk,
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function (){
                                    if ($('#method').val() === 'create'){
                                        $('#kt_ecommerce_add_queueManagement_form')[0].reset();
                                    }else {
                                        location.href = '/admin/queueManagement'
                                    }
                                });
                            },
                            statusCode: {
                                422: function (response) {
                                    let errorMessage = window.keys.ekioskErrorMessage + ' ' + "\n\n";
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
            form = document.querySelector('#kt_ecommerce_add_queueManagement_form');

            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTModalCustomersAdd.init();
});
