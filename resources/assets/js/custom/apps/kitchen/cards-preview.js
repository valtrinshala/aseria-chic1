window.queuedStrings = {
    receipt: []
}

let printingObj = {
    printing: false,
    order: null
}


// Prints that are finished but have not gone to server yet
let autoPrinted = {};
// Using {} instead of [] to not duplicate and have an easier check
let processingAutoPrint = {};
const autoPrintPart = localStorage.getItem('auto_print_orders');
if (autoPrintPart != null) {
    try {
        autoPrinted = JSON.parse(autoPrintPart);
    } catch(err) {
        localStorage.removeItem('auto_print_orders');
    }
}

window.orderPagMan = {
    parentEl: '.card-pagination',
    placementElement: '.card-pagination .pag-numbers',
    activeClass: 'active',
    reachableClass: 'reachable',
    clickSelect: '[page]',
    elementHTML: '<button class="pag-btn number {active_class} {reachable_class}" page="{number}">{number}</button>',
    current: 1,
    reachableDistance: 5,
    pagAmount: 0,
    maxPags: 5,
    currPagAmount: 0,
    orderElements: {},
    setup: function() {
        const prevBtn = document.querySelector(this.parentEl + ' ' + '.previous');
        const nextBtn = document.querySelector(this.parentEl + ' ' + '.next');

        prevBtn.addEventListener('click', e => {
            if (window.orderPagMan.current - 1 < 1) return;
            window.orderPagMan.clickPag(--window.orderPagMan.current);
            window.orderPagMan.orderReset();
        });

        nextBtn.addEventListener('click', e => {
            if (window.orderPagMan.current > Object.keys(window.orderPagMan.orderElements).length - 1) return;
            window.orderPagMan.clickPag(++window.orderPagMan.current);
            window.orderPagMan.orderReset();
        });
    },
    addPag: function(orderElement = null) {
        if (this.pagAmount >= this.maxPags) return;
        let number = this.currPagAmount + 1;
        this.currPagAmount++;
        if (this.currPagAmount < this.current - (this.maxPags * .5)) return;


        this.orderElements[number] = orderElement;

        const place = document.querySelector(this.placementElement);
        if (!place) return;

        let html = this.elementHTML;

        html = html.replaceAll('{number}', number);

        let activeClass = '';
        let reachableClass = '';
        if (number == this.current) {
            activeClass = this.activeClass;
        } else {
            if (number < this.current + this.reachableDistance && number > this.current - this.reachableDistance)
                reachableClass = this.reachableClass;

        }

        html = html.replaceAll('{active_class}', activeClass);
        html = html.replaceAll('{reachable_class}', reachableClass);


        place.innerHTML += html;

        const pags = document.querySelectorAll(this.placementElement + " " + this.clickSelect);
        for (let pag of pags) {
            pag.addEventListener('click', e => {
                const page = pag.getAttribute('page');
                orderPagMan.clickPag(page);
            });
        }

        this.pagAmount++;
    },
    clearPags: function() {
        this.currPagAmount = 0;
        this.pagAmount = 0;
        const place = document.querySelector(this.placementElement);

        if (place) place.innerHTML = '';
    },
    clickPag: function(number) {
        this.current = number;
        const pageElement = orderPagMan.orderElements[this.current];
        if (pageElement) {
            pageElement.scrollIntoView({behavior: 'smooth'});
        }

        if ('orderReset' in window.orderPagMan) window.orderPagMan.orderReset();
    }
}



const orderMan = {
    clearOrders: function() {
        const orders = document.querySelectorAll('.order-card[order]');
        if (orders.length > 0) {
            for (let i = orders.length - 1; i >= 0; i--) {
                orders[i].remove(0);
            }
        }

    },
    addOrder: function(order) {
        const ordering = document.getElementById('ordering');

        let noSpaceStatus = order.status.replaceAll(' ', '_');

        const orderHolder = document.createElement('div');
        orderHolder.classList.add('order-card');
        orderHolder.classList.add('holder');
        orderHolder.classList.add('w-fit-content');
        orderHolder.classList.add('mt-3');
        orderHolder.classList.add('flex-shrink-0');
        orderHolder.classList.add(noSpaceStatus);

        orderHolder.setAttribute('order', order.id);
        orderHolder.setAttribute('cancel_amount', order.cancel_amount);

        let note = '';

        if (order.note != '') {
            note = `
                <div class="Note mb-4 p-4">
                    <div class="title fw-bold">${window.keys.note}:</div>
                    <div class="note">${order.note}</div>
                </div>
            `;
        }

        const buttons = ordering.getAttribute('buttons');
        let buttonsHtml = '';
        let printButton = '';
        if (buttons) {
            const btns = buttons.split(',');
            for (let btn_comp of btns) {
                const btn_parts = btn_comp.split('_');
                let btn = btn_parts[0];
                let disabled = '';
                if (btn_parts.length > 1) {
                    if (btn_parts[1] == 'disabled')
                        disabled = 'disabled';
                }

                if (btn == 'edit') {
                    buttonsHtml += `
                        <a href="${window.routes.pos_index}?order_id=${order.id}" ${disabled} class="px-5 py-3 col btn btn-primary no-hover d-flex align-items-center justify-content-center w-100">
                            ${window.keys.editOrder}
                        </a>
                    `;
                }

                if (btn == 'print') {

                    printButton += `
                        <button ${disabled} class="px-1 py-3 print-order col btn btn-primary d-flex align-items-center gap-3 justify-content-center w-100">
                            ${window.keys.printOrder}
                        </button>
                    `;
                }


                // This is refund
                if (btn == 'cancel') {
                    let max = 'max-w-35';
                    let wnormal = 'w-80';
                    if (!btns.includes('edit')) {
                        max = 'max-w-100';
                        wnormal = 'w-100';
                    }
                    // Please swap between cancel order and refund order so their functions align else you can continue with this confusing method
                    // Because this needed to be in production as soon as possible and cancel worked like a refund, so we swapped names and left it,
                    // after swapping names (if you decide that approach) please change the order of buttons in pos/kitchen-index and pos/ready-index
                    buttonsHtml += `
                        <button ${disabled} class="px-1 py-2 coming-soon refund-order col btn text-danger btn-primary btn-secondary-cus d-flex align-items-center gap-3 justify-content-center ${wnormal} ${max}">
                            ${window.keys.cancelOrder}
                        </button>
                    `;
                }

                // This is cancel
                if (btn == 'refund') {
                    let tempName = window.keys.refundOrder;

                    if (btn_parts.length > 2) {
                        if (btn_parts[2] == 'fake') {
                            tempName = window.keys.cancelOrder;
                        }
                    }
                    btn_parts
                    let max = 'max-w-35';
                    let wnormal = 'w-80';
                    if (!btns.includes('edit')) {
                        max = 'max-w-100';
                        wnormal = 'w-100';
                    }
                    buttonsHtml += `
                        <button ${disabled} class="px-1 py-2 cancel-order col btn text-danger btn-primary btn-secondary-cus d-flex align-items-center gap-3 justify-content-center ${wnormal} ${max}">
                            ${tempName}
                            <!-- ${window.keys.refundOrder} -->
                        </button>
                    `;
                }

                if (btn == 'ack') {
                    let ackready = order.que_ready ? 'd-none' : '';
                    let ackbtn = order.que_ready ? '' : 'd-none';

                    printButton += `
                        <button ${disabled} class="${ackready} ack-ready-button px-5 py-3 col btn btn-primary no-hover d-flex align-items-center justify-content-center w-100">
                            ${window.keys.readybtn}
                        </button>
                    `;
                    printButton += `
                        <button ${disabled} class="${ackbtn} ack-button px-5 py-3 col btn btn-primary no-hover d-flex align-items-center justify-content-center w-100">
                            ${window.keys.acknowledge}
                        </button>
                    `;
                }

                if (btn == 'payment') {
                    buttonsHtml += `
                        <a ${disabled} href="${window.routes.pos_index}?order_id=${order.id}" class="px-5 py-3 col btn btn-primary no-hover d-flex align-items-center justify-content-center w-100">
                            ${window.keys.payment}
                        </a>
                    `;
                }
            }
        }

        let foodItemsL = '';

        let orderItems = order.items;
        // Start from 0 but for of loop shhhh better

        let i = -1;
        for (let orderItem of orderItems) {
            i++;

            let nextItem = null;
            let lastItem = null;
            if (i + 1 < orderItems.length) nextItem = orderItems[i+1];
            if (i - 1 >= 0) lastItem = orderItems[i-1];

            const isProdAfterMeal = (!orderItem.isMeal && lastItem != null && lastItem.isMeal) ? 'prod-after-meal' : '';
            const border = orderItem.mealName || (orderItem.isMeal && nextItem != null && !nextItem.isMeal) ? 'no-border' : '';
            const isMeal =  orderItem['isMeal'] ? 'meal' : '';

            const crossed = (orderItem.checked) || ('meal' in orderItem && orderItem.meal.checked) ? 'crossed' : '';
            const together = 'meal' in orderItem ? orderItem.meal.id : orderItem.id;

            let checkItem = '';
            if (!orderItem.isMeal) {
                let checked = '';
                if (orderItem.checked) checked = 'checked';
                checkItem = `
                    <div class="form-check form-check-sm form-check-custom form-check-solid align-items-start">
                        <input together="${together}" ${checked} class="p-4 form-check-input item-completable" type="checkbox" item-id="${orderItem.id}" order-id="${order.id}" key="${orderItem.randomKey}">
                    </div>
                `;
            }


            let addedExtras = '';
            if ('extra' in orderItem && orderItem.extra.length != 0) {
                addedExtras = `
                    <div class="extra">
                        <div class="title crossable fw-bold">${window.keys.extraAdds}:</div>
                        <div class="extra-items mb-2">
                `;

                for (let extra of orderItem.extra)
                    addedExtras += `<div class="extra-item crossable ps-3">${extra.quantity}x ${extra.name}</div>`

                addedExtras += '</div></div>'
            }

            let removedExtras = '';
            if ('removed' in orderItem && orderItem.removed.length != 0) {
                removedExtras = `
                    <div class="no">
                        <div class="title crossable fw-bold">${window.keys.extraRemoved}:</div>
                        <div class="no-items">
                `;

                for (let remove of orderItem.removed)
                    removedExtras += `<div class="extra-item crossable ps-3">${remove.name}</div>`;

                removedExtras += '</div></div>'
            }

            let extras = `
                ${addedExtras}
                ${removedExtras}
            `;


            let printIcon = '';

            if (orderItem.isMeal || !orderItem.mealName) {
                /*
                printIcon = `
                    <div class="coming-soon ps-5 pb-5">
                        <svg class="primary-children" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                          <g id="print" transform="translate(-4.5 -4.5)">
                            <path id="Path_13" data-name="Path 13" d="M21.808,10.5H7.288A2.788,2.788,0,0,0,4.5,13.288v7.6a2.692,2.692,0,0,0,2.692,2.692h.385v1.908a1.938,1.938,0,0,0,1.938,1.938h9.969a1.938,1.938,0,0,0,1.938-1.938V23.577h.385A2.692,2.692,0,0,0,24.5,20.885V13.192A2.692,2.692,0,0,0,21.808,10.5ZM19.885,25.485a.4.4,0,0,1-.4.4H9.515a.4.4,0,0,1-.4-.4V17.823a.4.4,0,0,1,.4-.4h9.969a.4.4,0,0,1,.4.4Z" transform="translate(0 -2.923)" fill="#5d4bdf"/>
                            <path id="Path_14" data-name="Path 14" d="M21.68,4.5H13.219a2.7,2.7,0,0,0-2.664,2.308h13.79A2.7,2.7,0,0,0,21.68,4.5Z" transform="translate(-2.95 0)" fill="#5d4bdf"/>
                          </g>
                        </svg>
                    </div>
                `;
                */
            }

            foodItemsL += `
                <div class="d-flex ${isProdAfterMeal} ${border} justify-content-between ${isMeal} food-item ${crossed}" order="${order.id}" together="${together}" item="${orderItem.id}" randomKey="${orderItem.randomKey}">
                    <div class="left d-flex gap-4">
                        ${checkItem}

                        <div class="">
                            <div class="item">
                                <span class="crossable title fw-bold fs-4">${orderItem.quantity}x ${orderItem.name}</span>
                            </div>
                            ${extras}
                        </div>
                    </div>

                    <div class="right">
                        ${printIcon}
                    </div>
                </div>
            `;
        }

        orderHolder.innerHTML = `
            <div class="card ms-3 p-0">
                <div class="d-flex justify-content-between px-3 align-items-center py-1 border1 status-color rounded-top" style="background-color: ${order.status_color}">

                    <h3 class="card-title searchable-text fw-bold m-0 text-white">#${order.view_id}</h3>
                    <h3 class="card-title searchable-text fw-bold m-0 status-text text-white">${order.status}</h3>
                    <div class="card-toolbar">
                        <span class="searchable-text text-white">${order.view_price}</span>
                    </div>

                </div>
                <div class="card-body p-0 rounded-bottom">
                    <div class="px-5 bg-body position-relative pt-4">
                        <div class="row">
                            <div class="col">
                                <div class="fs-5 fw-bold searchable-text">${order.table}</div>
                                <div class="fs-6 fw-medium searchable-text">${window.keys.assignment}: <span class="chef-assign">${order.chef}</span></div>
                            </div>
                            <div class="col text-end">
                                <div class="fs-5 text-danger fw-bold time-lapse">0s</div>
                                <div class="fs-6 date-stamp fw-medium" stamp="${order.time}">${window.keys.ordered}: ${order.view_date}</div>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 bg-body position-relative pt-4">
                        <div class="row">
                            <div class="col">
                                <div class="fs-6 fw-medium"><span class="completed-items">${order.completed}</span>/<span items="${order.count}" class="order-count">${order.count}</span> ${window.keys.com_orders}</div>
                            </div>
                            <div class="col text-end">
                                <div class="fs-6 fw-medium"><span class="progress-percentage">${order.progress}</span>%</div>

                            </div>
                        </div>
                    </div>
                    <div class="bg-body pb-4 px-5">
                        <div class="progress h-5px w-100">
                            <div class="progress-bar bg-success" role="progressbar" style="width: ${order.progress}%" aria-valuenow="${order.progress}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="px-5 bg-body d-flex gap-2">
                        ${buttonsHtml}
                    </div>

                    <div class="px-5 bg-body d-flex gap-2 pt-2">
                        ${printButton}
                    </div>

                    <div class="card-bottom-bit bg-body pt-4 px-5 overflow-auto rounded-bottom">
                        <div class="food-items mb-4">
                            ${foodItemsL}
                        </div>

                        ${note}
                    </div>

                </div>
            </div>
        </div>
        `;

        ordering.appendChild(orderHolder);
        orderPagMan.addPag(orderHolder);
    }
}

$(document).on('click', '.item-completable', function() {
    const item = this.getAttribute('item-id');
    const order = this.getAttribute('order-id');
    const randomKey = this.getAttribute('key');
    const crossId = this.getAttribute('together');
    const check = this;

    if (!item || !order || !randomKey) return;

    let added = "true";
    if (!check.checked)
        added = "false";

    let type = check.checked ? 'add' : 'remove';
    let op = check.checked ? 'remove' : 'add';

    let placeAll = document.querySelectorAll(`[order="${order}"][together="${crossId}"][randomKey="${randomKey}"]`);
    for (let place of placeAll) place.classList[type]('crossed');

    updateProgress(order);
    $.ajax({
        url: '/admin/prepare/item',
        method: 'POST',
        async: true,
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            order_id: order,
            item_id: item,
            added: added,
            randomKey: randomKey,
        },
        success: data => {
            if (data.status == 2) {
                location.href = data.uri;
                return;
            }
            if (data.status != 0) {
                check.checked = !added;
                let placeAll = document.querySelectorAll(`[order="${order}"][together="${crossId}"][randomKey="${randomKey}"]`);
                for (let place of placeAll) place.classList[type]('crossed');

                // Show error with data.message
                if ('message' in data && data.message != '') {
                    Swal.fire({
                        text: data.message,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: window.keys.confirmButtonOk,
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
                return;
            }

            const percentile = updateProgress(order);
            if (percentile == 100) swapStatus(order, 2)
            else swapStatus(order, 1);

            if ('message' in data && data.message != '') {
                Swal.fire({
                    text: data.message,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: window.keys.confirmButtonOk,
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                })
            }
        },
        error: err => {
            check.checked = !added;
            let placeAll = document.querySelectorAll(`[order="${order}"][together="${crossId}"][randomKey="${randomKey}"]`);
            for (let place of placeAll) place.classList[type]('crossed');
            console.error(err);

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

})

$(document).on('click', '.order-assign', function() {
    const order_id = this.getAttribute('order-id');
    if (!order_id) return;

    $.ajax({
        url: '/admin/assignToMe',
        method: 'POST',
        async: true,
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            order_id: order_id,
        },
        success: data => {
            if (data.status == 2) {
                location.href = data.uri;
                return;
            }
            if (data.status != 0) {
                // Show error with data.message
                Swal.fire({
                    text: data.message,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: window.keys.confirmButtonOk,
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
                return;
            }

            this.textContent = window.keys.confirmBtn;
            this.classList.remove('order-assign');
            this.classList.add('order-confirm');

            swapStatus(order_id, 1);
            assignUser(order_id, { name: document.getElementById('current-user').getAttribute('user') });

            // location.reload();
                // Change card status
                // These functions are unfinished because the style needs to be accepted before it moves to JS fully for refresh
        },
        error: err => {
            console.error(err);
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

})

$(document).on('click', '.order-confirm', function() {
    const order_id = this.getAttribute('order-id');

    $.ajax({
        url: '/admin/confirmOrder',
        method: 'POST',
        async: true,
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            order_id: order_id,
        },
        success: data => {
            if (data.status == 2) {
                location.href = data.uri;
                return;
            }
            if (data.status != 0) {
                // Show error with data.message
                Swal.fire({
                    text: data.message,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: window.keys.confirmButtonOk,
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
                return;
            }

            const orderCard = document.querySelector(`.order-card[order="${order_id}"]`);
            if (!orderCard) return;
            orderCard.remove();

        },
        error: err => {
            console.error(err);
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
})


window.cancelation = {
    current: null,
    orders: [],
    reasons: {},
    keySwaps: {},
    update: function() {
        if (this.current != null) return false;
        if (this.orders.length == 0) return false;

        this.current = this.orders.shift();
        return this.current;
    },
    acknowledge: function() {
        let orderId = this.current;
        $.ajax({
            method: 'POST',
            url: '/admin/approveCancelKitchen',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {order_id: orderId},
            success: resp => {
                if (resp.status == 2) {
                    location.href = resp.uri;
                    return;
                }

                if (resp.status == 1) {
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
                    this.current = null;
                    $('#ack-modal').modal('hide');
                }

                if (resp.message != '') {
                    Swal.fire({
                        text: data.message,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: window.keys.confirmButtonOk,
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    })
                }
            },
            error: err => {
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
    },
    getId: function(orderId) {
        if (orderId in this.keySwaps) return this.keySwaps[orderId];
        return '';
    },
    getReason: function(orderId) {
        if (orderId in this.reasons) return this.reasons[orderId];
        return '';
    }
}

$(document).on('click', '.acknowledge-current-order', function() {
    window.cancelation.acknowledge();
})

$(document).on('click', '.coming-soon', function() {
    Swal.fire({
        text: window.keys.comingSoon,
        icon: "error",
        buttonsStyling: false,
        confirmButtonText: window.keys.confirmButtonOk,
        customClass: {
            confirmButton: "btn fw-bold btn-primary",
        }
    });
})

$(document).ready(() => {
    window.orderPagMan.setup();
    setInterval(() => {
        if (window.cancelation.orders.length != 0 || window.cancelation.current != null)
            $('#ack-modal').modal('show');

        let orderId = window.cancelation.update();
        if (orderId == false) return;

        $('#ack-modal .order-id').text(window.cancelation.getId(orderId));
        $('#ack-modal .order-reason').text(window.cancelation.getReason(orderId));
        $('#ack-modal').modal('show');
    }, 50);
});

function swapStatus(order, status) {
    const statuses = [window.keys.waitingOrder, window.keys.progressOrder, window.keys.completedOrder];
    const statusColours = ['#E7B951', '#FF3636', '#29B93A'];
    const orderCard = document.querySelector(`.order-card[order="${order}"]`);
    if (!orderCard) return false;

    // Remove all statuses from classes
    for (let status of statuses) {
        let statusName = status.replaceAll(' ', '_');
        orderCard.classList.remove(statusName);
    }

    if (status in statuses) {
        let currentStatus = statuses[status];
        const currentStatusName = currentStatus.replaceAll(' ', '_');
        orderCard.classList.add(currentStatusName);

        const statusColorEl = orderCard.querySelector('.status-color');
        if (statusColorEl) statusColorEl.style.backgroundColor = statusColours[status];

        const statusText = orderCard.querySelector('.status-text');
        if (statusText) statusText.textContent = currentStatus;
        return true;
    }

    return false;
}

function assignUser(order, user) {
    const orderCard = document.querySelector(`.order-card[order="${order}"]`);
    if (!orderCard) return false;

    const chefEl = orderCard.querySelector('.chef-assign');
    if (chefEl && 'name' in user) chefEl.textContent = user.name;

    return true;
}

function updateProgress(order) {
    const orderCard = document.querySelector(`.order-card[order="${order}"]`);
    if (!orderCard) return 0;

    const items = orderCard.querySelectorAll('input[item-id]');
    const len = items.length;

    let checkedItems = 0;
    items.forEach(item => {
        if (item.checked) {
            checkedItems++;
        }
    })

    const percentage = ((checkedItems / len) * 100).toFixed(2);

    const bar = orderCard.querySelector('.progress-bar');
    if (bar) bar.style.width = `${percentage}%`;

    const comItems = orderCard.querySelector('.completed-items');
    if (comItems) comItems.textContent = checkedItems;


    const percentageEl = orderCard.querySelector('.progress-percentage');
    if (percentageEl) percentageEl.textContent = percentage;
    return percentage;
}

const second = 1;
const minute = second * 60;
const hour = minute * 60;

function fixTimers() {
    // now in seconds
    const now = (Date.now() / 1000);

    const orderCards = document.querySelectorAll('.order-card[order]');
    for (let orderCard of orderCards) {
        const timeStamp = orderCard.querySelector('.date-stamp');
        if (!timeStamp) return;

        const timeLapse = orderCard.querySelector('.time-lapse');
        if (!timeLapse) return;

        const stamp = timeStamp.getAttribute('stamp');
        const ellapsed = now - parseFloat(stamp);


        const hours = Math.floor(ellapsed / (hour));
        const minutes = Math.floor((ellapsed % (hour)) / (minute));
        const seconds = Math.floor((ellapsed % (minute)) / second);

        const strings = [];

        if (hours != 0) strings.push(`${hours}h`);
        if (minutes != 0 || hours != 0) strings.push(`${minutes}m`);
        strings.push(`${seconds}s`);

        let string = strings.join(' ');

        timeLapse.textContent = string;
    }
}

setInterval(() => {
    fixTimers();
}, 1000);

function getOrders() {
    const orderingType = document.getElementById("ordering");
    let url = '/admin/pos/kitchen/orders';
    if (orderingType && orderingType.getAttribute('type') == 'ready')  url = '/admin/pos/readyOrders';
    if (orderingType && orderingType.getAttribute('type') == 'e-kiosk') url = '/admin/pos/eKioskOrders';

    let finishedAutos = [];
    const orderIds = Object.keys(autoPrinted);
    if (orderIds.length > 0) {
        let index = 1;
        for (let orderId of orderIds) {

            const finishedString = 'uuid' + index + "=" + orderId;

            finishedAutos.push(finishedString);
            index++;
        }
    }



    $.ajax({
        url: url + '?' + finishedAutos.join('&'),
        method: 'GET',
        async: true,
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: resp => {
            if (resp.status == 2) {
                location.href = resp.uri;
                return;
            }

            if (resp.status != 0) {
                // Show error with resp.message
                if ('message' in resp && resp.message != '') {
                    Swal.fire({
                        text: resp.message,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: window.keys.confirmButtonOk,
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
                return;
            }

            orderMan.clearOrders();

            let canceled_orders = resp.data.canceled_orders;
            for (let order of canceled_orders) {
                window.cancelation.orders.push(order.id);
                window.cancelation.reasons[order.id] = order.cancellation_reason;
                window.cancelation.keySwaps[order.id] = order.order_number;
            }

            autoPrinted = {};
            localStorage.removeItem('auto_print_orders');
            if ('auto_print_orders' in resp.data) {
                for (let order of resp.data.auto_print_orders) {
                    if (!(order.id in processingAutoPrint)) {
                        processingAutoPrint[order.id] = order;
                        instaPrintReceipt(order.printString, 'auto-' + order.id);
                    }
                }
            }

            orderPagMan.clearPags();
            let orders = resp.data.orders;

            const popUp = document.getElementsByClassName('no-orders-pop');
            if (orders.length == 0) {
                for (let pop of popUp)
                    pop.classList.remove('d-none');
            } else {
                for (let pop of popUp)
                    pop.classList.add('d-none');
            }

            for (let order of orders) {
                orderMan.addOrder(order);
            }
            fixTimers();

            const ordersEls = document.querySelectorAll('.order-card[order]');
            for (let order of ordersEls) {
                const id = order.getAttribute('order');
                updateProgress(id);
            }


            // We literally have no time to make this prettier so please for the love of everything you have shut yow MOUF xd love you though try not to be too confused
            window.orderPagMan.orderReset = function() {
                orderMan.clearOrders();

                let canceled_orders = resp.data.canceled_orders;
                for (let order of canceled_orders) {
                    window.cancelation.orders.push(order.id);
                    window.cancelation.reasons[order.id] = order.cancellation_reason;
                    window.cancelation.keySwaps[order.id] = order.order_number;
                }

                orderPagMan.clearPags();
                let orders = resp.data.orders;
                const popUp = document.getElementsByClassName('no-orders-pop');
                if (orders.length == 0) {
                    for (let pop of popUp)
                        pop.classList.remove('d-none');
                } else {
                    for (let pop of popUp)
                        pop.classList.add('d-none');
                }

                for (let order of orders) {
                    orderMan.addOrder(order);
                }
                fixTimers();

                const ordersEls = document.querySelectorAll('.order-card[order]');
                for (let order of ordersEls) {
                    const id = order.getAttribute('order');
                    updateProgress(id);
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
        error: err => {
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
    });
}



window.debug_print = false;

if (window.debug_print) {
    document.addEventListener('click', e => {
        console.log(e.button);
        if (e.button == 0) {
            getOrders();
        }
    })

    document.addEventListener('contextmenu', e => {
        e.preventDefault();
        e.stopPropagation();
        console.log(printingObj);
        return false;
    })
}

if (!window.debug_print) {
    getOrders();
    setInterval(() => {
        getOrders();
    }, 10000);
}


document.addEventListener('DOMContentLoaded', () => {
    if (window.debug_print) {
        window.Mine = {
            postMessage: stuff => alert(stuff)
        }
    }

    let printerSettings = localStorage.getItem('printer_settings');
    if (printerSettings != null) {
        try {
            printerSettings = JSON.parse(printerSettings);

            if ('receipt_printer' in printerSettings && printerSettings.receipt_printer && printerSettings.receipt_printer.status == 1) {
                if ('Mine' in window) {
                    // Default port would be 9100 please pay attention 192.168.178.167
                    const type = printerSettings.receipt_printer.printer.type;
                    let prefix = 'order';
                    if (type == 'sticker_printer') prefix = 'sticker';
                    window.Mine.postMessage(`con:${prefix}-${printerSettings.receipt_printer.printer.name}:${printerSettings.receipt_printer.printer.ip}:${printerSettings.receipt_printer.printer.port}`);
                }
            } else {
                // No printer or the printer status is 0
            }
        } catch(err) {
            // No printer connected
        }
    } else {
        // No printer selected (no messages here until instructed otherwise)
    }


    const orders = document.querySelectorAll('.order-card[order]');
    for (let order of orders) {
        const id = order.getAttribute('order');
        updateProgress(id);
    }
})

$(document).on('click', '.print-order', function() {
    const orderCard = this.closest('[order]');
    if (orderCard) {
        const orderId = orderCard.getAttribute('order');

        $.ajax({
            method: 'GET',
            url: '/admin/pos/printPosOrder?order_id=' + orderId,
            dataType: 'json',
            success: resp => {
                if (resp.status == 2) {
                    location.href = resp.uri;
                    return;
                }

                if (resp.status == 1) {
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

                const data = resp.data;
                if (!('string' in data)) {
                    Swal.fire({
                        text: window.keys.printerOrderDoesNotExist,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: window.keys.confirmButtonOk,
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                    return;
                }

                instaPrintReceipt(data.string, orderId);
                if (resp.message != '') {
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
            error: err => {
                console.error(err);
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

    }
})

function connected(nonce) {
    printingObj.printing = false;
    printingObj.order = null;
    Swal.fire({
        text: nonce + " " + window.keys.connected,
        icon: "error",
        buttonsStyling: false,
        confirmButtonText: window.keys.confirmButtonOk,
        customClass: {
            confirmButton: "btn fw-bold btn-primary",
        }
    });
}

window.failed_connection = (nonce, reason, status) => {
    printingObj.printing = false;
    if (reason == "Succeed") {
        if (window.debug_print) alert(nonce + ' printed');

        const orderPrinted = printingObj.order;
        if (orderPrinted != null) {
            // Continue as you know which order was printed
            if (printingObj.order != null && printingObj.order.startsWith('auto-')) {
                const orderId = printingObj.order.split('auto-')[1];
                autoPrinted[orderId] = orderId;

                localStorage.setItem('auto_print_orders', JSON.stringify(autoPrinted));
            }
        }
        return;
    }

    Swal.fire({
        text: nonce + " " + window.keys.connectionFailReason + ": " + reason,
        icon: "error",
        buttonsStyling: false,
        confirmButtonText: window.keys.confirmButtonOk,
        customClass: {
            confirmButton: "btn fw-bold btn-primary",
        }
    });
}


setInterval(() => {
    // Precheck incase something goes wrong
    if ('Mine' in window) {
        if (window.queuedStrings.receipt.length != 0 && window.queueInterval == null)
            startQueue();
    }
}, 1000);

let lastPrint = Date.now();
function instaPrintReceipt(printString, nonce = null) {
    if (Date.now() - lastPrint > 3000 && window.queuedStrings.receipt.length == 0) {
        if (printingObj.printing) {
            window.queuedStrings.receipt.push({string: printString, nonce: nonce});
            startQueue();
            return;
        }

        lastPrint = Date.now();

        printingObj.printing = true;
        printingObj.order = nonce;
        invoicePrinting(printString);
        return;
    }

    window.queuedStrings.receipt.push({string: printString, nonce: nonce});
    if (window.queueInterval == null)
        startQueue();
}

function startQueue() {
    if (window.queueInterval != null) clearInterval(window.queueInterval);

    window.queueInterval = setInterval(() => {
        if (printingObj.printing) return;

        if (window.queuedStrings.receipt.length == 0) {
            if (window.queueInterval != null) clearInterval(window.queueInterval);

            window.queueInterval = null;
            return;
        }

        let printString = '';
        if (window.debug_print) alert(window.currentPrinting);

        let printObj = window.queuedStrings.receipt.shift();
        printString = printObj.string;

        if (!printString || printString == '') return;
        if (window.debug_print) alert('Printing ' + window.currentPrinting + " "+ printString);

        printingObj.printing = true;
        printingObj.order = printObj.nonce;
        invoicePrinting(printString);

        if (window.queuedStrings.receipt.length == 0) {
            if (window.queueInterval != null) clearInterval(window.queueInterval);

            window.queueInterval = null;
            return;
        }
    }, 3000)
}
