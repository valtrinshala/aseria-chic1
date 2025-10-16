"use strict";

var KTCustomersList = function () {
    var datatable;
    var table;
    window.queryFilters = {}

    var initCustomerList = function () {
        datatable = $(table).DataTable({
            'order': [],
            'processing': true,
            'serverSide': true,
            'ajax': {
                url: '/admin/filters',
                type: 'GET',
                data: function (d) {
                    d.filters = JSON.stringify(window.queryFilters);
                }
            },
            "drawCallback": function (response) {
                    let totals = response.json.totals;
                    for (let key in totals) {
                        if (key in totals) {
                            $('#' + key).text(totals[key]);
                        }
                    }
            },
            "columns": [
                {
                    "data": "order_receipt",
                    "render": function(data, row) {
                        return `
                            <a class="text-gray-800 fs-10.5 flex-column justify-content-center my-0 text-hover-primary"
                                href="${row.order_url}">${data}</a>
                        `;
                    }
                },
                {
                    "data": "order_number",
                    "render": function(data) {
                        return `
                            <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">${data}</div>
                        `;
                    }
                },
                {
                    "data": "order_type",
                    "render": function(data) {
                        return `
                            <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">${data}</div>
                        `;
                    }
                },
                {
                    "data": "pos_or_kiosk",
                    "render": function(data) {
                        return `
                            <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">${data}</div>
                        `;
                    }
                },
                {
                    "data": "payment_method_type",
                       "render": function(data) {
                        return `
                            <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">${data}</div>
                        `;
                    }
                },
                {
                    "data": "cart_total_cost",
                       "render": function(data) {
                        return `
                            <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">${data}</div>
                        `;
                    }
                },
                {
                    "data": "discount_amount",
                       "render": function(data) {
                        return `
                            <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">${data}</div>
                        `;
                    }
                },
                {
                    "data": "profit_after_all",
                       "render": function(data) {
                        return `
                            <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">${data}</div>
                        `;
                    }
                },
                {
                    "data": "tax_amount",
                       "render": function(data) {
                        return `
                            <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">${data}</div>
                        `;
                    }
                },
                {
                    "data": "payable_after_all",
                       "render": function(data) {
                        return `
                            <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">${data}</div>
                        `;
                    }
                },
                {
                    "data": "created_at",
                       "render": function(data) {
                        return `
                            <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">${data}</div>
                        `;
                    }
                },
                {
                    "data": "updated_at",
                       "render": function(data) {
                        return `
                            <div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">${data}</div>
                        `;
                    }
                }
            ],
            // 'columnDefs': [
            //     { orderable: false, targets: 0 },
            //     { orderable: false, targets: 11 },
            // ]
        });

        datatable.on('draw', function () {
            KTMenu.init();
        });
    }

    const clearButton = document.querySelector('#kt_ecommerce_sales_flatpickr_clear');
    clearButton.addEventListener('click', e => {
        // alert('test')
        flatpickr.clear();
        delete window.queryFilters['date']
        datatable.ajax.reload();
        // ajaxFilters($(this));
    });

    const element = document.querySelector('#kt_ecommerce_sales_flatpickr');
    let flatpickr;
    flatpickr = $(element).flatpickr({
        altInput: true,
        altFormat: "d/m/Y",
        dateFormat: "Y-m-d",
        mode: "range",
        onChange: function (selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                window.queryFilters['date'] = dateStr;
                datatable.ajax.reload();
                // ajaxFilters($(this));
            }
        },
    });


    $(document).ready(function () {
        $("#printTable").click(function () {
            var selectedDate = $("#kt_ecommerce_sales_flatpickr").val();
            var encodedDate = encodeURIComponent(selectedDate);
            var printWindow = window.open('/admin/printOverall?date=' + JSON.stringify(window.queryFilters), '_blank');
            printWindow.onload = function () {
                printWindow.print();
                setTimeout(function () {
                    printWindow.close();
                }, 1000);
            };
        });
    });
    $("#pdfTable").click(function () {
        window.open('/admin/pdfOverall?date=' + JSON.stringify(window.queryFilters), '_blank');
    });
    $("#excelTable").click(function () {
        window.open('/admin/report/excel?date=' + JSON.stringify(window.queryFilters), '_blank');
    });

    $(document).ready(function () {
        window.queryFilters = window.queryFilters || {};
        function highlightSelectedOrder() {
            $('.order-option').closest('div.p-2').css('background-color', '');
            $('.order-option').each(function () {
                if ($(this).data('value') === window.queryFilters['order']) {
                    $(this).closest('div.p-2').css('background-color', '#F1F1F4');
                }
            });
        }
        $('.order-option').click(function () {
            var value = $(this).data('value');
            window.queryFilters['order'] = value;
            highlightSelectedOrder();
            datatable.ajax.reload();
            // ajaxFilters($(this));
        });
    });

    // function setTableOrder(tableData) {
    //     const tables = $('.order-filter-table');
    //     if (tables.length == 0) return;
    //     const mainTable = tables[0];
    //     const symbolPlacement = mainTable.getAttribute('csl');
    //     const symbol = mainTable.getAttribute('sym');
    //
    //     let orders = tableData;
    //
    //     const start = `<div class="text-gray-800 fs-10.5 flex-column justify-content-center my-0">`;
    //     const end = `</div>`;
    //
    //     const table = $(mainTable).DataTable();
    //     table.clear();
    //     for (let order of orders) {
    //         const orderRow = [];
    //
    //         orderRow.push(`
    //         <div class="form-check form-check-sm form-check-custom form-check-solid">
    //             <input class="form-check-input" type="checkbox" value="30.03.2023" />
    //         </div>
    //         `);
    //
    //         const properties = [
    //             { type: 'string', value: '<a class="text-gray-800 fs-10.5 flex-column justify-content-center my-0 text-hover-primary" href="/admin/order/' + order.id + '">' + parseInt(order.order_receipt) + '</a>' },
    //             { type: 'string', value: order.order_number },
    //             { type: 'string', value: order.order_type.replace(/_/g, ' ').toLowerCase().replace(/\b\w/g, function(char) {
    //                     return char.toUpperCase();
    //                 }) },
    //             { type: 'string', value: order.pos_or_kiosk },
    //             { type: 'string', value: order.payment_method_type },
    //             { type: 'cost', value: order.cart_total_cost },
    //             { type: 'cost', value: order.discount_amount },
    //             { type: 'cost', value: order.profit_after_all },
    //             { type: 'cost', value: order.tax_amount },
    //             { type: 'cost', value: order.payable_after_all },
    //             { type: 'date', value: order.created_at },
    //             { type: 'date', value: order.updated_at }
    //         ];
    //
    //         for (let property of properties) {
    //             if (property.type == 'cost')
    //                 property.value = symbolPlacement == 0 ? parseFloat(property.value).toFixed(2) + ' ' + symbol : symbol + ' ' + parseFloat(property.value).toFixed(2)
    //
    //             if (property.type == 'percentage')
    //                 property.value = parseFloat(property.value).toFixed(2) + ' %';
    //
    //
    //             if (property.type == 'date') {
    //                 var date = new Date(property.value);
    //                 var year = date.getFullYear();
    //                 var month = ('0' + (date.getMonth() + 1)).slice(-2);
    //                 var day = ('0' + date.getDate()).slice(-2);
    //                 var hours = ('0' + date.getHours()).slice(-2);
    //                 var minutes = ('0' + date.getMinutes()).slice(-2);
    //                 var seconds = ('0' + date.getSeconds()).slice(-2);
    //                 property.value = ` ${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    //             }
    //
    //             orderRow.push(start + property.value + end);
    //         }
    //         table.row.add(orderRow);
    //     }
    //     table.draw();
    // }

    $(document).ready(function () {
        window.queryFilters = window.queryFilters || {};

        function highlightSelectedOrderType() {
            var orderType = window.queryFilters['orderType'] || 'all';

            $('.filter-option').each(function () {
                $(this).closest('div.p-2').css('background-color', '');
                if ($(this).data('order-type') === orderType) {
                    $(this).closest('div.p-2').css('background-color', '#F1F1F4');
                }
            });
        }

        $('.filter-option').click(function () {
            var selectedOrderType = $(this).data('order-type');
            window.queryFilters['orderType'] = selectedOrderType;
            highlightSelectedOrderType();
            datatable.ajax.reload();
            // ajaxFilters($(this));
        });
    });

    $(document).ready(function () {
        window.queryFilters = window.queryFilters || {};
        function highlightSelectedOrderTaker() {
            $('.order-taker-option').closest('div.p-2').css('background-color', '');
            $('.order-taker-option').each(function () {
                if ($(this).data('order-taker-id') === window.queryFilters['orderTaker']) {
                    $(this).closest('div.p-2').css('background-color', '#F1F1F4');
                }
            });
        }
        $('.order-taker-option').click(function () {
            var orderTakerId = $(this).data('order-taker-id');
            window.queryFilters['orderTaker'] = orderTakerId;
            highlightSelectedOrderTaker();
            datatable.ajax.reload();
            // ajaxFilters($(this));
        });
    });

    $(document).ready(function () {
        window.queryFilters = window.queryFilters || {};
        function highlightSelectedChef() {
            $('.chef-option').closest('div.p-2').css('background-color', '');
            $('.chef-option').each(function () {
                if ($(this).data('chef-id') === window.queryFilters['chef']) {
                    $(this).closest('div.p-2').css('background-color', '#F1F1F4');
                }
            });
        }
        $('.chef-option').click(function () {
            var chefId = $(this).data('chef-id');
            window.queryFilters['chef'] = chefId;
            highlightSelectedChef();
            datatable.ajax.reload();
            // ajaxFilters($(this));
        });
    });

    $(document).ready(function () {
        window.queryFilters = window.queryFilters || {};
        function highlightSelectedRow() {
            var paymentMethod = window.queryFilters['paymentMethod'] || 'all';
            $('.payment-method-option').each(function () {
                if ($(this).data('payment-method') === paymentMethod) {
                    $(this).closest('div.p-2').css('background-color', '#F1F1F4');
                } else {
                    $(this).closest('div.p-2').css('background-color', '');
                }
            });
        }
        $('.payment-method-option').click(function () {
            var selectedMethod = $(this).data('payment-method');
            window.queryFilters['paymentMethod'] = selectedMethod;
            highlightSelectedRow();
            datatable.ajax.reload();
            // ajaxFilters($(this));
        });
    });


// function ajaxFilters(highlight) {
//     const tables = $('.order-filter-table');
//     if (tables.length == 0) return;
//     const mainTable = tables[0];
//     const symbolPlacement = mainTable.getAttribute('csl');
//     const symbol = mainTable.getAttribute('sym');
//     $.ajax({
//         url: `/admin/filters?filters=`+ JSON.stringify(window.queryFilters),
//         type: 'GET',
//         success: function (response) {
//             for (let key in response.totals) {
//                 if (response.totals.hasOwnProperty(key)) {
//                     $('#' + key).text(symbolPlacement == 0 ? parseFloat(response.totals[key]).toFixed(2) + ' ' + symbol : symbol + ' ' + parseFloat(response.totals[key]).toFixed(2));
//                 }
//             }
//             setTableOrder(response.orders);
//         },
//     });
//     highlight.closest('.menu').prev('button[data-kt-menu-trigger]').click();
// }


    return {
        init: function () {
            table = document.querySelector('#kt_customers_table');

            if (!table) {
                return;
            }

            initCustomerList();
        }
    }
}();

KTUtil.onDOMContentLoaded(function () {
    KTCustomersList.init();
});
