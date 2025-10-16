window.queueInterval = null;
window.lastPrint = Date.now();

// window.printerPreNonce = 'sticker-';

window.printerPreNonce = 'order-';
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
                window.orderPagMan.clickPag(page);
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

        const pageElement = window.orderPagMan.orderElements[this.current];
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
        let noSpaceStatus = order.status.replaceAll(' ', '_');

        const orderHolder = document.createElement('div');
        orderHolder.classList.add('order-card');
        orderHolder.classList.add('holder');
        orderHolder.classList.add('w-fit-content');
        orderHolder.classList.add('mt-3');
        orderHolder.classList.add('flex-shrink-0');
        orderHolder.classList.add(noSpaceStatus);

        orderHolder.setAttribute('order', order.id);

        let note = '';

        if (order.note != '') {
            note = `
                <div class="Note mb-4 p-4">
                    <div class="title fw-bold">${window.keys.note}:</div>
                    <div class="note">${order.note}</div>
                </div>
            `;
        }

        let assignmentConfirmButton = `
            <button order-id="${order.id}" class="order-confirm col btn btn-primary d-flex align-items-center justify-content-center w-100">
                ${window.keys.confirmBtn}
            </button>
        `;

        if(order.assignment == 1) {
            assignmentConfirmButton = `
                <button order-id="${order.id}" class="order-assign col btn btn-primary no-hover d-flex align-items-center justify-content-center w-100">
                    ${window.keys.assignmentMe}
                </button>
            `;
        }

        let foodItemsL = '';

        let orderItems = order.items;

        // If you need explanation for this loop go to where show_kitchen last was xD
        if (orderItems.length > 0) {
            for (let i = orderItems.length - 1; i >= 0; i--) {
                const item = orderItems[i];
                if ('show_kitchen' in item && item.show_kitchen == false) {
                    orderItems.splice(i, 1);
                }
            }
        }

        // Start from 0 but for of loop shhhh better
        let i = -1;
        for (let orderItem of orderItems) {
            // We could have done it all here and then every time next or last item had this continue to next one til you find
            // which would will be more efficient because it's not 2 different loops it would be "sometimes double movement"
            // But this ain't clean bruh... so the above loop is used because of that
            // if ('show_kitchen' in orderItem && orderItem.show_kitchen == false) continue;
            // if (!orderItem.show_kitchen) continue;
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
            // if (!orderItem.isMeal) {
            if (!orderItem.mealName) {
                let checked = '';
                if (orderItem.checked) checked = 'checked';
                checkItem = `
                    <div class="form-check form-check-sm form-check-custom form-check-solid align-items-start">
                        <input together="${together}" ${checked} class="p-4 form-check-input item-completable" type="checkbox" item-id="${orderItem.id}" order-id="${order.id}" child-key="${orderItem.childRandomKey}" key="${orderItem.randomKey}">
                    </div>
                `;
            }
            // }


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
                printIcon = `
                    <div childRandomKey="${orderItem.childRandomKey}" randomKey="${orderItem.randomKey}" order="${order.id}" class="print-order-item ps-5">
                        <svg class="primary-children" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                          <g id="print" transform="translate(-4.5 -4.5)">
                            <path id="Path_13" data-name="Path 13" d="M21.808,10.5H7.288A2.788,2.788,0,0,0,4.5,13.288v7.6a2.692,2.692,0,0,0,2.692,2.692h.385v1.908a1.938,1.938,0,0,0,1.938,1.938h9.969a1.938,1.938,0,0,0,1.938-1.938V23.577h.385A2.692,2.692,0,0,0,24.5,20.885V13.192A2.692,2.692,0,0,0,21.808,10.5ZM19.885,25.485a.4.4,0,0,1-.4.4H9.515a.4.4,0,0,1-.4-.4V17.823a.4.4,0,0,1,.4-.4h9.969a.4.4,0,0,1,.4.4Z" transform="translate(0 -2.923)" fill="#5d4bdf"/>
                            <path id="Path_14" data-name="Path 14" d="M21.68,4.5H13.219a2.7,2.7,0,0,0-2.664,2.308h13.79A2.7,2.7,0,0,0,21.68,4.5Z" transform="translate(-2.95 0)" fill="#5d4bdf"/>
                          </g>
                        </svg>
                    </div>
                `;
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

                    <div class="px-5 bg-body d-flex gap-2 pb-4">
                        ${assignmentConfirmButton}
                        <!--
                        <button order="${order.id}" class="print-order col btn btn-primary btn-secondary-cus d-flex align-items-center gap-3 justify-content-center w-80 max-w-35" disabled>
                            ${window.keys.printOrder}
                        </button>
                        -->
                    </div>

                    <div class="card-bottom-bit bg-body px-5 overflow-auto rounded-bottom">
                        <div class="food-items mb-4">
                            ${foodItemsL}
                        </div>

                        ${note}
                    </div>

                </div>
            </div>
        </div>
        `;

        const ordering = document.getElementById('ordering');
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

    let data = {
        order_id: order,
        item_id: item,
        added: added,
        randomKey: randomKey,
    }

    const childKey = this.getAttribute('child-key');
    if (childKey != null && childKey != 0) {
        data['childRandomKey'] = childKey;
    }

    $.ajax({
        url: '/admin/prepare/item',
        method: 'POST',
        async: true,
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: data,
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

$(document).on('click', '.print-order', function() {
    // Disabled order printing for the time being
    return;
    const orderId = this.getAttribute('order');

    $.ajax({
        method: 'GET',
        url: '/admin/printKitchenOrder?order_id=' + orderId,
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

            if (!('print_order' in resp)) {
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

            printInvoiceType(resp.print_order, 'order');
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

})

$(document).on('click', '.print-order-item', function() {
    const orderId = this.getAttribute('order');
    const randomKey = this.getAttribute('randomKey');

    if (!orderId || !randomKey) return;

    const data = {
        'order_id': orderId,
        'random_key': randomKey,
    }

    const childKey = this.getAttribute('childRandomKey');
    if (childKey != null && childKey != 0) {
        data['child_random_key'] = childKey;
    }

    const queryParams = [];
    for (let item in data) {
        queryParams.push(`${item}=${data[item]}`);
    }

    $.ajax({
        method: 'GET',
        // url: '/admin/printKitchenItem?order_id=' + orderId + '&random_key=' + randomKey,
        url: '/admin/printKitchenItem?' + queryParams.join('&'),
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

            if (!('print_order' in resp)) {
                Swal.fire({
                    text: "Print order does not exist, alert development team as this error should never appear",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: window.keys.confirmButtonOk,
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
                return;
            }

            printInvoiceType(resp.print_order, 'sticker');
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
    $.ajax({
        url: '/admin/kitchen/orders',
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

window.updating_orders = true;
getOrders();
setInterval(() => {
    if (window.updating_orders)
        getOrders();
}, 10000);


document.addEventListener('DOMContentLoaded', () => {
    const orders = document.querySelectorAll('.order-card[order]');
    for (let order of orders) {
        const id = order.getAttribute('order');
        updateProgress(id);
    }


        let printerSettings = localStorage.getItem('printer_settings');
        if (printerSettings != null) {
            try {
                printerSettings = JSON.parse(printerSettings);

                if ('receipt_printer' in printerSettings && printerSettings.receipt_printer && printerSettings.receipt_printer.status == 1) {
                    if ('Mine' in window) {
                        // Default port would be 9100 please pay attention 192.168.178.167
                        window.Mine.postMessage(`con:${window.printerPreNonce}${printerSettings.receipt_printer.printer.name}:${printerSettings.receipt_printer.printer.ip}:${printerSettings.receipt_printer.printer.port}`);
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

});


window.queuedStrings = {
    sticker: [],
    order: []
};
window.types = {
    sticker: [],
    order: []
};

function printInvoiceType(string, type) {
    let printerSettings = localStorage.getItem('printer_settings');
    if (printerSettings != null) {
        try {
            printerSettings = JSON.parse(printerSettings);

            if (type == 'sticker') {
                if ('sticker_printer' in printerSettings && printerSettings.sticker_printer && printerSettings.sticker_printer.status == 1) {
                    if ('Mine' in window) {
                        instaPrintSticker(string);

                        // Old logic is here, the update is: "No total printing simply sticker printing" if you need to get everything back remove everything above and uncomment everything below this line

                        /*
                        // Default port would be 9100 please pay attention 192.168.178.167
                        window.printerPreNonce = 'sticker-';
                        window.Mine.postMessage(`con:${window.printerPreNonce}${printerSettings.sticker_printer.printer.name}:${printerSettings.sticker_printer.printer.ip}:${printerSettings.sticker_printer.printer.port}`);
                        // window.queuedStrings.sticker.push(string);
                        window.types.sticker.push(window.printerPreNonce + printerSettings.sticker_printer.printer.name);
                        window.printerPreNonce = 'order-';
                        */
                    } else {
                        console.log(`con:${window.printerPreNonce}${printerSettings.sticker_printer.printer.name}:${printerSettings.sticker_printer.printer.ip}:${printerSettings.sticker_printer.printer.port}`);
                        console.log(window.printerPreNonce + printerSettings.sticker_printer.printer.name);
                        console.log(string);
                        window.queuedStrings.sticker.push(string);
                        window.currentPrinting = 1;
                        // invoicePrinting(string);
                    }
                } else {
                    // No printer or the printer status is 0
                }
            }

            if (type == 'order') {
                if ('order_printer' in printerSettings && printerSettings.order_printer && printerSettings.order_printer.status == 1) {
                    if ('Mine' in window) {
                        // Default port would be 9100 please pay attention 192.168.178.167
                        window.Mine.postMessage(`con:${window.printerPreNonce}${printerSettings.order_printer.printer.name}:${printerSettings.order_printer.printer.ip}:${printerSettings.order_printer.printer.port}`);
                        window.queuedStrings.order.push(string);
                        window.types.order.push(window.printerPreNonce + printerSettings.order_printer.printer.name);
                    } else {
                        console.log(`con:${window.printerPreNonce}${printerSettings.order_printer.printer.name}:${printerSettings.order_printer.printer.ip}:${printerSettings.order_printer.printer.port}`);
                        console.log(window.printerPreNonce + printerSettings.sticker_printer.printer.name);
                        console.log(string);
                        window.queuedStrings.order.push(string);
                        window.currentPrinting = 2;
                        // invoicePrinting(string);
                    }
                } else {
                    // No printer or the printer status is 0
                }
            }

        } catch(err) {
            // No printer connected
        }
    } else {
        // No printer selected (no messages here until instructed otherwise)
    }
}

window.currentPrinting = 1;
function connected(nonce) {
    Swal.fire({
        text: nonce + " " + window.keys.connected,
        icon: "error",
        buttonsStyling: false,
        confirmButtonText: window.keys.confirmButtonOk,
        customClass: {
            confirmButton: "btn fw-bold btn-primary",
        }
    });
    if (nonce in window.types.sticker) {
        window.currentPrinting = 1;
    }

    if (nonce in window.types.order) {
        window.currentPrinting = 2;
    }
}

window.debug_print = false;
window.failed_connection = (nonce, reason, status) => {
    if (reason == "Succeed") {
        if (window.debug_print) alert(nonce + ' printed');
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

function changePrinter(type) {
    if (window.debug_print) alert(type);
    let printerSettings = localStorage.getItem('printer_settings');
    if (printerSettings != null) {
        try {
            printerSettings = JSON.parse(printerSettings);

            if (type == 'sticker') {
                if ('sticker_printer' in printerSettings && printerSettings.sticker_printer && printerSettings.sticker_printer.status == 1) {
                    if ('Mine' in window) {
                        // window.printerPreNonce = 'sticker-';
                        window.printerPreNonce = 'order-';
                        window.currentPrinting = 1;

                        window.Mine.postMessage(`con:${window.printerPreNonce}${printerSettings.sticker_printer.printer.name}:${printerSettings.sticker_printer.printer.ip}:${printerSettings.sticker_printer.printer.port}`);
                        // Uncomment all this when going back from sticker only
                        /*
                        window.printerPreNonce = 'sticker-';
                        window.Mine.postMessage(`con:${window.printerPreNonce}${printerSettings.sticker_printer.printer.name}:${printerSettings.sticker_printer.printer.ip}:${printerSettings.sticker_printer.printer.port}`);
                        window.currentPrinting = 1;
                        window.printerPreNonce = 'order-';
                        */
                    }
                } else {
                    // No printer or the printer status is 0
                }
            }

            if (type == 'order') {
                if ('order_printer' in printerSettings && printerSettings.order_printer && printerSettings.order_printer.status == 1) {
                    if ('Mine' in window) {
                        window.Mine.postMessage(`con:${window.printerPreNonce}${printerSettings.order_printer.printer.name}:${printerSettings.order_printer.printer.ip}:${printerSettings.order_printer.printer.port}`);
                        window.currentPrinting = 2;
                    }
                } else {
                }
            }

        } catch(err) {
            // No printer connected
        }
    } else {
    }
}

const logMessages = [];
/*
window.log = message => {
    logMessages.push(message);

    Swal.fire({
        text: logMessages.join(','),
        icon: "error",
        buttonsStyling: false,
        confirmButtonText: window.keys.confirmButtonOk,
        customClass: {
            confirmButton: "btn fw-bold btn-primary",
        }
    });
}
*/

document.addEventListener('DOMContentLoaded', () => {
    changePrinter('sticker');
});

// These two functions from this line are apart of sticker only update, to get everything back just delete or comment these two functions and uncomment stuff below

setInterval(() => {
    // Precheck incase something goes wrong
    if ('Mine' in window) {
        if (window.queuedStrings.sticker.length != 0 && window.queueInterval == null)
            startQueue();
    }
}, 1000);

function instaPrintSticker(printString) {
    if (Date.now() - window.lastPrint > 3000 && window.queuedStrings.sticker.length == 0) {
        window.lastPrint = Date.now();

        // stickerPrinting(76, printString);
        invoicePrinting(printString);
        return;
    }

    window.queuedStrings.sticker.push(printString);
    if (window.queueInterval == null)
        startQueue();
}

function startQueue() {
    if (window.queueInterval != null) clearInterval(window.queueInterval);

    window.queueInterval = setInterval(() => {
        if (window.queuedStrings.sticker.length == 0) {
            if (window.queueInterval != null) clearInterval(window.queueInterval);

            window.queueInterval = null;
            return;
        }

        let printString = '';
        if (window.debug_print) alert(window.currentPrinting);

        printString = window.queuedStrings.sticker.shift();

        if (!printString || printString == '') return;
        if (window.debug_print) alert('Printing ' + window.currentPrinting + " "+ printString);

        // stickerPrinting(80, printString);
        invoicePrinting(printString);

        if (window.queuedStrings.sticker.length == 0) {
            if (window.queueInterval != null) clearInterval(window.queueInterval);

            window.queueInterval = null;
            return;
        }
    }, 3000)
}

/*
setInterval(() => {
    let printString = '';
    if (window.debug_print) alert(window.currentPrinting);

    printString = window.queuedStrings.sticker.shift();

    if (!printString || printString == '') return;
    if (window.debug_print) alert('Printing ' + window.currentPrinting + " "+ printString);

    stickerPrinting(80, printString);
}, 3000);
*/

/*
setInterval(() => {
    let printString = '';
    if (window.debug_print) alert(window.currentPrinting);
    if (window.currentPrinting == 1) {
        if (window.queuedStrings.sticker.length == 0) {
            changePrinter('order');
            return;
        }

        printString = window.queuedStrings.sticker.shift();
    }

    if (window.currentPrinting == 2) {
        if (window.queuedStrings.order.length == 0) {
            changePrinter('sticker');
            return;
        }

        printString = window.queuedStrings.order.shift();
    }

    if (printString == '') return;
    if (window.debug_print) alert('Printing ' + window.currentPrinting + " "+ printString);
    stickerPrinting(80, printString);
}, 7000);
*/

/*
setInterval(() => {
    const rand = parseInt(Math.random() * 80);
    stfickerPrinting(80, `LR:${rand};`);
})
*/
