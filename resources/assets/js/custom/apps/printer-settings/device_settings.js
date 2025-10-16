let device_ids = [];
let reverting = {};
const toVerify = [];

const type = document.getElementById('device-type');

const assignmentConfiguration = {
    url: '/admin/pos/assignDeviceInPos',
    keys: ['device_ids']
}

// Any other configuration for kitchen specific
if (type && type.value == 'kitchen') {
    assignmentConfiguration.url = '/admin/assignDeviceInKitchen';
    assignmentConfiguration.device_kitchen_id = null;
    assignmentConfiguration.keys = ['device_ids', 'device_kitchen_id']

    get_device_id();
}


function get_device_id() {
    if ('Mine' in window) {
        window.Mine.postMessage('device_id:');
    } else {
        Swal.fire({
            text: 'Sent: device_id: (not on apk so you will not get it back) will continue with "test_id" as id)',
            icon: "success",
            buttonsStyling: false,
            confirmButtonText: window.keys.confirmButtonOk,
            customClass: {
                confirmButton: "btn fw-bold btn-primary",
            }
        });

        assignmentConfiguration.device_kitchen_id = 'test_id';
    }
}

function device_id_retrieve(id, message = '') {
    if (id == null) {
        Swal.fire({
            text: message,
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: window.keys.confirmButtonOk,
            customClass: {
                confirmButton: "btn fw-bold btn-primary",
            }
        });
    }

    assignmentConfiguration.device_kitchen_id = id;
}

let settings = {};
// This is specific for settings
if (type && type.value == 'kitchen') {
    settings = {
        sticker_printer: {
            printer: null,
            status: 0
        },
        order_printer: {
            printer: null,
            status: 0
        },
        language: null
    }

    const inputs = document.querySelectorAll('input:not([name="_token"]):not([immune]), select:not([immune])');
    for (let input of inputs) {
        input.setAttribute('disabled', 'disabled');
    }

    let editting = false;
    const editBtns = document.getElementsByClassName('edit-btn');
    for (let eBtn of editBtns) {
        eBtn.addEventListener('click', e => {
            e.preventDefault();
            e.stopPropagation();
            editting = !editting;

            if (!editting) {
                // We are saving
                reverting = settings;
                device_ids = [];

                const printerSelect = document.getElementById('order_printer');
                if (printerSelect && printerSelect.value != '' && printerSelect.value != ' ' && printerSelect.value in window.printers) {
                    settings.order_printer.printer = window.printers[printerSelect.value];
                    let id = settings.order_printer.printer.id;
                    device_ids.push(id);
                    toVerify.push({
                        id: id,
                        ip: settings.order_printer.printer.ip,
                        name: settings.order_printer.printer.name,
                        port: settings.order_printer.printer.port,
                        type: settings.order_printer.type,
                        prenonce: 'order-'
                    });
                } else {
                    settings.order_printer.printer = null;
                }

                const printerStatus = document.getElementById('order_printer_status');
                if (printerStatus && 'checked' in printerStatus && printerStatus.checked) {
                    settings.order_printer.status = 1;
                } else {
                    settings.order_printer.status = 0;
                }


                const paymentSelect = document.getElementById('sticker_printer');
                // if (paymentSelect && paymentSelect.value != '' && paymentSelect.value != ' ' && paymentSelect.value in window.sticker_printers) {
                    // settings.sticker_printer.printer = window.sticker_printers[paymentSelect.value];


                if (paymentSelect && paymentSelect.value != '' && paymentSelect.value != ' ' && paymentSelect.value in window.printers) {
                    settings.sticker_printer.printer = window.printers[paymentSelect.value];
                    let id = settings.sticker_printer.printer.id;
                    device_ids.push(id);

                    toVerify.push({
                        id: id,
                        ip: settings.sticker_printer.printer.ip,
                        name: settings.sticker_printer.printer.name,
                        port: settings.sticker_printer.printer.port,
                        prenonce: 'sticker-'
                    });
                } else {
                    settings.sticker_printer.printer = null;
                }

                const paymentStatus = document.getElementById('sticker_printer_status');
                if (paymentStatus && 'checked' in paymentStatus && paymentStatus.checked) {
                    settings.sticker_printer.status = 1;
                } else {
                    settings.sticker_printer.status = 0;
                }

                localStorage.setItem('printer_settings', JSON.stringify(settings));
                startChecking();
                for (let input of inputs) {
                    input.setAttribute('disabled', 'disabled');
                }
            } else {
                for (let input of inputs) {
                    input.removeAttribute('disabled');
                }
            }
            for (let editBtn of editBtns) {
                let textInBtn = editBtn.querySelector('.text');
                if (textInBtn) textInBtn.textContent = editting ? textInBtn.getAttribute('active') : textInBtn.getAttribute('inactive');
            }
            return false;
        });
    }

} else {
    settings = {
        receipt_printer: {
            printer: null,
            status: 0
        },
        terminal: {
            terminal: null,
            status: 0
        },
        language: null
    }

    const inputs = document.querySelectorAll('input:not([name="_token"]):not([immune]), select:not([immune])');
    for (let input of inputs) {
        input.setAttribute('disabled', 'disabled');
    }

    let editting = false;
    const editBtns = document.getElementsByClassName('edit-btn');
    for (let eBtn of editBtns) {
        eBtn.addEventListener('click', e => {
            e.preventDefault();
            e.stopPropagation();
            editting = !editting;

            if (!editting) {
                // We are saving
                reverting = settings;
                device_ids = [];

                const printerSelect = document.getElementById('receipt_printer');
                if (printerSelect && printerSelect.value != '' && printerSelect.value != ' ' && printerSelect.value in window.printers) {
                    settings.receipt_printer.printer = window.printers[printerSelect.value];
                    let id = settings.receipt_printer.printer.id;
                    device_ids.push(id);

                    toVerify.push({
                        id: id,
                        ip: settings.receipt_printer.printer.ip,
                        name: settings.receipt_printer.printer.name,
                        port: settings.receipt_printer.printer.port,
                        prenonce: 'order-'
                    });
                } else {
                    settings.receipt_printer.printer = null;
                }

                const printerStatus = document.getElementById('receipt_printer_status');
                if (printerStatus && 'checked' in printerStatus && printerStatus.checked) {
                    settings.receipt_printer.status = 1;
                } else {
                    settings.receipt_printer.status = 0;
                }


                const paymentSelect = document.getElementById('payment_terminal');
                if (paymentSelect && paymentSelect.value != '' && paymentSelect.value != ' ') {
                    // Verify terminal
                    const manualId = 1;
                    if (paymentSelect.value == manualId) {
                        settings.terminal.terminal = {
                            id: manualId,
                            name: 0,
                            ip: manualId,
                            port: '',
                            type: '',
                            compatibility_port: '',
                            socket_mode: '',
                            terminal_type: '',
                            terminal_manual_payment: true,
                        }
                    } else {
                        if (paymentSelect.value in window.terminals) {
                            settings.terminal.terminal = window.terminals[paymentSelect.value];
                            let id = settings.terminal.terminal.id;
                            device_ids.push(id);
                        }
                    }
                } else {
                    settings.terminal.terminal = null;
                }

                const paymentStatus = document.getElementById('payment_terminal_status');
                if (paymentStatus && 'checked' in paymentStatus && paymentStatus.checked) {
                    settings.terminal.status = 1;
                } else {
                    settings.terminal.status = 0;
                }

                localStorage.setItem('printer_settings', JSON.stringify(settings));
                for (let input of inputs) {
                    input.removeAttribute('disabled');
                }

                if (toVerify.length == 0) {
                    localStorage.removeItem('printer_settings');
                    sendDeviceIds([]);
                    return;
                }
                startChecking();
            } else {
                for (let input of inputs) {
                    input.removeAttribute('disabled');
                }
            }
            for (let editBtn of editBtns) {
                let textInBtn = editBtn.querySelector('.text');
                if (textInBtn) textInBtn.textContent = editting ? textInBtn.getAttribute('active') : textInBtn.getAttribute('inactive');
            }
            return false;
        });
    }
}

let printerSettings = localStorage.getItem('printer_settings');
if (printerSettings != null) {
    try {
        printerSettings = JSON.parse(printerSettings);
        if ('receipt_printer' in printerSettings && 'printer' in printerSettings.receipt_printer) {
            settings.receipt_printer = printerSettings.receipt_printer;
            // Select printer on printer select
            const select = document.getElementById('receipt_printer');
            if (settings.receipt_printer.printer && select) {
                select.value = settings.receipt_printer.printer.id;

                if (settings.receipt_printer.status == 1) {
                    document.getElementById('receipt_printer_status').checked = true;
                }
            }
        }

        if ('sticker_printer' in printerSettings && 'printer' in printerSettings.sticker_printer) {
            settings.sticker_printer = printerSettings.sticker_printer;
            // Select printer on printer select
            const select = document.getElementById('sticker_printer');
            if (settings.sticker_printer.printer && select) {
                select.value = settings.sticker_printer.printer.id;

                if (settings.sticker_printer.status == 1) {
                    document.getElementById('sticker_printer_status').checked = true;
                }
            }
        }

        if ('order_printer' in printerSettings && 'printer' in printerSettings.order_printer) {
            settings.order_printer = printerSettings.order_printer;
            // Select printer on printer select
            const select = document.getElementById('order_printer');
            if (settings.order_printer.printer && select) {
                select.value = settings.order_printer.printer.id;

                if (settings.order_printer.status == 1) {
                    document.getElementById('order_printer_status').checked = true;
                }
            }
        }

        if ('terminal' in printerSettings && 'terminal' in printerSettings.terminal) {
            settings.terminal = printerSettings.terminal;
            // Select printer on printer select
            const select = document.getElementById('payment_terminal');
            if (settings.receipt_printer.printer && select) {
                select.value = settings.terminal.terminal.id;

                if (settings.terminal.status == 1) {
                    document.getElementById('payment_terminal_status').checked = true;
                }
            }
        }

        if ('language' in printerSettings)
            settings.language = printerSettings.language;
    } catch(err) {
        // There are no saves
    }
}


let checkingPrinters = false;
let config = {
    amountToCheck: 0,
    currentChecked: 0,
}

setInterval(() => {
    if (!checkingPrinters) return;
    if (toVerify.length == 0) {
        checkingPrinters = false;
        return;
    }

    const verifyPrinter = toVerify.shift();

    if ('Mine' in window)
        window.Mine.postMessage(`con:${verifyPrinter.prenonce}${verifyPrinter.name}:${verifyPrinter.ip}:${verifyPrinter.port}`);

}, 1000);

function startChecking() {
    checkingPrinters = true;
    config.currentChecked = 0;
    config.amountToCheck = toVerify.length;
}

function finishedPrinters() {
    if (config.amountToCheck == config.currentChecked) {
        Swal.fire({
            text: window.keys.printerConigurationConfirmed,
            icon: "success",
            buttonsStyling: false,
            confirmButtonText: window.keys.confirmButtonOk,
            customClass: {
                confirmButton: "btn fw-bold btn-primary",
            }
        });

        sendDeviceIds(device_ids)
    }
}

// Testing whilst we are not in apk
/*
if (!('Mine' in window)) {
    setTimeout(() => {
        sendDeviceIds(['1c2dcc13-668f-4a1d-935e-96a07a032bf9']);
    }, 5000);
}
*/

function sendDeviceIds(deviceIds) {
    const data = {};
    assignmentConfiguration.device_ids = deviceIds;
    for (let key of assignmentConfiguration.keys) {
        data[key] = assignmentConfiguration[key];
    }

    $.ajax({
        method: 'POST',
        // url: '/admin/pos/assignDeviceInPos',
        url: assignmentConfiguration.url,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        data: data,
        success: function (resp) {
            if (resp.status == 2) {
                revertPrinters();
                location.href = resp.redirect_uri;
                return;
            }

            if (resp.status == 1) {
                revertPrinters();

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

            if (resp.status == 0 || resp.status == 4) {
                const inputs = document.querySelectorAll('input:not([name="_token"]):not([immune]), select:not([immune])');
                for (let input of inputs) {
                    input.setAttribute('disabled', 'disabled');
                }
            } else {
                revertPrinters();
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
            revertPrinters();
            swal.fire({
                text: `An unexpected error occured`,
                icon: "error",
                buttonsstyling: false,
                confirmbuttontext: window.keys.confirmButtonOk,
                customClass: {
                    confirmButton: "btn fw-bold btn-primary",
                }
            });
        }
    })
}

function revertPrinters() {
    settings = reverting;
    localStorage.setItem('printer_settings', JSON.stringify(settings));
}

window.connected = nonce => {
    /*
    console.log('mutation');
    console.log(nonce + ' printer connected');
    */

    config.currentChecked++;
    if (config.amountToCheck == config.currentChecked) {
        finishedPrinters();
    }
}

window.failed_connection = (nonce, reason, status) => {
    /*
    console.log('mutation');
    console.log(nonce, reason, status);
    */

    if (status != 3) {
        Swal.fire({
            text: nonce + " " + window.keys.failedConnection,
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: window.keys.confirmButtonOk,
            customClass: {
                confirmButton: "btn fw-bold btn-primary",
            }
        });
        finishedPrinters();
    } else {
        Swal.fire({
            text: nonce + " " +  failedConnectionReason + ": " + reason,
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: window.keys.confirmButtonOk,
            customClass: {
                confirmButton: "btn fw-bold btn-primary",
            }
        });
        finishedPrinters();
    }
}
