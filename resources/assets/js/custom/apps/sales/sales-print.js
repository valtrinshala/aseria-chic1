/*
window.Mine = {
    postMessage: message => {
        console.log(message);
    }
}
*/
// Function for other javascript functions
window.invoicePrinting = string => {
    console.log(string);
    printInvoice(string);
}

window.stickerPrinting = (width, string, height = null) => {
    const sizeObject = { width: width, height: height }
    if (height == null) {
        sizeObject.replaceHeight = true;
    }

    printInvoice(string, sizeObject, {pad: {left: 40}});
}

// Pages that have no javascript but have a print button
$(document).on('click', '.print-btn', function () {
    const stringEl = document.getElementById('printer-string');
    if (stringEl && 'value' in stringEl && stringEl.value != '') {
        printInvoice(stringEl.value);
    }
});

$(document).ready(() => {
    if ('Main' in window) {
        let els = document.getElementsByClassName('show-app');
        for (let el of els) el.classList.remove('d-none');
    }


    $("#delete-order").click(function () {
        var orderId = $(this).data("order-id");
        Swal.fire({
            text: window.keys.deleteText + "?",
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
                        location.href = '/admin/order'
                    },
                    error: function (xhr, status, error) {

                    }
                });
            } else if (result.dismiss === 'cancel') {
                Swal.fire({
                    text: window.keys.notDeleted,
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


})

function loadImage(url) {
    return new Promise((resolve, reject) => {
        const image = new Image();
        image.onload = () => {
            resolve(image);
        }
        image.onerror = () => {
            resolve(0);
        }
        image.src = url;
    })
}

function processBarcode(codeValue) {
    return new Promise((resolve, reject) => {
        $.ajax({
            method: 'GET',
            url: '/admin/getBase64Image?value=' + codeValue,
            success: resp => {
                const fullString = 'data:image/png;base64,' + resp;
                const image = new Image();
                image.onload = () => {
                    resolve(image);
                }
                image.onerror = () => {
                    resolve(0);
                }

                image.src = fullString;
            },
            error: err => {
                console.log(err);
                resolve(0);
            }
        })
    })
}

async function printInvoice(printString, size = null, details = null) {
    const linePixelHeight = 24;
    let lHeight = 50;
    let lines = 0;
    if ('invoicePrintSettings' in window && 'logo_height' in window.invoicePrintSettings && !isNaN(parseFloat(window.invoicePrintSettings.logo_height))) {
        lHeight = parseFloat(window.invoicePrintSettings.logo_height);
    }

    let settings = {
        logo: {
            height: lHeight
        },
        spacing: {
            height: linePixelHeight * .5
        },
        font: {
            size: linePixelHeight,
            family: 'NanumGothicCoding',
            spacing: 10,
            last: linePixelHeight,
            weight: 'normal'
        },
        padding: {
            top: linePixelHeight,
            left: 0,
            right: 0,
            bottom: linePixelHeight + 76
        }
    }

    if (details && 'pad' in details) {
        if ('top' in details.pad) settings.padding.top += details.pad.top;
        if ('bottom' in details.pad) settings.padding.bottom += details.pad.bottom;
        if ('left' in details.pad) settings.padding.left += details.pad.left;
        if ('right' in details.pad) settings.padding.right += details.pad.right;
    }

    const formatters = {
        wordSplit(context, text, width, charPadding = 0) {
            const lines = [];
            const words = text.split(' ');

            let currentLine = '';
            let index = 0;

            // This essentailly skips measuring if the word is beyond a max average length,
            // It's to make it faster so we don't check every single word if we're very far from the width
            // This has to be lower than 1 to account for font style and size but it will still skip checking in a lot of cases
            for (let word of words) {
                if (index == 0) {
                    currentLine = word;
                    index++;
                    continue;
                }

                let testLine = currentLine + " " + word;
                if (lines.length != 0) {
                    testLine = ''.padEnd(charPadding) + testLine;
                }

                const bounds = context.measureText(testLine);
                if (bounds.width > width) {
                    if (lines.length != 0)
                        currentLine = ''.padEnd(charPadding) + currentLine;

                    lines.push(currentLine);
                    currentLine = word;
                } else {
                    currentLine += " " + word;
                }

                index++;
            }

            lines.push(currentLine);

            let finalLines = [];
            let pad = parseInt(charPadding);
            if (!isNaN(pad) && pad != 0) {
                let lineIndex = 0;
                for (let line of lines) {
                    lineIndex++;
                    let text = line;

                    if (lineIndex != 1) {
                        text = ''.padEnd(pad) + line;
                    }

                    finalLines.push(text);
                }
            } else {
                finalLines = lines;
            }

            return finalLines;
        }
    }

    const tempStorage = {
        row: {
            noReset: ['RRL', 'LRL', 'LRF', 'RRF'],
            initialY: 0,
            y: 0,
        }
    }

    const renderings = {
        LOGO: function(context, data) {
            return new Promise(async(resolve, reject) => {
                const image = await loadImage(data);
                if (image != 0) {
                    context.filter = 'grayscale(1)';

                    const aspectRatio = image.width / image.height;

                    let imageHeight = settings.logo.height;
                    let imageWidth = imageHeight * aspectRatio;

                    if (imageWidth > context.can.width) {
                        imageWidth = context.can.width;
                        imageHeight = imageWidth / aspectRatio;
                    }

                    let x = (context.can.width * .5) - (imageWidth * .5);
                    context.drawImage(image, 0, 0, image.width, image.height, x, context.currentY, imageWidth, imageHeight);

                    context.currentY += imageHeight + settings.spacing.height;
                }

                resolve(true);
            });
        },

        LR: function (context, amount) {
            // LR Is spacing
            // context.currentY += amount * ;
            context.currentY += parseFloat(amount) * settings.spacing.height;
        },

        LRR: function (context, amount) {
            context.currentY -= parseFloat(amount) * settings.spacing.height * .5;
        },

        FL: function(context, height = 1.5) {
            let h = parseFloat(height);
            if (isNaN(h)) h = 1.5;

            context.setLineDash([8]);

            context.lineWidth = h;
            context.moveTo(0, context.currentY);
            context.lineTo(canvas.width, context.currentY);
            context.stroke();
            // Reset dashes
            context.setLineDash([]);
            context.currentY += settings.spacing.height * 4;
        },

        R: function (context, data) {
            context.font = `${settings.font.weight} ${settings.font.size}px ${settings.font.family}`;
            const bounds = context.measureText('A');
            const spacing = (bounds.fontBoundingBoxAscent - bounds.fontBoundingBoxDescent) + settings.font.spacing; // settings.font.spacing * 1.5;
            // context.currentY += spacing;
        },

        B: function (context, string) {
            settings.font.weight = '600';
        },

        UB: function (context, string) {
            settings.font.weight = 'normal';
        },

        FS: function (context, fontSize) {
            settings.font.size = `${fontSize}`;
        },

        RRL: function (context, text) {
            context.textAlign = 'right';
            context.font = `${settings.font.weight} ${settings.font.size}px ${settings.font.family}`;

            let tempStoredY = tempStorage.row.y;
            context.currentY -= tempStoredY;

            context.fillText(text, context.can.width - settings.padding.right, context.currentY);


            const bounds = context.measureText(text);
            const spacing = (bounds.fontBoundingBoxAscent - bounds.fontBoundingBoxDescent) + settings.font.spacing;

            let finalY = Math.max(tempStoredY, spacing)

            context.currentY += finalY;
            tempStorage.row.y = finalY;
        },

        LRL: function (context, text) {
            context.textAlign = 'left';
            context.font = `${settings.font.weight} ${settings.font.size}px ${settings.font.family}`;

            let tempStoredY = tempStorage.row.y;
            context.currentY -= tempStoredY;

            context.fillText(text, settings.padding.left, context.currentY);

            const bounds = context.measureText(text);
            const spacing = (bounds.fontBoundingBoxAscent - bounds.fontBoundingBoxDescent) + settings.font.spacing;

            let finalY = Math.max(tempStoredY, spacing)

            context.currentY += finalY;
            tempStorage.row.y = finalY;
        },

        RRF: function (context, full) {
            const settingParts = full.split('-');

            const settingsContext = settingParts.shift().split(':');

            const percentage = parseInt(settingsContext[0]) / 100;
            const characterPadding = parseInt(settingsContext[1]);

            const text = settingParts.join('-');

            context.textAlign = 'right';
            context.font = `${settings.font.weight} ${settings.font.size}px ${settings.font.family}`;
            // context.fillText(text, context.can.width - settings.padding.right, context.currentY);


            let lines = [text];


            const startBound = context.measureText(text);
            if (startBound.width > canvas.width * percentage) {
                lines = formatters.wordSplit(context, text, canvas.width * percentage, characterPadding);
            }

            let extra = 0;
            let tempStoredY = tempStorage.row.y;
            context.currentY -= tempStoredY;

            for (let line of lines) {
                context.fillText(line, context.can.width - settings.padding.right, context.currentY);
                let bounds = context.measureText(line);
                let spacing = (bounds.fontBoundingBoxAscent - bounds.fontBoundingBoxDescent) + settings.font.spacing; // settings.font.spacing * 1.5;

                extra += spacing;
                context.currentY += spacing;
            }

            context.currentY -= extra;
            let finalY = Math.max(tempStoredY, extra)
            context.currentY += finalY;
            tempStorage.row.y = finalY;
        },

        LRF: function (context, full) {
            const settingParts = full.split('-');
            const settingsContext = settingParts.shift().split(':');

            const percentage = parseInt(settingsContext[0]) / 100;
            const characterPadding = parseInt(settingsContext[1]);

            const text = settingParts.join('-');


            context.textAlign = 'left';
            context.font = `${settings.font.weight} ${settings.font.size}px ${settings.font.family}`;
            // context.fillText(text, settings.padding.left, context.currentY);


            let lines = [text];

            const startBound = context.measureText(text);
            if (startBound.width > canvas.width * percentage) {
                lines = formatters.wordSplit(context, text, canvas.width * percentage, characterPadding);
            }

            let extra = 0;
            let tempStoredY = tempStorage.row.y;
            context.currentY -= tempStoredY;

            for (let line of lines) {
                context.fillText(line, settings.padding.left, context.currentY);

                let bounds = context.measureText(line);
                let spacing = (bounds.fontBoundingBoxAscent - bounds.fontBoundingBoxDescent) + settings.font.spacing // settings.font.spacing * 1.5;

                extra += spacing;
                context.currentY += spacing;
            }

            context.currentY -= extra;
            let finalY = Math.max(tempStoredY, extra)
            context.currentY += finalY;
            tempStorage.row.y = finalY;
        },

        BARCODE: function(context, text) {
            return new Promise(async(resolve, reject) => {
                const image = await processBarcode(text);
                if (image != 0) {
                    let imageHeight = image.height;
                    let imageWidth = image.width;
                    let aspectRatio = imageWidth / imageHeight;
                    let width = canvas.width * 0.5;
                    imageWidth = width;
                    imageHeight = imageWidth / aspectRatio;

                    if (imageWidth > context.can.width) {
                        imageWidth = context.can.width;
                        imageHeight = imageWidth / aspectRatio;
                    }

                    let x = (context.can.width * .5) - (imageWidth * .5);
                    context.drawImage(image, 0, 0, image.width, image.height, x, context.currentY, imageWidth, imageHeight);
                    context.currentY += imageHeight + (settings.font.spacing*2);

                    const extraZeros = text.padStart(10, '0')
                    context.textAlign = 'center';
                    context.fillText(extraZeros, context.can.width * .5, context.currentY);

                    context.currentY += settings.spacing.height;
                }

                resolve(true);
            });
        },

        CL: function (context, text) {
            context.textAlign = 'center';
            context.font = `${settings.font.weight} ${settings.font.size}px ${settings.font.family}`;

            let lines = [text];

            const startBound = context.measureText(text);
            if (startBound.width > canvas.width) {
                lines = formatters.wordSplit(context, text, canvas.width);
            }

            for (let line of lines) {
                context.fillText(line, context.can.width * .5, context.currentY);
                let bounds = context.measureText(line)
                let spacing = (bounds.fontBoundingBoxAscent - bounds.fontBoundingBoxDescent) + settings.font.spacing;
                context.currentY += spacing; // settings.font.spacing;
            }
        },

        BG: function(context, txt) {
            context.fillStyle = '#FFF';
            context.fillRect(0, 0, canvas.width, canvas.height);
        }
    }

    const canvas = document.createElement('canvas');

    canvas.width = 550;
    canvas.height = 0;

    canvas.style.width = `${canvas.width}px`;
    canvas.style.height = `${canvas.height}px`;
    canvas.style.position = 'absolute';
    canvas.style.zIndex = '9999999';
    canvas.style.top = '0px';
    canvas.style.left = '50%';
    canvas.style.transform = 'translateX(-50%)';
    canvas.style.background = 'white';
    canvas.style.border = '2px solid black';

    let context = canvas.getContext('2d');
    context.canvasHeight = canvas.height;
    context.can = canvas;
    context.currentY = 0;

    let lastY = 0;

    // Needs to run ones for the height and another time for the content
    for (let i = 0; i < 2; i++) {
        const string = printString;

        const contexts = string.split(';');


        context.currentY = settings.padding.top;
        await renderings['BG'](context, '');
        context.fillStyle = '#000';
        for (let con of contexts) {
            let keys = con.split(':');

            const key = keys[0];
            keys.shift();
            const string = keys.join(':');


            if (key in renderings && typeof (renderings[key]) == 'function') {
                if (!(tempStorage.row.noReset.includes(key)))
                    tempStorage.row.y = tempStorage.row.initialY;

                await renderings[key](context, string);
            }

        }

        if (lastY != context.currentY) {

            lastY = context.currentY;
            let lastHeight = context.currentY + settings.padding.bottom;
            canvas.height = lastHeight;
            canvas.style.height = `${canvas.height}px`;
        }
    }

    let base64 = canvas.toDataURL().split(',');
    base64.shift();
    base64 = base64.join(',')

    if ('Mine' in window) {
        if (size != null) {
            if ('replaceHeight' in size && size.replaceHeight)
                size.height = Math.ceil(lastY / linePixelHeight) * 4;


            if ('width' in size && 'height' in size) {
                if ('debugging_print' in window && window.debugging_print) alert(`ipi_size:${size.width}:${size.height}:`);
                Mine.postMessage(`ipi_size:${size.width}:${size.height}:` + base64);
            } else
                console.error("You have not added width and height on size when printing with size");
        } else {
            if ('debugging_print' in window && window.debugging_print) alert(`ipi:1:`);
            Mine.postMessage('ipi:1:' + base64);
        }
    }

    canvas.addEventListener('click', () => {
        canvas.remove();
    })

    if (!('Mine' in window)) {
        document.body.appendChild(canvas);
    }
}

