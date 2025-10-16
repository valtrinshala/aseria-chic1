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
            const realDate = moment(dateRow[9].innerHTML, "DD MMM YYYY, LT").format(); // select date from 5th column in table
            dateRow[9].setAttribute('data-order', realDate);
        });

        // Init datatable --- more info on datatables: https://datatables.net/manual/
        datatable = $(table).DataTable({
            'order': [],
            'processing': true,
            'serverSide': true,
            'ajax': {
                url: '/admin/order/all/OrderByAllFilters',
                type: 'GET',
                data: function (d) {
                    d.dateFilter = $('#kt_ecommerce_sales_flatpickr').val();

                }
            },
            'columns': [
                {
                    data: 'id',
                    orderable: false,
                    className: 'dt-body-center',
                    render: function (data, type, row) {
                        return `
                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" value="${data}">
                    </div>`;
                    }
                },
                {
                    data: 'order_receipt',
                    render: function (data, type, row) {
                        return `
                    <a href="${row.order_url}" class="text-gray-800 text-hover-primary mb-1">
                        ${data}
                    </a>`;
                    }
                },
                {
                    data: 'order_number',
                    className: 'text-gray-800 fs-10.5 flex-column justify-content-center my-0',
                },
                {
                    data: 'pos_or_kiosk',
                    render: function (data) {
                        return `<div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">${data}</div>`;
                    }
                },
                {
                    data: 'order_type',
                    render: function (data) {
                        return `<div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">${data}</div>`;
                    }
                },
                {
                    data: 'payable_after_all',
                    render: function (data) {
                        return `<div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">${data}</div>`;
                    }
                },
                {
                    data: 'payment_method_type',
                    className: 'text-gray-800 fs-10.5 flex-column justify-content-center my-0',
                },
                {
                    data: 'created_at',
                    render: function (data) {
                        return `<div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">${data}</div>`;
                    }
                },
                {
                    data: 'status',
                    orderable: false,
                    render: function (data, type, row) {
                        return `<div class="badge badge-light-${row.style}">${data}</div>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                    <a href="#" class="btn btn-sm btn-light btn-flex border-0 btn-center btn-active-light-primary"
                       data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">${row.actions}<i class="ki-duotone ki-right fs-5 ms-1"></i>
                    </a>
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600
                                menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                         data-kt-menu="true">
                        <div class="menu-item px-3">
                            <a href="${row.order_url}" class="menu-link px-3">${row.view}</a>
                        </div>
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-3 text-danger"
                               data-order-id="${row.id}"
                               data-kt-customer-table-filter="delete_row">${row.delete}</a>
                        </div>
                    </div>`;
                    }
                }
            ],
            'columnDefs': [
                { orderable: false, targets: 0 }, // Disable ordering on column 0 (checkbox)
                { orderable: false, targets: 9 }, // Disable ordering on column 8 (actions)
            ],
        });
        const clearButton = document.querySelector('#kt_ecommerce_sales_flatpickr_clear');
        if (clearButton) {
            clearButton.addEventListener('click', e => {
                flatpickr.clear();
            });
        }

        const element = document.querySelector('#kt_ecommerce_sales_flatpickr');
        let flatpickr;
        flatpickr = $(element).flatpickr({
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            mode: "range",
            onChange: function (selectedDates, dateStr, instance) {
                if(selectedDates.length === 2 || selectedDates.length === 0){
                    datatable.ajax.reload();
                    //         const tables = $('.order-filter-table');
            //         if (tables.length == 0) {
            //             console.error('No tables found.');
            //             return;
            //         }
            //         const mainTable = tables[0];
            //         const table = $(mainTable).DataTable();
            //         table.clear().draw();
            //         $.ajax({
            //             url: '/admin/order/all/OrderByDate',
            //             type: 'GET',
            //             data: {
            //                 date: dateStr,
            //             },
            //             success: function(response) {
            //                 if (!response || !response.data || !Array.isArray(response.data)) {
            //                     console.error('Invalid response format or missing data.');
            //                     return;
            //                 }
            //                 for (let order of response.data) {
            //                     const newRow = $('<tr>');
            //
            //                     newRow.append('<td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0"><div class="form-check form-check-sm form-check-custom form-check-solid"><input class="form-check-input" type="checkbox" value="' + order.id + '" /></div></td>');
            //                     newRow.append('<td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0"><a href="/admin/order/' + order.id + '" class="text-gray-800 text-hover-primary mb-1">' + (parseInt(order.order_receipt) !== 0 ? parseInt(order.order_receipt) : '') + '</a></td>');
            //                     newRow.append('<td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">' + order.order_number + '</td>');
            //                     newRow.append('<td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">' + order.pos_or_kiosk + '</td>');
            //                     newRow.append('<td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">' + order.order_type + '</td>');
            //                     newRow.append('<td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">' + formatCurrency(order.payable_after_all) + '</td>');
            //                     newRow.append('<td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">' + order.payment_method_type + '</td>');
            //                     newRow.append('<td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">' + formatDate(order.created_at) + '</td>');
            //                     newRow.append('<td class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">' + getStatusBadge(order.is_cancelled, order.completed_at, order.chef_id) + '</td>');
            //                     newRow.append('<td class="text-end"><a href="#" class="btn btn-sm btn-light btn-flex border-0 btn-center btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions<i class="ki-duotone ki-right fs-5 ms-1"></i></a><div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true"><div class="menu-item px-3"><a href="' + order.route + '" class="menu-link px-3">View</a></div><div class="menu-item px-3"><a href="#" class="menu-link px-3 text-danger" data-order-id="' + order.id + '" data-kt-customer-table-filter="delete_row">Delete</a></div></div></td>');
            //
            //                     table.row.add(newRow);
            //                 }
            //
            //                 table.draw();
            //             },
            //
            //             statusCode: {
            //                 422: function (response) {
            //                     let errorMessage = "Sorry, looks like there are some errors detected:\n\n";
            //                     for (let field in response.responseJSON.errors) {
            //                         errorMessage += `${response.responseJSON.errors[field].join(", ")}\n`;
            //                     }
            //                     Swal.fire({
            //                         text: errorMessage,
            //                         icon: "error",
            //                         buttonsStyling: false,
            //                         confirmButtonText: window.keys.confirmButtonOk,
            //                         customClass: {
            //                             confirmButton: "btn btn-primary"
            //                         }
            //                     });
            //                 }
            //             },
            //             error: function (error) {
            //                 Swal.fire({
            //                     text: window.keys.ekioskServerErrors,
            //                     icon: "error",
            //                     buttonsStyling: false,
            //                     confirmButtonText: window.keys.confirmButtonOk,
            //                     customClass: {
            //                         confirmButton: "btn btn-primary"
            //                     }
            //                 });
            //             }
            //         });
                }
            },
        });

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        datatable.on('draw', function () {
            initToggleToolbar();
            handleDeleteRows();
            toggleToolbars();
            KTMenu.init(); // reinit KTMenu instances
        });
    }
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

    // Function to get status badge HTML
    function getStatusBadge(isCancelled, completedAt, chefId) {
        if (isCancelled == true) {
            return '<div class="badge badge-light-danger">'+ window.keys.refunded +'</div>';
        } else if (completedAt != null ) {
            return '<div class="badge badge-light-success">'+ window.keys.completed +'</div>';
        } else if (chefId != null ) {
            return '<div class="badge badge-light-primary">'+ window.keys.inProgress +'</div>';
        } else {
            return '<div class="badge badge-light-warning">'+ window.keys.waiting +'</div>';
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
                const orderId = e.target.getAttribute('data-order-id');
                const parent = e.target.closest('tr');

                const orderName = parent.querySelectorAll('td')[1].innerText;

                Swal.fire({
                    text: "Are you sure you want to delete " + orderName + "?",
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
                            url: "/admin/order/" + orderId,
                            method: "DELETE",
                            contentType: false,
                            processData: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (result) {
                                datatable.row($(parent)).remove().draw();
                            },
                            error: function (xhr, status, error) {
                                console.error(xhr.responseText);
                            }
                        });
                    } else if (result.dismiss === 'cancel') {
                        Swal.fire({
                            text: orderName + ' ' + window.keys.notDeleted,
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

    function setTableOrder(tableData) {
        const tables = $('.order-filter-table');
        if (tables.length == 0) return;
        const mainTable = tables[0];
        const symbolPlacement = mainTable.getAttribute('csl');
        const symbol = mainTable.getAttribute('sym');

        let orders = tableData;

        const start = `<div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">`;
        const end = `</div>`;

        const table = $(mainTable).DataTable();
        table.clear();
        for (let order of orders) {
            const orderRow = [];

            orderRow.push(`
            <div class="form-check form-check-sm form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox" value="30.03.2023" />
            </div>
            `);

            const properties = [
                { type: 'string', value: order.tracking },
                { type: 'string', value: order.order_number },
                { type: 'string', value: order.order_type },
                {
                    type: 'string',
                    value: (order.subtotal).toFixed(2)
                },
                {
                    type: 'cost',
                    value: order.payment_method_type
                },
                {
                    type: 'cost',
                    value: order.created_at
                },
                {
                    type: 'percentage',
                    value: order.completed_at
                },
            ];

            for (let property of properties) {
                if (property.type == 'cost')
                    property.value = symbolPlacement == 0 ? parseFloat(property.value).toFixed(2) + ' ' + symbol : symbol + ' ' + parseFloat(property.value).toFixed(2)

                if (property.type == 'percentage')
                    property.value += ' %';

                if (property.type == 'date') {
                    var date = new Date(property.value);
                    var year = date.getFullYear();
                    var month = ('0' + (date.getMonth() + 1)).slice(-2);
                    var day = ('0' + date.getDate()).slice(-2);
                    var hours = ('0' + date.getHours()).slice(-2);
                    var minutes = ('0' + date.getMinutes()).slice(-2);
                    var seconds = ('0' + date.getSeconds()).slice(-2);
                    property.value = ` ${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                }

                orderRow.push(start + property.value + end);
            }
            table.row.add(orderRow);
        }
        table.draw();
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
                text: window.keys.deleteSelectedOrders,
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
                        url: "/admin/order/delete/selected",
                        method: "POST",
                        data: { ids: ids },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });

                    // Remove header checked box
                    const headerCheckbox = table.querySelectorAll('[type="checkbox"]')[0];
                    headerCheckbox.checked = false;
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: window.keys.selectedOrders,
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
            if (toolbarBase) toolbarBase.classList.add('d-none');
            if (toolbarSelected) toolbarSelected.classList.remove('d-none');
        } else {
            if (toolbarBase) toolbarBase.classList.remove('d-none');
            if (toolbarSelected) toolbarSelected.classList.add('d-none');
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

            const pagHolderElement = document.querySelector('.sale-pargination-holder');
            if (pagHolderElement) {
                const pags = document.getElementById('kt_customers_table_paginate');
                if (pags) pagHolderElement.appendChild(pags);
            }

            const searchHolderElement = document.querySelector('.sale-search-holder');
            if (searchHolderElement) {
                const searchEl = document.getElementById('search-custom-input-all');
                if (searchEl) searchHolderElement.appendChild(searchEl);
            }

            const limitHolderElement = document.querySelector('.sale-limit-holder');
            if (limitHolderElement) {
                const limitEl = document.getElementById('kt_customers_table_length');
                if (limitEl) limitHolderElement.appendChild(limitEl);
            }
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTCustomersList.init();
});
