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
                    'title': {
                        validators: {
                            notEmpty: {
                                message: window.keys.modifierTitle
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
                    'selected_ingredients': {
                        validators: {
                            notEmpty: {
                                message: window.keys.ingredientsRequired
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
            let infoSpanColor = $('#infoSpanColor')
            let iconDiv = $('#iconDiv');
            let infoSpanDiv = $('#infoSpanDiv');

            colorDiv.show();
            iconDiv.hide();

            $('#kt_security_summary_tabs a[data-bs-toggle="tab"]').click(function () {
                let targetTabId = $(this).attr('href');

                switch (targetTabId) {
                    case "#kt_security_summary_tab_pane_hours":
                        colorDiv.show();
                        infoSpanColor.show();
                        iconDiv.hide();
                        infoSpanDiv.hide();
                        break;
                    case "#kt_security_summary_tab_pane_day":
                        colorDiv.hide();
                        infoSpanColor.hide();
                        iconDiv.show();
                        infoSpanDiv.show();
                        break;
                    default:
                        colorDiv.show();
                        infoSpanColor.show();
                        iconDiv.hide();
                        infoSpanDiv.hide();
                }
            });
        $('#categories').change(function () {
            var selectedOption = $(this).find('option:selected');
            var dataColor = selectedOption.data('color');
            $('.categoryColor').css('background-color', dataColor);
        });
        var selectedIngredients = [];
        const currency = $('label[for="cost"]').data('currency') ?? '€';
        const direction = $('label[for="cost"]').data('direction') ?? 'end';
        $('#ingredientDropdown').on('change', function () {
            var selectedIng = JSON.parse($(this).val());
            var urlEdit = "/admin/ingredient/" + selectedIng.id + "/edit"
            if (!selectedIng) {
                return;
            }
            if (selectedIngredients.includes(selectedIng.id)) {
                return;
            } else {
                selectedIngredients.push(selectedIng.id);
                var tableBody = $('#kt_customers_table tbody');
                var newRow = $('<tr id="' + selectedIng.id + '"></tr>');
                var selectOptions = [];
                for (var key in window.units) {
                    if ((selectedIng.unit.toLowerCase() === 'kilogram' || selectedIng.unit.toLowerCase() === 'gram') && (key.toLowerCase() === 'kilogram' || key.toLowerCase() === 'gram')) {
                        selectOptions.push('<option ' + (selectedIng.unit === window.units[key] ? 'selected' : '') + ' value="' + key + '">' + window.units[key] + '</option>');
                    } else if ((selectedIng.unit.toLowerCase() === 'liter' || selectedIng.unit.toLowerCase() === 'milliliter') && (key.toLowerCase() === 'liter' || key.toLowerCase() === 'milliliter')) {
                        selectOptions.push('<option ' + (selectedIng.unit === window.units[key] ? 'selected' : '') + ' value="' + key + '">' + window.units[key] + '</option>');
                    } else if (selectedIng.unit === window.units[key]) {
                        selectOptions.push('<option ' + (selectedIng.unit === window.units[key] ? 'selected' : '') + ' value="' + key + '">' + window.units[key] + '</option>');
                    }
                }
                let ratio = selectedIng.unit.toLowerCase() === 'milliliter' || selectedIng.unit.toLowerCase() === 'gram' ? 0.001 : 1 ;
                newRow.append('<input id="qty-' + selectedIng.id + '" data-qty-final="'+ selectedIng.id +'" type="hidden" name="ingredients[' + selectedIng.id + '][qty]" value=1>');
                newRow.append('<input id="qty-' + selectedIng.id + '" data-unit-final="'+ selectedIng.id +'" type="hidden" name="ingredients[' + selectedIng.id + '][unit]" value="'+ selectedIng.unit +'">');
                newRow.append('<input id="qty-' + selectedIng.id + '" data-cost-final="'+ selectedIng.id +'" type="hidden" name="ingredients[' + selectedIng.id + '][cost]" value="'+ selectedIng.cost +'">');
                newRow.append('<input id="qty-' + selectedIng.id + '" data-price-final="'+ selectedIng.id +'" type="hidden" name="ingredients[' + selectedIng.id + '][price]" value="'+ selectedIng.price +'">');
                newRow.append('<input id="qty-' + selectedIng.id + '" data-cost-per-unit-final="'+ selectedIng.id +'" type="hidden" name="ingredients[' + selectedIng.id + '][cost_per_unit]" value="'+ ratio * selectedIng.cost +'">');
                newRow.append('<input id="qty-' + selectedIng.id + '" data-price-per-unit-final="'+ selectedIng.id +'" type="hidden" name="ingredients[' + selectedIng.id + '][price_per_unit]" value="'+ ratio * selectedIng.price +'">');
                newRow.append('<td class="text-gray-900"><a href="' + urlEdit + '" class="text-gray-800 text-hover-primary mb-1">' + selectedIng.name + '</a></td>');
                newRow.append('<td class="text-gray-900" data-quantity = "' + selectedIng.quantity + '">' + parseFloat(selectedIng.quantity) + ' ' + selectedIng.unit + '</td>');

                newRow.append('<td class="text-gray-900"><input data-system-unit="'+selectedIng.unit+'"  value="1" type="number" step="0.1" data-qty="qty" class="form-control qty-units"></td>');
                newRow.append('<td class="text-gray-900"><select class="form-select form-control qty-units" data-control="select2" data-hide-search="true" data-unit="unit"><option></option>' + selectOptions.join('') + '</select></td>');

                if (direction == 'end') {
                    newRow.append('<td class="text-gray-900" data-final-cost = "' + parseFloat(selectedIng.cost).toFixed(2) + '" data-cost = "' + parseFloat(selectedIng.cost).toFixed(2) + '">' + parseFloat(selectedIng.cost).toFixed(2) + '  ' + currency + '</td>');
                    newRow.append('<td class="text-gray-900" data-final-price = "' + parseFloat(selectedIng.price).toFixed(2) + '" data-price = "' + parseFloat(selectedIng.price).toFixed(2) + '">' + parseFloat(selectedIng.price).toFixed(2) + '  ' + currency + '</td>');
                } else {
                    newRow.append('<td class="text-gray-900" data-final-cost = "' + parseFloat(selectedIng.cost).toFixed(2) + '" data-cost = "' + parseFloat(selectedIng.cost).toFixed(2) + '">'+ currency + ' ' + parseFloat(selectedIng.cost).toFixed(2)+'</td>');
                    newRow.append('<td class="text-gray-900" data-final-price = "' + parseFloat(selectedIng.price).toFixed(2) + '" data-price = "' + parseFloat(selectedIng.price).toFixed(2) + '">'+ currency + ' ' + parseFloat(selectedIng.price).toFixed(2)+'</td>');
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

            $('#cost').val(totalCost.toFixed(2));
            $('#price').val(totalPrice.toFixed(2));
            let inp = document.getElementById('ingredientDropdown');
            inp.selectedIndex = -1
            try {
                KTMenu.init();
            } catch (err) {
                // No KTMenu module found
            }
        });

        $('#kt_customers_table tbody').on('input', '.qty-units', function() {

            let row = $(this).closest('tr');
            let inputValue = row.find('[data-qty]').val();
            let unitValue = row.find('[data-unit]').val();
            let cost = parseFloat(row.find('[data-cost]').data('cost'));
            let price = parseFloat(row.find('[data-price]').data('price'));
            let systemUnit = row.find('[data-system-unit]').data('system-unit');
            let totalCost, totalPrice;
            const systemUnitLower = systemUnit.toLowerCase();
            const unitValueLower = unitValue.toLowerCase();
            const conversionFactors = {
                'liter': {'milliliter': 0.001},
                'milliliter': {'liter': 1000},
                'kilogram': {'gram': 0.001},
                'gram': {'kilogram': 1000}
            };
            if (systemUnitLower === unitValueLower || unitValueLower === "unit") {
                totalCost = cost * inputValue;
                totalPrice = price * inputValue;
            } else {
                if (conversionFactors.hasOwnProperty(systemUnitLower) && conversionFactors[systemUnitLower].hasOwnProperty(unitValueLower)) {
                    const conversionFactor = conversionFactors[systemUnitLower][unitValueLower];
                    totalCost = cost * inputValue * conversionFactor;
                    totalPrice = price * inputValue * conversionFactor;
                } else {
                    alert("The units are incorrect!")
                }
            }
            row.find('[data-qty-final]').val(inputValue);
            row.find('[data-unit-final]').val(unitValue);
            let ratio1 = unitValue.toLowerCase() === 'milliliter' || unitValue.toLowerCase() === 'gram' ? 0.001 : 1 ;
            row.find('[data-cost-final]').val(totalCost);
            row.find('[data-price-final]').val(totalPrice);
            row.find('[data-cost-per-unit-final]').val(totalCost * ratio1);
            row.find('[data-price-per-unit-final]').val(totalPrice * ratio1);

            row.find('[data-final-cost]').attr('data-final-cost', totalCost.toFixed(2));
            row.find('[data-final-price]').attr('data-final-price', totalPrice.toFixed(2));
            if (direction === 'end') {
                row.find('[data-cost]').text((totalCost).toFixed(2) + ' ' + currency)
                row.find('[data-price]').text((totalPrice).toFixed(2) + ' ' + currency)
                row.find('[data-final-cost]').attr('data-final-cost', totalCost.toFixed(2));
            } else {
                row.find('[data-cost]').text(currency + ' ' + (totalCost).toFixed(2))
                row.find('[data-price]').text(currency + ' ' + (totalPrice).toFixed(2))
                row.attr("data-final-cost", totalCost.toFixed(2));
                row.attr("data-final-price", totalPrice.toFixed(2));
            }
            let allCost = 0, allPrice = 0;
            $('#kt_customers_table tbody tr').each(function () {
                allCost += parseFloat($(this).find('[data-final-cost]').attr('data-final-cost'));
                allPrice += parseFloat($(this).find('[data-final-price]').attr('data-final-price'));
            });
            $('#cost').val(allCost.toFixed(2));
            $('#price').val(allPrice.toFixed(2));
        });

        $('#kt_customers_table tbody').on('click', '[data-kt-customer-table-filter="delete_row"]', function () {
            var row = $(this).closest('tr');
            if (row.length > 0) {
                var ingredientId = row[0].id;
                row.remove();
                selectedIngredients = selectedIngredients.filter(function (item) {
                    return item !== ingredientId;
                });
            }
            let allCost = 0, allPrice = 0;
            $('#kt_customers_table tbody tr').each(function () {
                allCost += parseFloat($(this).find('[data-final-cost]').attr('data-final-cost'));
                allPrice += parseFloat($(this).find('[data-final-price]').attr('data-final-price'));
            });
            $('#cost').val(allCost.toFixed(2));
            $('#price').val(allPrice.toFixed(2));
        });
        $('#submitButton').on('click', function () {
            $('#kt_ecommerce_add_modifier_form').submit();
        });
        $('#kt_ecommerce_add_modifier_form').submit(function (e) {
            if ($('#kt_customers_table tbody tr').length === 0) {
                $('#selected_ingredients').val('')
            } else {
                $('#selected_ingredients').val('has ingredients')
            }
            e.preventDefault();
            if (validator) {
                validator.validate().then(function (status) {
                    if (status == 'Valid') {
                        var formData = new FormData($('#kt_ecommerce_add_modifier_form')[0]);
                        $.ajax({
                            url: '/admin/modifier',
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                Swal.fire({
                                    text: window.keys.modifierAdded,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: window.keys.confirmButtonOk,
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function (){
                                    // $('#kt_ecommerce_add_modifier_form')[0].reset();
                                    // $('#kt_ecommerce_add_modifier_form select').val(null).trigger('change');
                                    // $('#kt_customers_table tbody').empty();
                                    // selectedIngredients = [];
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
            form = document.querySelector('#kt_ecommerce_add_modifier_form');

            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTModalCustomersAdd.init();
});
