// Bring this value from settings later on
let showingKeyboard = true;

$(document).on("click", '.show-key-pads', function() {
    if (showingKeyboard)
        $('.key-pad').addClass('d-none');
    else
        $('.key-pad').removeClass('d-none');

    showingKeyboard = !showingKeyboard;
});

// Yes this has the same logic as dynamic keypads but this will be used everywhere
const keypads = {};

$(document).on('click', '.key-pad button', function(e) {
    // Allow nothing else to run when running keypad
    e.preventDefault();
    e.stopPropagation();

    const mainArea = $(this).closest('.key-pad')[0];

    let currentPad = {steppedValue: '', lastValue: '', currentValue: ''};
    if ('keypadId' in mainArea && mainArea.keypadId in keypads) {
        currentPad = keypads[mainArea.keypadId];
    } else {
        mainArea.keypadId = Object.keys(keypads).length;
        keypads[mainArea.keypadId] = currentPad;
    }

    let val = this.getAttribute('value');
    if (val == 'back')
        currentPad.steppedValue = currentPad.steppedValue.substring(0, currentPad.steppedValue.length - 1);
    else
        currentPad.steppedValue += val;


    const targetClass = mainArea.getAttribute('targets');

    if (targetClass) {
        const targets = document.getElementsByClassName(targetClass);
        for (let target of targets) {
        if ('keypadEvent' in target && typeof(target.keypadEvent) == 'function')
            target.keypadEvent(currentPad);
        }
    }

    return false;
});