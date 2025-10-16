"use strict";
$('.submit-appearance').click(function (e) {
    var formData = new FormData($('#kt_ecommerce_appearance')[0])
    e.preventDefault();
    $.ajax({
        url: '/admin/setting/appearance',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            Swal.fire({
                text: window.keys.logoUpdated,
                icon: "success",
                buttonsStyling: false,
                confirmButtonText: window.keys.confirmButtonOk,
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            }).then(function (){
                location.href = '/admin/setting';
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
                })
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
});
