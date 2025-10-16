const dynamicKeypads = {};

/*new js
// Function to toggle between dine-in, take-away, and delivery tables
$(document).ready(function() {
function toggleOrderTable(tableType) {
  if (tableType === 'dineIn') {
      $("#dineInTable").show();
      $("#takeAwayTable").hide();
      $("#deliveryTable").hide();
  } else if (tableType === 'takeAway') {
      $("#dineInTable").hide();
      $("#takeAwayTable").show();
      $("#deliveryTable").hide();
  } else if (tableType === 'delivery') {
      $("#dineInTable").hide();
      $("#takeAwayTable").hide();
      $("#deliveryTable").show();
  }
}*/


/*buttons on card for takeaway,dinein
$('.take-away').click(function() {
  toggleOrderTable('takeAway');
});

$('.dine-in').click(function() {
  //openDeskSelectionPopup();
  toggleOrderTable('dineIn');
});

$('.delivery').click(function() {
  toggleOrderTable('delivery');
  // Additional logic for handling delivery, if needed
});
});



function addToOrder(clickedElement, tableType, desk) {
  var name = $(clickedElement).text().trim();
  var price = $(clickedElement).closest('.card-body').find('.product-price').text().trim().replace();

  var orderTableBody;
  if (tableType === 'dineIn') {
      // Append desk information to the name for Dine In orders
      name += ' - ' + desk;
      orderTableBody = $("#dineInOrderTableBody");
  } else if (tableType === 'takeAway') {
      orderTableBody = $("#takeAwayOrderTableBody");
  } else if (tableType === 'delivery') {
      orderTableBody = $("#deliveryOrderTableBody");
  }

  var newRow = $("<tr class='box-bg table-radius'>");

  newRow.append($("<td class='fw-bold fs-4'>").text(name));

  var quantityColumn = $("<td>").addClass("quantity-column");
  // ... (rest of your quantityColumn code)

  newRow.append(quantityColumn);

  newRow.append($("<td class='price-column text-violet fs-4'>").text(price + "€"));

  orderTableBody.append(newRow);

  updateTotalSpan();
}*/






var defaultThemeMode = "light";
var themeMode;
if (document.documentElement) {
    if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
        themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
    } else {
        if (localStorage.getItem("data-bs-theme") !== null) {
            themeMode = localStorage.getItem("data-bs-theme");
        } else {
            themeMode = defaultThemeMode;
        }
    }
    if (themeMode === "system") {
        themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
    }
    document.documentElement.setAttribute("data-bs-theme", themeMode);
}



const openModalBtn = document.getElementById('proced');
let myModal = document.getElementById('myModal');


openModalBtn.addEventListener('click', function () {
    if (currentMode == 'none') {
        Swal.fire({
            text: window.keys.formsDeliveryProceed,
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: window.keys.confirmButtonOk,
            customClass: {
                confirmButton: "btn fw-bold btn-primary",
            }
        });
    } else {
        $(myModal).modal('show')
    }
});


var cashModal = document.getElementById('cashModal');

$(document).on('click', '.meal-drink-swap', function() {
    swapMealItem('drink', 'drinks', this);
});

$(document).on('click', '.meal-fries-swap', function() {
    swapMealItem('fries', 'fries', this);
});

$(document).on('click', '.meal-sauces-swap', function() {
    swapMealItem('sauces', 'sauces', this);
});


function swapMealItem(itemType, itemKey, self) {
    let validKeys = ['main_meal', 'drinks', 'sauces', 'fries'];
    if (!validKeys.includes(itemKey)) return;

    const mapper = {
        'drinks': 'drink_num',
        'fries': 'fries_num',
        'sauces': 'sauces_num'
    };

    if (self.classList.contains('btn-primary')) return;

    let currentDrinkNum = self.getAttribute(`${itemType}-num`);

    let otherItems = document.querySelectorAll(`.drink-active-holder a[${itemType}-num="${currentDrinkNum}"]`);

    for (let item of otherItems) {
        item.classList.remove('btn-primary');
        item.classList.add('btn-secondary');
    }

    self.classList.add('btn-primary');
    self.classList.remove('btn-secondary');

    if (!(currentEditting in mealConfigurations)) {
        mealConfigurations[currentEditting] = {
            main_meal: {},
            drinks: {},
            sauces: {},
            fries: {}
        }
    }

    if (!(currentDrinkNum in mealConfigurations[currentEditting][itemKey]))
        mealConfigurations[currentEditting][itemKey][currentDrinkNum] = { type: '', modifiers: [], sizeSelected: {}}

    const id = self.getAttribute(`${itemType}-id`);
    const ownerId = self.getAttribute('owner');

    const newProductInfo = currentProducts[id];

    const cat = self.getAttribute('group');

    if (cat in modifyingDealPrep) {
        let currentDetails = {};
        for (let item of modifyingDealPrep[cat]) {

            if (item.id == id) {
                currentDetails = item;
                break;
            }
        }

        let extraIndex = self.getAttribute(itemType  + '-num');

        const currentGenSwapper = window[itemKey + "_modGenSwapper"];
        currentGenSwapper.changeParent(`.${itemType}-size[sel-num="${extraIndex}"]`)
        // currentGenSwapper.attach(`.drink-mod- sizes-layout[drink-num="${extraIndex}"]`);
        currentGenSwapper.clear();
        let sizes = currentDetails.size;
        let first = true;
        for (let sizeKey in sizes) {
            let sizePrice = sizes[sizeKey];
            if (sizePrice == null) continue;
            let price = parseFloat(sizePrice).toFixed(2);
            let viewPrice = price;
            let plus = '+';

            let name = sizeKey;
            let active_class = '';
            if (first) {
                viewPrice = currentDetails.price;
                active_class = 'active-mod';
                plus = '';
                first = false;
            }

            if (sizeKey in window.sizeKeys) name = window.sizeKeys[sizeKey];

            const insertObj = {
                product_id: currentDetails.id,
                mod_name: name,
                mod_price: price,
                mod_id: sizeKey,
                active_class: active_class,
                mod_price_view: viewPrice,
                plus
            };

            insertObj[mapper[itemKey]] = extraIndex;

            currentGenSwapper.insert(insertObj);
        }
    }

    mealConfigurations[currentEditting][itemKey][currentDrinkNum].type = id;
    if (ownerId != id) {
        const owner = currentProducts[ownerId];
        const extraPrice = parseFloat((parseFloat(newProductInfo.price) - parseFloat(owner.price)).toFixed(5));

        mealConfigurations[currentEditting][itemKey][currentDrinkNum].extraPrice = extraPrice;
    } else mealConfigurations[currentEditting][itemKey][currentDrinkNum].extraPrice = null;

    mealConfigurations[currentEditting][itemKey][currentDrinkNum].sizeSelected = {};

    if (currentEditting in currentOrderList) {
        let row = currentOrderList[currentEditting];
        updateQuantity(row, 0, 0, [], true);
    }

    updateTotalSpan()
}


$(document).on('click', '.meal-fries-mod-swap', function() {
    handleModifierClick(this);
});

$(document).on('click', '.meal-sauces-mod-swap', function() {
    handleModifierClick(this);
});

$(document).on('click', '.meal-drink-mod-swap', function() {
    handleModifierClick(this);
    /*
    if (this.classList.contains('active-mod')) return;

    let currentDrinkNum = this.getAttribute('drink-num');

    let otherDrinks = document.querySelectorAll(`.drink-mod-modifier-layout a[drink-num="${currentDrinkNum}"]`);

    for (let drink of otherDrinks) {
        drink.classList.remove('active-mod');
    }

    this.classList.add('active-mod');
    */

    /*
    if (!(currentEditting in mealConfigurations)) {
        mealConfigurations[currentEditting] = {
            main_meal: {},
            drinks: {},
            sauces: {},
            fries: {}
        }
    }

    if (!(currentDrinkNum in mealConfigurations[currentEditting].drinks))
        mealConfigurations[currentEditting].drinks[currentDrinkNum] = { type: '', modifiers: {} }

    mealConfigurations[currentEditting].drinks[currentDrinkNum].type = this.getAttribute('drink-id');
    */
});


$(document).on('click', '.paymentMethods li a[type="Cash"]', function() {
    clearRecipientAmount();
    updateTotalAndBalance()
    cashCalculations.clear();
    cashCalculations.change('total', superTotal);
    updateRecipientAmount('0', false);
    updateTotalAndBalance()


    window.method = {};
    window.method.id = this.getAttribute('data-table-id')
    window.method.type = this.getAttribute('type');
    for (let opened of openedPaymentModals) {
        $(opened).modal('hide');
    }
    openedPaymentModals.push(cashModal);
    $(cashModal).modal('show');
})


var discountModalBtn = document.getElementById('discountbtn');
var discountModal = document.getElementById('discountModal');


discountModalBtn.addEventListener('click', function () {

$(discountModal).modal('show');

});



var bankModal = document.getElementById('bankModal');
$(document).on('click', '.paymentMethods li a[type="Card"]', function() {
    window.method = {};
    window.method.id = this.getAttribute('data-table-id')
    window.method.type = this.getAttribute('type');
    for (let opened of openedPaymentModals) {
        $(opened).modal('hide');
    }
    openedPaymentModals.push(bankModal);
    $(bankModal).modal('show');
})

var mixModal = document.getElementById('mixModal');

$(document).on('click', '.paymentMethods li a[type="Mix"]', function() {
    clearMixInputs();
    cashCalculationsMix.clear();
    cashCalculationsMix.change('total', superTotal);
    runCalculationsMix('0', false);
    updateMixInputs();

    window.method = {};
    window.method.id = this.getAttribute('data-table-id')
    window.method.type = this.getAttribute('type');
    for (let opened of openedPaymentModals) {
        $(opened).modal('hide');
    }
    openedPaymentModals.push(mixModal);
    $(mixModal).modal('show');
})

var notesModalBtn = document.getElementById('notesbtn');
var notesModal = document.getElementById('notesModal');


notesModalBtn.addEventListener('click', function () {

$(notesModal).modal('show');

});



// var editModalBtn = document.getElementById('edit-td');
// var editModal = document.getElementById('edit-itemModal');


// editModalBtn.addEventListener('click', function () {

// $(editModal).modal('show');

// });
// var tableModalBtn = document.getElementById('tablebtn');
// var tableModal = document.getElementById('tableModal');


// tableModalBtn.addEventListener('click', function () {

// $(tableModal).modal('show');

// });



document.addEventListener('DOMContentLoaded', function () {
    var serviceTableLinks = document.querySelectorAll('.tables li a');
    var saveButton = document.querySelector('.save-button');
    var orderTableBody = document.getElementById('orderTableBody');
    var selectedTableId = null;

    serviceTableLinks.forEach(function (tableLink) {
        tableLink.addEventListener('click', function () {

            selectedTableId = this.getAttribute('data-table-id');


            orderTableBody.innerHTML = '';


            var sampleRow = '<tr><td></td><td>Sample Item</td><td>1</td><td>$10.00</td><td></td></tr>';
            orderTableBody.innerHTML = sampleRow;
        });
    });

    saveButton.addEventListener('click', function () {

        var selectedTableElement = document.querySelector('.serviceTabe-' + selectedTableId);
        if (selectedTableElement) {
            selectedTableElement.style.backgroundColor = '#FF36361A';
            selectedTableElement.style.borderColor = '#FF3636';
        }
    });
});




$(document).ready(function() {
    $('.edit-tr').click(function() {
        $('#orderTableBody>tr').css('transform', 'translateX(0px)');
    });
});


$(document).on("click", '.close-payment-modals', function(e) {
    $('#myModal').modal('hide');
    $('#cashModal').modal('hide');
    $('#bankModal').modal('hide');
    $('#mixModal').modal('hide');
});

$(document).on("click", '.locator-keys', function(e) {
    if (this.classList.contains('extra')) {
        if (this.classList.contains('close')) {
            locatorNumber = '';
            $('#loc-num').val('');
            $('#tableModal').modal('hide');
            return;
        }

        if (this.classList.contains('confirm')) {
            locatorNumberFinal = locatorNumber;
            locatorNumber = ''
            $('#loc-num').val('');
            $('#tableModal').modal('hide');

            prepTable();
            return;
        }
        return;
    }

    if (this.value == 'back') {
        if (locatorNumber.length > 0) {
            locatorNumber = locatorNumber.substr(0, locatorNumber.length - 1);
        }
    } else {
        locatorNumber += this.value;
    }

    $('#loc-num').val(locatorNumber);
});


$(document).on("click", '.plus-btn', function() {
    const rowCheck = $(this).closest('.row-ter');
    if (!(0 in rowCheck)) return;

    updateQuantity(rowCheck, 1, rowCheck[0].price);
});

$(document).on("click", '[id-open]', function(e) {
    let id = this.getAttribute('id-open');
    const options = id.split('-');
    if (options.length > 1) {
        let others = options[1];
        $(`[id*="-${options[1]}"]`).addClass('hide-id');
    }

    $(`#${id}`).removeClass('hide-id');
});

let showingKeyboard = true;
$(document).on("click", '.show-keyboard', function() {
    if (showingKeyboard)
        $('.calculator').addClass('d-none');
    else
        $('.calculator').removeClass('d-none');

    showingKeyboard = !showingKeyboard;
});

$(document).on("click", '.add-disc', function() {
    let key = 'disc-in';
    let discount = $(`#${key}`);
    // let discountValue = parseFloat(discount.val());
    let discountValue = 0;
    if (key in dynamicKeypads) {
        discountValue = dynamicKeypads[key].steppedValue;
        dynamicKeypads[key].currentValue = discountValue;
        dynamicKeypads[key].lastValue = discountValue;
        discountValue = parseFloat(discountValue);
    }

    // False = percentage
    // True = solid
    let discountType = $('#disc-type');
    let discountT = discountType[0].checked;
    let area = $('.discount-area');
    let areaAmount = $('.discount-area-amount');
    if (isNaN(discountValue)) {
        discountValue = 0;
        // $('#discountModal').modal('hide');
        // return;
    }

    window.discountT = discountT ? 'solid' : 'perc';
    window.discountAmount = discountValue;
    const totPrice = calculateTotalPrice(true);

    if (discountT) {
        let price = discountValue.toFixed(2);
        let priceString = window.currency_symbol + price;
        if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;

        // areaAmount.text(priceString);


        let areaText = '0%';
        if (discountValue != 0) areaText = ((parseFloat(totPrice.noDisc / discountValue)) * 100).toFixed(2) + '%';
        // area.text(areaText);
    } else {
        // area.text(`${discountValue.toFixed(2)}%`);

        let price = parseFloat(parseFloat(totPrice.noDisc) * (discountValue * 0.01)).toFixed(2);
        let priceString = window.currency_symbol + price;
        if (window.currency_pos_left == 0) priceString = price + window.currency_symbol;
        // areaAmount.text(priceString)
    }

    $("#discountModal").modal("hide");
    if (!window.manualDisc) updateTotalSpan();
    window.manualDisc = false;
});

$(".discard").click(function(e) {
    let key = 'disc-in';
    e.preventDefault();
    $("#discountModal").modal("hide");

    if (key in dynamicKeypads) {
        dynamicKeypads[key].currentValue = dynamicKeypads[key].lastValue;
        dynamicKeypads[key].steppedValue = dynamicKeypads[key].lastValue;
    }
});

$(document).on("click", '.minus-btn', function() {
    const rowCheck = $(this).closest('.row-ter');
    if (!(0 in rowCheck)) return;

    updateQuantity(rowCheck, -1, rowCheck[0].price);
});

$(document).on("click", '.inner-tab', function(e) {
    const holder = $(this).parent().parent();
    if (holder && holder.length > 0) {
        const tabs = holder[0].querySelectorAll('.inner-tab');
        for (let tab of tabs) {
            if (tab == $(this)[0]) continue;
            tab.nextElementSibling.classList.remove('active');
        }
    }

    $(this).next().each((index, e) => {
        e.classList.toggle('active');
    });
})

$(document).on("click", '.tab-meal-switcher', function(e) {
    if (!('manualStep' in window) || !window.manualStep) {
        stepController.currentStep = Infinity;
    }

    $('.tab-meal-switcher').each((index, tab) => {
        tab.classList.remove('active');
    });

    this.classList.add('active');


    let currentTab = this.getAttribute('tab');
    const modifierTabs = $('.meal-modifier-tabs');
    modifierTabs.each((index, tab) => {
        tab.style.display = 'none';
    })

    $(`.meal-modifier-tabs[layout="${currentTab}"]`).show();
})

$(document).on("click", '.moved-edit .edit-td', function(e) {
    const rowCheck = $(this).closest('.row-ter');
    if (!(0 in rowCheck)) return;
    const currentRow = rowCheck[0];
    modalType = 0;

    editRow(currentRow);

    $(this).closest('tr')[0].classList.remove('moved-edit');
    // $(this).css('transform', 'translateX(-94px)');
});

$(document).on("click", '#orderTableBody tr td.table-right-side', function(e) {
    const editTd = $(this).closest('tr')[0];
    const isOn = editTd.classList.contains('moved-delete');

    $('.moved-edit').removeClass('moved-edit');
    $('.moved-delete').removeClass('moved-delete');

    editTd.classList.remove('moved-edit');

    if (!isOn)
        editTd.classList.add('moved-delete');
});

$(document).on("click", '#orderTableBody tr td.table-left-side', function(e) {
    const editTd = $(this).closest('tr')[0];
    const isOn = editTd.classList.contains('moved-edit');

    $('.moved-edit').removeClass('moved-edit');
    $('.moved-delete').removeClass('moved-delete');

    editTd.classList.remove('moved-delete');
    if (!isOn)
        editTd.classList.add('moved-edit');
});

$(document).on('click', '.print-last-receipt', function() {
    const lastPrintedString = localStorage.getItem('last_printed_string');
    if (lastPrintedString == null) {
        Swal.fire({
            text: `{{ __('There is no order history') }}`,
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: window.keys.confirmButtonOk,
            customClass: {
                confirmButton: "btn fw-bold btn-primary",
            }
        })

        return;
    }

    window.invoicePrinting(lastPrintedString);
})

$(document).on('click', '.open-reports-modal', function() {
    $("#moreModal").modal('hide');
    $('#report-more-modal').modal('show');
})


$(document).on('click', '.print-last-z-report', function() {
    $.ajax({
        method: 'GET',
        url: '/admin/pos/printLastZReport',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        success: function (resp) {
            if (resp.status == 2) {
                location.href = resp.redirect_uri;
                return;
            }

            if (resp.status == 1) {
                // These will change with the library in the next update
                Swal.fire({
                    text: resp.message,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: window.keys.confirmButtonOk,
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
                return;
            }

            if (resp.status == 0) {
                let data = resp.data;

                if (window.printer_testing || window.connected_printer) {
                    if ('printString' in data) {
                        window.invoicePrinting(data.printString);
                    } else {
                        Swal.fire({
                            text: "Probleme me kthimin e serverit ju lutem dergoni mesazh tek mirembajtesit",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: window.keys.confirmButtonOk,
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                        return;
                    }
                } else {
                    Swal.fire({
                        text: window.keys.printerNotConfigured,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: window.keys.confirmButtonOk,
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
            }

            if ('message' in resp && resp.message != '') {
                Swal.fire({
                    text: resp.message,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: window.keys.confirmButtonOk,
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                })
            }
        },
        error: function(err) {
            console.log(err);
            Swal.fire({
                text: window.keys.unexpectedError,
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: window.keys.confirmButtonOk,
                customClass: {
                    confirmButton: "btn fw-bold btn-primary",
                }
            });
        }
    })
});

$(document).on('click', '.size-list-item', function(e) {
    const otherKey = this.getAttribute('data-other');
    const size = this.getAttribute('data-size');
    const activeClass = this.getAttribute('other-class');

    if (currentEditting in currentOrderList) {
        let row = currentOrderList[currentEditting];
        let index = $(row)[0].indexFor;

        setRowSize(index, size, this);

        let cl = 'btn-violet';
        if (activeClass) cl = activeClass;

        $(`.size-list-item[data-other="${otherKey}"]`).removeClass(cl);
        this.classList.add(cl);

        updateQuantity(row, 0, 0, [], true);
        updateTotalSpan();
    }
});

$(document).on('click', '#discountbtn', function() {
    const key = 'disc-in';
    let value = '';
    if (key in dynamicKeypads) value = dynamicKeypads[key].steppedValue;
    document.getElementById('disc-in').value = value;
});

$(document).on('click', '.dynamic-keypad', function() {
    if (this.classList.contains('disabled')) return;

    const targetId = this.getAttribute('targetting');

    const target = document.getElementById(targetId);
    if (!target) return;

    let currentPad = {steppedValue: '', lastValue: '', currentValue: ''};
    if (targetId in dynamicKeypads) currentPad = dynamicKeypads[targetId];
    else dynamicKeypads[targetId] = currentPad;

    let val = this.getAttribute('value');
    if (val == 'back') {
        currentPad.steppedValue = currentPad.steppedValue.substring(0, currentPad.steppedValue.length - 1);
        if (targetId == 'disc-in' && 'clearDiscount' in window && window.clearDiscount == true) {
            currentPad.steppedValue = '';
            currentPad.lastValue = '';
            currentPad.currentValue = '';
            window.clearDiscount = false;
        }
    } else {
        currentPad.steppedValue += val;
    }

    if ('keypadEvent' in target && typeof(target.keypadEvent) == 'function')
        target.keypadEvent(currentPad.steppedValue);
});

$(document).ready(function() {
    let key = 'disc-in';
    const discountInput = document.getElementById(key);
    discountInput.keypadEvent = val => {
        discountInput.value = val;
    }
});
