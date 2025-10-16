"use strict";

// Class definition
var KTCustomersList = function () {
    // Define shared variables
    var datatable;
    var table

    // Private functions
    var initCustomerList = function () {
        // Set date data order
        const tableRows = table.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            const dateRow = row.querySelectorAll('td');
            const realDate = moment(dateRow[8].innerHTML, "DD MMM YYYY, LT").format(); // select date from 9th column in table
            dateRow[8].setAttribute('data-order', realDate);
        });

        // Init datatable --- more info on datatables: https://datatables.net/manual/
        datatable = $(table).DataTable({
            "info": false,
            'order': [],
            'columnDefs': [
                { orderable: false, targets: 0 }, // Disable ordering on column 0 (checkbox)
                { orderable: false, targets: 8 }, // Disable ordering on column 9 (actions)
            ]
        });

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        datatable.on('draw', function () {
            initToggleToolbar();
            handleDeleteRows();
            toggleToolbars();
            KTMenu.init(); // reinit KTMenu instances
        });
    }
    const clearButton = document.querySelector('#kt_ecommerce_sales_flatpickr_clear');
    clearButton.addEventListener('click', e => {
        flatpickr.clear();
    });

    const element = document.querySelector('#kt_ecommerce_sales_flatpickr');
    flatpickr = $(element).flatpickr({
        altInput: true,
        altFormat: "d/m/Y",
        dateFormat: "Y-m-d",
        mode: "range",
        onChange: function (selectedDates, dateStr, instance) {
            if(selectedDates.length === 2 || selectedDates.length === 0){
                const tables = $('.z-report-filter-table');
                if (tables.length == 0) {
                    console.error('No tables found.');
                    return;
                }
                const mainTable = tables[0];
                const table = $(mainTable).DataTable();
                table.clear().draw();
                $.ajax({
                    url: '/admin/zReport/all/zReportByDate',
                    type: 'GET',
                    data: {
                        date: dateStr,
                    },
                    success: function(response) {
                        const symbolPlacement = mainTable.getAttribute('csl');
                        const symbol = mainTable.getAttribute('sym');
                        if (!response || !response.data || !Array.isArray(response.data)) {
                            console.error('Invalid response format or missing data.');
                            return;
                        }
                        for (let zReport of response.data) {
                            let total = symbolPlacement == 0 ? parseFloat(zReport.total_sales).toFixed(2) + ' ' + symbol : symbol + ' ' + parseFloat(zReport.total_sales).toFixed(2);
                            const newRow = $('<tr>');
                            newRow.append('<td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">'+ parseFloat(zReport.report_number) +'</td>');
                            newRow.append('<td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">' + zReport.cash_register?.name + '</td>');
                            newRow.append('<td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">' + zReport.cash_register?.key + '</td>');
                            newRow.append('<td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">' + zReport.location?.name + '</td>');
                            newRow.append('<td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">' + zReport.start_z_report + '</td>');
                            newRow.append('<td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">' + (zReport.end_z_report ?? ' ') + '</td>');
                            newRow.append('<td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">' + parseInt(zReport.total_sales) + '</td>');
                            newRow.append('<td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">' + getStatusBadge(zReport.end_z_report) + '</td>');
                            newRow.append('<td class="text-end"><a href="/pdf/zReport/'+zReport.id+'" class="btn btn-sm btn-light btn-flex'+ (!zReport.end_z_report ? ' disabled ' : '') +'btn-center btn-active-light-primary border-0 bg-light-primary">'+ 'Download' +'<i class="ki-duotone ki-down fs-5 ms-1"></i></td>');

                            table.row.add(newRow);
                        }

                        table.draw();
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
            }
        },
    });
    function formatCurrency(amount) {
        var currencyOnLeft = window.settings.currency_on_left ? window.settings.currency_on_left : '';
        var formattedAmount = parseFloat(amount).toFixed(2);
        if (currencyOnLeft === '1') {
            return window.settings.currency_symbol + ' ' + formattedAmount;
        } else {
            return formattedAmount + ' ' + window.settings.currency_symbol;
        }
    }
    function formatDate(dateString) {
        var date = new Date(dateString);
        var year = date.getFullYear();
        var month = ('0' + (date.getMonth() + 1)).slice(-2);
        var day = ('0' + date.getDate()).slice(-2);
        let property = `${day}.${month}.${year}`;

        return property;
    }

    function getStatusBadge(endZReport) {
        if (endZReport) {
            return '<div class="badge badge-light-danger">Closed</div>';
        } else {
            return '<div class="badge badge-light-success">Opened</div>';
        }
    }

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-customer-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }
    // Delete customer
    var handleDeleteRows = () => {
        // Select all delete buttons
        const deleteButtons = table.querySelectorAll('[data-kt-customer-table-filter="delete_row"]');

        deleteButtons.forEach(d => {
            d.addEventListener('click', function (e) {
                e.preventDefault();
                const modifierId = e.target.getAttribute('data-modifier-id');
                const parent = e.target.closest('tr');

                const modifierName = parent.querySelectorAll('td')[1].innerText;

                Swal.fire({
                    text: window.keys.deleteText + ' ' + modifierName + "?",
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
                            url: "/admin/modifier/"+modifierId,
                            method: "DELETE",
                            contentType: false,
                            processData: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(result){
                                datatable.row($(parent)).remove().draw();
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                            }
                        });
                    } else if (result.dismiss === 'cancel') {
                        Swal.fire({
                            text: modifierName + ' ' + window.keys.notDeleted,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: window.keys.confirmButtonOk,
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                });
            })
        });
    }
    // Init toggle toolbar
    var initToggleToolbar = () => {
        const checkboxes = table.querySelectorAll('[type="checkbox"]');

        const deleteSelected = document.querySelector('[data-kt-customer-table-select="delete_selected"]');

        checkboxes.forEach(c => {
            // Checkbox on click event
            c.addEventListener('click', function () {
                setTimeout(function () {
                    toggleToolbars();
                }, 50);
            });
        });

        // Deleted selected rows
        deleteSelected.addEventListener('click', function () {
            // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
            Swal.fire({
                text: window.keys.deleteReports,
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
                    let ids = [];
                    checkboxes.forEach(c => {
                        if (c.checked) {
                            ids.push(c.value);
                            datatable.row($(c.closest('tbody tr'))).remove().draw();
                        }
                    });
                    $.ajax({
                        url: "/admin/modifier/delete/selected",
                        method: "POST",
                        data: { ids: ids },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });

                    // Remove header checked box
                    const headerCheckbox = table.querySelectorAll('[type="checkbox"]')[0];
                    headerCheckbox.checked = false;
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: window.keys.selectedReports,
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
    }

    // Toggle toolbars
    const toggleToolbars = () => {
        // Define variables
        const toolbarBase = document.querySelector('[data-kt-customer-table-toolbar="base"]');
        const toolbarSelected = document.querySelector('[data-kt-customer-table-toolbar="selected"]');
        const selectedCount = document.querySelector('[data-kt-customer-table-select="selected_count"]');

        // Select refreshed checkbox DOM elements
        const allCheckboxes = table.querySelectorAll('tbody [type="checkbox"]');

        // Detect checkboxes state & count
        let checkedState = false;
        let count = 0;

        // Count checked boxes
        allCheckboxes.forEach(c => {
            if (c.checked) {
                checkedState = true;
                count++;
            }
        });

        // Toggle toolbars
        if (checkedState) {
            selectedCount.innerHTML = count;
            toolbarBase.classList.add('d-none');
            toolbarSelected.classList.remove('d-none');
        } else {
            toolbarBase.classList.remove('d-none');
            toolbarSelected.classList.add('d-none');
        }
    }

    // Public methods
    return {
        init: function () {
            table = document.querySelector('#kt_customers_table');

            if (!table) {
                return;
            }

            initCustomerList();
            initToggleToolbar();
            handleSearchDatatable();
            handleDeleteRows();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTCustomersList.init();
});
