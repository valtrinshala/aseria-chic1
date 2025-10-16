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
                                message: window.keys.dealName
                            }
                        }
                    },
                    'foodItems[]': {
                        validators: {
                            notEmpty: {
                                message: window.keys.productName
                            }
                        }
                    },
                    'categories': {
                        validators: {
                            notEmpty: {
                                message: window.keys.categoryRequired
                            }
                        }
                    },
                    'price': {
                        validators: {
                            notEmpty: {
                                message: window.keys.priceRequired
                            },
                            regexp: {
                                regexp: /^[0-9]+(\.[0-9]{1,7})?$/,
                                message: window.keys.regexRequired
                            }
                        }
                    },
                    'cost': {
                        validators: {
                            notEmpty: {
                                message: window.keys.costRequired
                            },
                            regexp: {
                                regexp: /^[0-9]+(\.[0-9]{1,7})?$/,
                                message: window.keys.regexRequired
                            }
                        }
                    },
                    'selected_foodItems': {
                        validators: {
                            notEmpty: {
                                message: window.keys.productName
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
        let colorDiv = $('#colorDiv');
        let infoSpanColor = $('#infoSpanColor');
        let iconDiv = $('#iconDiv');
        let infoSpan = $('#infoSpan');

        colorDiv.show();
        iconDiv.hide();

        $('#kt_security_summary_tabs a[data-bs-toggle="tab"]').click(function () {
            let targetTabId = $(this).attr('href');

            switch (targetTabId) {
                case "#kt_security_summary_tab_pane_hours":
                    colorDiv.show();
                    infoSpanColor.show();
                    iconDiv.hide();
                    infoSpan.hide();
                    break;
                case "#kt_security_summary_tab_pane_day":
                    colorDiv.hide();
                    infoSpanColor.hide();
                    iconDiv.show();
                    infoSpan.show();
                    break;
                default:
                    colorDiv.show();
                    infoSpanColor.show();
                    iconDiv.hide();
                    infoSpan.hide();
            }
        });
        $('#categories').change(function () {
            var selectedOption = $(this).find('option:selected');
            var dataColor = selectedOption.data('color');
            $('.categoryColor').css('background-color', dataColor);
        });

        var selectedFoodItems = [];
        const currency = $('label[for="cost"]').data('currency') ?? '€';
        const direction = $('label[for="cost"]').data('direction') ?? 'end';
        $('#foodItemDropdown').on('change', function () {
            var selectedProduct = JSON.parse($(this).val());
            var urlEdit = "/admin/foodItem/" + selectedProduct.id + "/edit"
            if (!selectedProduct) {
                return;
            }
            if (selectedFoodItems.includes(selectedProduct.id)) {
                let existingRow = $('#' + selectedProduct.id);
                let inputValue = existingRow.find('#qty-' + selectedProduct.id);
                let inputSum = parseInt(inputValue.val(parseInt(inputValue.val()) + 1).val())
                parseInt(existingRow.find('[data-qty]').text(inputSum))
                if (direction == 'end') {
                    existingRow.find('[data-cost]').text((inputSum * existingRow.find('[data-cost]').attr('data-cost')).toFixed(2) + ' ' + currency)
                    existingRow.find('[data-price]').text((inputSum * existingRow.find('[data-price]').attr('data-price')).toFixed(2) + ' ' + currency)
                } else {
                    existingRow.find('[data-cost]').text(currency +' '+(inputSum * existingRow.find('[data-cost]').attr('data-cost')).toFixed(2))
                    existingRow.find('[data-price]').text(currency +' '+(inputSum * existingRow.find('[data-price]').attr('data-price')).toFixed(2))
                }
            } else {
                selectedFoodItems.push(selectedProduct.id);
                var tableBody = $('#kt_customers_table tbody');
                var newRow = $('<tr id="' + selectedProduct.id + '"></tr>');
                newRow.append('<input id="qty-' + selectedProduct.id + '" type="hidden" name="foodItems[' + selectedProduct.id + ']" value="1">');
                newRow.append('<td class="text-gray-900"><a href="' + urlEdit + '" class="text-gray-800 text-hover-primary mb-1">' + selectedProduct.name + '</a></td>');
                newRow.append('<td class="text-gray-900" data-qty = "' + 1 + '">' + 1 + '</td>');
                if (direction == 'end') {
                    newRow.append('<td class="text-gray-900" data-cost = "' + parseFloat(selectedProduct.cost).toFixed(2) + '">' + parseFloat(selectedProduct.cost).toFixed(2) + '  ' + currency + '</td>');
                    newRow.append('<td class="text-gray-900" data-price = "' + parseFloat(selectedProduct.price).toFixed(2) + '">' + parseFloat(selectedProduct.price).toFixed(2) + '  ' + currency + '</td>');
                } else {
                    newRow.append('<td class="text-gray-900" data-cost = "' + parseFloat(selectedProduct.cost).toFixed(2) + '">' + currency + ' ' + parseFloat(selectedProduct.cost).toFixed(2) + '</td>');
                    newRow.append('<td class="text-gray-900" data-price = "' + parseFloat(selectedProduct.price).toFixed(2) + '">' + currency + ' ' + parseFloat(selectedProduct.price).toFixed(2) + '</td>');
                }
                newRow.append('<td class="float-right pe-0"><div class="float-end menu-item px-3 pe-0"><a href="javascript:void(0)" class="menu-link px-3 text-danger" data-ingredient-id="new_id" data-kt-customer-table-filter="delete_row">Delete</a></div></td>');
                tableBody.append(newRow);
            }

            var totalCost = 0;
            var totalPrice = 0;
            $('#kt_customers_table tbody tr').each(function () {
                let sum = parseInt($(this).find('#qty-' + this.id).val());
                totalCost += $(this).find('[data-cost]').attr('data-cost') * sum;
                totalPrice += $(this).find('[data-price]').attr('data-price') * sum;
            });

            $('#cost').val(totalCost);
            $('#price').val(totalPrice);
            let inp = document.getElementById('foodItemDropdown');
            inp.selectedIndex = -1
            try {
                KTMenu.init();
            } catch (err) {
                // No KTMenu module found
            }
        });
        $('#kt_customers_table tbody').on('click', '[data-kt-customer-table-filter="delete_row"]', function () {
            var row = $(this).closest('tr');
            if (row.length > 0) {
                var productId = row[0].id;
                row.remove();
                selectedFoodItems = selectedFoodItems.filter(function (item) {
                    return item !== productId;
                });
            }
            var price = 0, cost = 0;
            $('#kt_customers_table tbody tr').each(function () {
                let sum = parseInt($(this).find('#qty-' + this.id).val());
                cost += $(this).find('[data-cost]').attr('data-cost') * sum;
                price += $(this).find('[data-price]').attr('data-price') * sum;
            });
            $('#price').val(price)
            $('#cost').val(cost)
        });

        $('#submitButton').on('click', function () {
            $('#kt_ecommerce_add_meal_form').submit();
        });
        $('#kt_ecommerce_add_meal_form').submit(function (e) {
            if ($('#kt_customers_table tbody tr').length === 0) {
                $('#selected_foodItems').val('')
            } else {
                $('#selected_foodItems').val('has food items')
            }
            e.preventDefault();
            if (validator) {
                validator.validate().then(function (status) {
                    if (status == 'Valid') {
                        var formData = new FormData($('#kt_ecommerce_add_meal_form')[0]);
                        $.ajax({
                            url: '/admin/meal',
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                Swal.fire({
                                    text: window.keys.dealSuccessfullyAdded,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: window.keys.confirmButtonOk,
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function () {
                                    // $('#kt_ecommerce_add_meal_form')[0].reset();
                                    // $('#kt_ecommerce_add_meal_form select').val(null).trigger('change');
                                    // $('#kt_customers_table tbody').empty();
                                    // selectedFoodItems = [];
                                    location.reload();
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
            form = document.querySelector('#kt_ecommerce_add_meal_form');

            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTModalCustomersAdd.init();
});
