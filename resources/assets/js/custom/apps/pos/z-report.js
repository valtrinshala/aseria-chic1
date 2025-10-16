$(document).ready(function() {
    const uis = $('.saldo-ui');
    uis.each((_, element) => {
        let currency_settings = { currency: '', currency_left: false };

        if (element.classList.contains('display-text')) {
            // Start up the display with 0.00
            const currencySymbol = element.getAttribute('currency-symbol');
            if (currencySymbol) currency_settings.currency = currencySymbol;
            const currencyPos = element.getAttribute('currency-left');
            if (currencyPos) {
                currency_settings.currency_left = currencyPos == 1;

                element.textContent = currency_settings.currency_left ? currency_settings.currency + " 0.00" : "0.00 " + currency_settings.currency;
            }
        }

        element.keypadEvent = pad => {
            // Don't allow any numbers above 2 decimal places
            const seps = pad.steppedValue.split('.');
            if (seps.length > 1)
                pad.steppedValue = seps[0] + '.' + seps[1].substr(0, 2);

            let price = isNaN(parseFloat(pad.steppedValue)) ? 0 : parseFloat(pad.steppedValue);

            if (element.classList.contains('display-text')) {
                // Set the value of the display
                price = price.toFixed(2);
                let priceString = price + " " + currency_settings.currency;
                if (currency_settings.currency_left) priceString = currency_settings.currency + " " + price;

                element.textContent = priceString;
            }

            if (element.classList.contains('input')) {
                element.value = price;
            }
        }
    })
})
