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
                                message: window.keys.locationName
                            }
                        }
                    },
                    'location': {
                        validators: {
                            notEmpty: {
                                message: window.keys.locationAddress
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

        $('.check-taxes').on('click', function() {
            var checkbox = $(this);
            if (checkbox.prop('checked')) {
                $.ajax({
                    url: '/admin/setting/ourLocation/check/taxes',
                    type: 'GET',
                    data: {
                        type: checkbox.attr('name'),
                    },
                    success: function (response) {
                        if (response.status === 1){
                            checkbox.prop('checked', false);
                            Swal.fire({
                                text: response.message,
                                icon: "error",
                                buttonsStyling: false,
                                showCancelButton: true,
                                confirmButtonText: window.keys.createButton,
                                cancelButtonText: window.keys.cancelButtonText,
                                customClass: {
                                    confirmButton: "btn btn-primary",
                                    cancelButton: "btn fw-bold btn-active-light-primary"
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.open(response.uri+"?type="+checkbox.attr('name'), '_blank');
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
            }
        });

        $('#submitButton').on('click', function () {
            $('#kt_ecommerce_add_ourLocation_form').submit();
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
                confirmButtonText: window.keys.confirmButtonText,
                cancelButtonText: window.keys.cancelButtonText,
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: "/admin/setting/ourLocation/" + recordId,
                        method: "DELETE",
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (result) {
                            location.href = '/admin/setting/ourLocation/'
                        },
                        statusCode: {
                            422: function (response) {
                                Swal.fire({
                                    text: response.responseJSON.error,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: window.keys.confirmButtonOk,
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
                        text: foodItemName + ' ' + window.keys.notDeleted,
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

        $('#kt_ecommerce_add_ourLocation_form').submit(function (e) {
            var formData = new FormData($('#kt_ecommerce_add_ourLocation_form')[0]);
            if ($('#method').val() === 'create'){
                var url =  '/admin/setting/ourLocation';
            }else if ($('#method').val() === 'update' ){
                url =  '/admin/setting/ourLocation/'+$('#page-id').val();
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
                                    text: window.keys.locationAdded,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: window.keys.confirmButtonOk,
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function (){
                                    if ($('#method').val() === 'create'){
                                        $('#kt_ecommerce_add_ourLocation_form')[0].reset();
                                    }else {
                                        location.href = '/admin/setting/ourLocation'
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
                                    text: error.responseJSON.errors ?? window.keys.ekioskServerErrors,
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
            form = document.querySelector('#kt_ecommerce_add_ourLocation_form');

            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTModalCustomersAdd.init();
});
