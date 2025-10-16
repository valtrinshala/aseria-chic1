"use strict";

document.addEventListener('DOMContentLoaded', function () {
    const deleteBackups = document.querySelectorAll('.delete-backup-form');
    const restoreBackups = document.querySelectorAll('.restore-backup-form');

    deleteBackups.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            let filename = formData.get('name');
            Swal.fire({
                text: window.keys.deleteText + " '" + filename + "' ?",
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
                        url: '/admin/deleteBackup/' + filename,
                        type: 'POST',
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            $('.' + filename.replace(/\.zip$/, "")).remove();
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        },
                    });
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: filename + ' ' + window.keys.notDeleted,
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
    });

    restoreBackups.forEach(form1 => {
        form1.addEventListener('submit', function (e) {
            e.preventDefault();
            var formData1 = new FormData(this);
            let filename1 = formData1.get('name');
            Swal.fire({
                text: window.keys.restoreText + " '" + filename1 + "' ?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: window.keys.confirmRestoreButtonText,
                cancelButtonText: window.keys.cancelButtonText,
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: '/admin/restoreBackup/' + filename1,
                        type: 'POST',
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            Swal.fire({
                                text: filename1 + ' ' + window.keys.backupHasBeenRestored,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: window.keys.confirmButtonOk,
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        },
                    });
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: filename1 + ' ' + window.keys.notRestored,
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
    });

});
