@extends('layouts.blank-view')
@section('title', 'Results')
@section('page-style')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/horizon.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/Oswald_font.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
    <style>
        * {
            font-family: 'Oswald' !important;
        }

        html, body {
            font-size: 12px !important;
        }

        .selecting-cash-register {
            background: white;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            position: fixed;
            top: 0;
            left: 0;
        }

        .selecting-cash-register.hidden {
            display: none;
        }

        .hideable.hide {
            display: none !important;
        }

        .customer-image {
            max-height: 20vh;
            width: auto;
        }

        .image-preview {
            /* position: fixed; */
            position: relative;
            inset: 0;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: 99999;
            width: 100%;
            height: 100%;
        }
    </style>
    <input id="uri" type="hidden" url="{{ Storage::disk('public')->url('') }}">
    <div class="whole-container-cs row h-100 d-flex">
        <div class="col left-side-cs overflow-hidden h-100 m-0">
            <div class="title bg-custom-gray title-section-cs">
                <h1 class="cs-title m-4">{{ __('Your basket') }}</h1>
            </div>

            <div class="flex-grow-1 overflow-hidden border-custom-cs">

                <div class="order-items d-flex flex-column">
                    <div class="item w-100 p-5 d-flex justify-content-between order-items-layout">
                        <div class="left-info me-10">
                            <div class="d-flex flex-column gap-0">
                                <h3 class="mb-0">{quantity}x {name}</h3>
                                <div class="extras">
                                    <p class="mb-0 {has_add}"><span class="">Add:</span> <span class="add-text">{add_text}</span></p>
                                    <p class="mb-0 {has_remove}"><span class="">No:</span> <span class="remove-text">{rem_text}</span></p>
                                </div>
                                <div class="products {has_products}">
                                    <p class="products-holder fs-4 ms-10">{product_html}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-0 right-info">
                            <div class="d-flex flex-column justify-content-between h-100">
                                <p class="mb-0 price-per d-none text-end">{price_per}</p>
                                <h2 class="mb-0 price fw-300 text-end">{sub_total}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box-bg title-section-cs white-children p-4">
                <div class="d-flex flex-stack  rounded-3">
                    <div class="fs-6 ">
                        <span class="d-none font-span ">{{ __('Order type') }}:</span>
                        <span class="d-none font-span hideable">{{ __('Note') }}:</span>
                        <span class="d-none font-span hideable">{{ __('Payment type') }}:</span>
                        <span class="d-none font-span disc-type d-none">{{ __('Discount') }} <span class="discount-type"></span></span>
                        <span class="d-none mb-2 font-span ">{{ __('Tax') }} <span class="tax-type"></span></span>
                        <span class="d-block fs-3 lh-1 fw-bold">{{ __('Total') }}:</span>
                    </div>
                    <div class="fs-6 text-end">
                        <span class="d-none font-span order-type">{{ __('Take away') }}</span>
                        <span class="d-none order-status info hideable font-span order-type"></span>
                        <span class="d-none font-span mb-0 flex-grow-1 hideable info payment-type text-end fw-medium" default="{{ __('Not specified') }}"></span>
                        <span class="d-none font-span discount-area"></span>
                        <span class="d-none mb-2 font-span tax-area"></span>
                        <span class="total-amount d-block fs-3 lh-1 fw-bold" data-kt-pos-element="grant-total">0.00</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col p-0 right-side-cs bg-custom-gray">
            <div id="preview-holder" class="h-100">
                <!-- <div class="image-preview"></div> -->
                <!--
                <div class="d-flex flex-column justify-content-between gap-20 h-100 p-12">
                    <div class="top-bit">
                        <div class="w-100 d-flex justify-content-center">
                            <img class="customer-image" src="{{$settings->getClientImage()}}"></img>
                        </div>
                    </div>
                    <div class="bottom-bit">
                        <div class="d-flex flex-column gap-19 mb-5">
                            @if (false)
                            <div class="order-heading">
                                <div class="d-flex justify-content-between">
                                    <h2 class="mb-0 flex-grow-1">Order ID:</h2>
                                    <h2 class="mb-0 flex-grow-1 info order-id text-end fw-medium">#12341234123</h2>
                                </div>
                            </div>
                            @endif

                            <div class="order-body">
                                <div class="d-flex justify-content-between hideable hide">
                                </div>
                            </div>
                        </div>


                        Old

                        <div class="box-bg bg-white box-radius p-5 pt-0 mt-auto pb-0">
                            <div class="d-flex flex-stack  rounded-3 py-4">
                                <div class="fs-6 ">
                                    <span class="d-block font-span ">{{ __('Order type') }}:</span>
                                    <span class="d-block font-span hideable">{{ __('Note') }}:</span>
                                    <span class="d-block font-span hideable">{{ __('Payment type') }}:</span>
                                    <span class="d-block font-span disc-type d-none">{{ __('Discount') }} <span class="discount-type"></span></span>
                                    <span class="d-none mb-2 font-span ">{{ __('Tax') }} <span class="tax-type"></span></span>
                                    <span class="d-block fs-3 lh-1 text-violet fw-bold">{{ __('Total') }}:</span>
                                </div>
                                <div class="fs-6 text-end">
                                    <span class="d-block font-span order-type">{{ __('Take away') }}</span>
                                    <span class="d-block order-status info hideable font-span order-type"></span>
                                    <span class="d-block font-span mb-0 flex-grow-1 hideable info payment-type text-end fw-medium" default="{{ __('Not specified') }}"></span>
                                    <span class="d-block font-span discount-area"></span>
                                    <span class="d-none mb-2 font-span tax-area"></span>
                                    <span class="total-amount d-block fs-3 lh-1 text-violet fw-bold" data-kt-pos-element="grant-total">0.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-4 d-flex flex-column gap-3 rounded-1 d-none">
                            <div class="d-flex justify-content-between">
                                <h2 class="mb-0">{{-- __('Discount') }}:</h2>
                                <h2 class="mb-0 discount-amount" default="{{ __('No discounts') --}}"></h2>
                            </div>
                            <div class="d-flex justify-content-between">
                                <h2 class="mb-0 fs-1 text-violet">Total:</h2>
                                <h2 class="mb-0 fs-1 total-amount text-violet"></h2>
                            </div>
                        </div>

                    </div>
                -->
                </div>
            </div>
        </div>
    </div>
    <div class="selecting-cash-register hidden">
        <div class="p-20">
            <div class="mb-10">
                <label for="location" class="fs-1">{{ __('Location') }}</label>
                <select id="location" name="location" class="form-select mb-2"
                        data-control="select2" data-placeholder="{{ __('Select location') }}"
                        data-allow-clear="true">
                    <option selected></option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="cashRegister" class="fs-1">{{ __('Cash Register') }}</label>
                <select id="cashRegister" name="cashRegister" class="form-select mb-2"
                        data-control="select2" data-placeholder="{{ __('Select cash register') }}"
                        data-allow-clear="true">
                    <option selected></option>
                </select>
            </div>
        </div>
    </div>
    <script>
        const currentPreviewImage = 0;
        let previewImages = {};

        let lastActivity = Date.now();
        let showing = false;
        let isClean = true;

        let imageData = {
            changeTimer: 5000,
            lastImage: Date.now(),
            index: 0,
            url: ''
        }

        let currency_symbol = `{{$settings['currency_symbol']}}`;
        const currency_pos_left = `{{$settings['currency_symbol_on_left'] ? 1 : 0}}`;
        currency_pos_left == 1 ? currency_symbol += ' ' : currency_symbol = ' ' + currency_symbol;

        class SwapLayouts {
            constructor(layoutSelector, clear = true, hideable = true) {
                this.keys = [];
                this.layoutSelector = layoutSelector;
                this.hideable = hideable;
                this.attach(this.layoutSelector, false);
                this.getVariables();
                if (clear) this.clear();
            }

            // Variables will only be taken from the first element selected
            getVariables() {
                if (this.parent == null) return;

                this.html = this.construct.outerHTML;
                let keys = this.html.matchAll(/{.+?}/gm);
                let next = {done: false};
                do {
                    next = keys.next();
                    if (next.done == false) {
                        let keyT = next.value;
                        for (let a of keyT)
                            this.keys.push(a);
                    }
                } while(next.done == false)
            }

            clear() {
                if (this.parent == null) return;
                this.parent.innerHTML = '';
                if (this.hideable)
                    $(this.parent).closest('.modification').css('display', 'none');
            }

            insert(obj) {
                if (this.hideable)
                    $(this.parent).closest('.modification').css('display', 'block');

                if (this.parent == null) return;
                let toChange = {};
                let objKeys = Object.keys(obj);
                for (let key of objKeys) {
                    if (this.keys.includes(`{${key}}`)) {
                        toChange[`{${key}}`] = obj[key];
                    }
                }

                let finalHtml = this.html;
                for (let key in toChange) {
                    finalHtml = finalHtml.replaceAll(key, toChange[key]);
                }

                this.parent.innerHTML += finalHtml;
            }

            attach(newSelector, clear = true) {
                this.layoutSelector = newSelector;
                let layoutHtml = document.querySelector(this.layoutSelector);
                if (layoutHtml) {
                    this.construct = layoutHtml;
                    this.parent = this.construct.parentElement;
                }

                if (clear) this.clear();
                return this;
            }
        }


        const itemLayout = new SwapLayouts('.order-items-layout', true, false);

        let registerId = null;
        let cashRegisters = {};
        const translations = {
            take_away: `{{ __('Take away') }}`,
            dine_in: `{{ __('Dine in') }}`,
            delivery: `{{ __('Delivery') }}`,
        };

        const sizeKeys = {
            small: `{{ __('Small') }}`,
            medium: `{{ __('Medium') }}`,
            large: `{{ __('Large') }}`
        }

        @foreach($cashRegisters as $cashRegister)
            if (!(`{{$cashRegister->location_id}}` in cashRegisters)) cashRegisters[`{{ $cashRegister->location_id }}`] = [];

            cashRegisters[`{{ $cashRegister->location_id }}`].push({
                id: '{{ $cashRegister->id }}',
                name: '{{ $cashRegister->name }}'
            });
        @endforeach

        $('#location').change(function() {
            const registerSelect = document.getElementById('cashRegister');
            let options = registerSelect.options;
            for (let i = options.length - 1; i > 0; i--) {
                registerSelect.options[i].remove();
            }

            if (this.value in cashRegisters) {
                if (this.value != '') {
                    let newOptions = cashRegisters[this.value];

                    for (let option of newOptions) {
                        let optionElement = document.createElement('option');
                        optionElement.value = option.id;
                        optionElement.textContent = option.name;
                        registerSelect.appendChild(optionElement);
                    }
                }
            }
        })

        $('#cashRegister').change(function() {
            if (this.value != '') {
                registerId = this.value;
                localStorage.setItem('cash-reg', registerId);

                closeRegisterScreen();
            }
        })

        $(document).ready(() => {
            /*
            registerId = localStorage.getItem('cash-reg');
            if (registerId == null) {
                openRegisterScreen();
            }

            getResults().then(results => {
                updateUI(results);
            }).catch(err => {console.log(err)});

            setInterval(() => {
                getResults().then(results => {
                    updateUI(results);
                }).catch(err => {console.log(err)});
            }, 2000);
             */
        });

        function openRegisterScreen() {
            const showingElements = document.getElementsByClassName('selecting-cash-register');
            for (let showingElement of showingElements) {
                showingElement.classList.remove('hidden');
            }
        }

        function closeRegisterScreen() {
            const showingElements = document.getElementsByClassName('selecting-cash-register');
            for (let showingElement of showingElements) {
                showingElement.classList.add('hidden');
            }
        }

        function getResults() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    async: true,
                    url: `getDataCS?cash_register_id=${registerId}`,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if ('data' in response)
                            resolve(response.data);
                        reject('Data is missing');
                    },
                    error: function (error) {
                        reject(error);
                    }
                });
            });
        }

        function swap(cl, text, hasDefault = false) {
            const els = document.getElementsByClassName(cl);
            for (let el of els) {
                let uiText = text;
                if (hasDefault)
                    uiText = el.getAttribute('default');

                el.textContent = uiText;
            }
        }

        function cleanUI() {
            itemLayout.clear();

            swap('payment-type', '', true);
            swap('discount-area', '', true);
            swap('total-amount', '');

            let noteElements = document.getElementsByClassName('hideable');
            for (let noteElement of noteElements) {
                noteElement.classList.add('hide');
            }


        }

        function updateUI(results) {
            cleanUI();
            if ('clean' in results) {
                isClean = true;
                return;
            }
            isClean = false;

            let orderType = results.order_type;
            if (orderType in translations) orderType = translations[orderType];

            swap('order-type', orderType);

            let cartPrice = parseFloat(results.cart_total_price);

            if ('tax' in results && !isNaN(parseFloat(results.tax))) {
                cartPrice = (100 / (parseFloat(results.tax) + 100)) * cartPrice;

                let price = parseFloat(cartPrice * (parseFloat(results.tax) * 0.01)).toFixed(2);
                let priceString = currency_symbol + price;
                if (currency_pos_left == 0) priceString = price + currency_symbol;

                swap('tax-area', priceString);
                swap('tax-type', results.tax + '%');
            }

            if (results.discount_amount != 0) {
                const discElements = document.getElementsByClassName('disc-type');
                for (let discElement of discElements) {
                    discElement.classList.remove('d-none');
                }

                swap('discount-type', results.final_disc_percentage + "%")
                swap('discount-area', results.final_disc_value)

                /*
                if (results.is_discount_in_percentage == 'true') {
                    let disc_amount = cartPrice * (parseFloat(results.discount_amount) * 0.01);
                    cartPrice -= disc_amount;
                    swap('discount-type', results.discount_amount + '%');


                    let price = parseFloat(disc_amount).toFixed(2);
                    let priceString = currency_symbol + price;
                    if (currency_pos_left == 0) priceString = price + currency_symbol;
                    swap('discount-area', priceString);
                } else {

                    let percentile = (parseFloat(results.discount_amount) / cartPrice) * 100;
                    cartPrice -= parseFloat(results.discount_amount);

                    let price = parseFloat(results.discount_amount).toFixed(2);
                    let priceString = currency_symbol + price;
                    if (currency_pos_left == 0) priceString = price + currency_symbol;

                    swap('discount-type', percentile.toFixed(2) + '%');
                    swap('discount-area', priceString);
                }
                */


                let price = parseFloat(cartPrice * (parseFloat(results.tax) * 0.01)).toFixed(2);
                let priceString = currency_symbol + price;
                if (currency_pos_left == 0) priceString = price + currency_symbol;

                swap('tax-area', priceString);
            } else {
                const discElements = document.getElementsByClassName('disc-type');
                for (let discElement of discElements) {
                    discElement.classList.add('d-none');
                }
                swap('discount-type', '');
                swap('discount-area', '');
            }

            if ('tax' in results && !isNaN(parseFloat(results.tax)))
                cartPrice = (cartPrice + parseFloat(results.tax) * 0.01 * cartPrice);

            swap('total-amount', results.final_price);

            if ('items' in results && results.items.length > 0) {
                if (showing) {
                    removeCurtain();
                }

                showing = false;
                lastActivity = Date.now();
                for (let i = results.items.length - 1; i >= 0; i--) {
                    let item = results.items[i];
                    if (item.type == 'product') {

                        let pricePer = parseFloat(item.price_per).toFixed(2);
                        let priceStringPer = currency_symbol + pricePer;
                        if (currency_pos_left == 0) priceStringPer = pricePer + currency_symbol;

                        let subtotal = parseFloat(item.sub_total).toFixed(2);
                        let subtotalString = currency_symbol + subtotal;
                        if (currency_pos_left == 0) subtotalString = subtotal + currency_symbol;

                        let layoutObj = {
                            name: item.name,
                            quantity: item.quantity,
                            price_per: priceStringPer,
                            sub_total: subtotalString,
                            has_add: 'hideable hide',
                            add_text: '',
                            has_remove: 'hideable hide',
                            rem_text: '',
                            has_products: 'hideable hide',
                            product_html: ''
                        }

                        if ('removed_ingredients_names' in item && item.removed_ingredients_names.length != 0) {
                            layoutObj.has_remove = '';
                            layoutObj.rem_text = item.removed_ingredients_names.join(', ');
                        }

                        if ('size' in item) {
                            for (let size in item.size) {
                                if (!isNaN(parseFloat(item.size[size]))) {
                                    if (size in sizeKeys)
                                        layoutObj.name += ', ' + sizeKeys[size];

                                    break;
                                }
                            }
                        }

                        if ('modifiers' in item) {
                            if (item.modifiers.length != 0) {
                                let modStrings = [];
                                for (let mod of item.modifiers) {
                                    modStrings.push(`${mod.quantity}x ${mod.name}`);
                                }

                                layoutObj.has_add = '';
                                layoutObj.add_text = modStrings.join(', ');
                            }
                        }

                        itemLayout.insert(layoutObj);
                    }

                    if (item.type == 'meal' || item.type == 'deal') {

                        let pricePer = parseFloat(item.price_per).toFixed(2);
                        let priceStringPer = currency_symbol + pricePer;
                        if (currency_pos_left == 0) priceStringPer = pricePer + currency_symbol;

                        let subtotal = parseFloat(item.sub_total).toFixed(2);
                        let subtotalString = currency_symbol + subtotal;
                        if (currency_pos_left == 0) subtotalString = subtotal + currency_symbol;

                        let layoutObj = {
                            name: item.name,
                            quantity: item.quantity,
                            price_per: priceStringPer,
                            sub_total: subtotalString,
                            has_add: 'hideable hide',
                            add_text: '',
                            has_remove: 'hideable hide',
                            rem_text: '',
                            has_products: 'hideable hide',
                            product_html: ''
                        }

                        const removedIngredients = [];
                        const extraModifiers  = [];

                        if ('products' in item && item.products.length != 0) {
                            let productStrings = [];
                            for (let product of item.products) {
                                const prod = product;

                                if ('size' in product) {
                                    for (let size in product.size) {
                                        if (!isNaN(parseFloat(product.size[size]))) {
                                            if (size in sizeKeys)
                                                prod.name += ', ' + sizeKeys[size];

                                            break;
                                        }
                                    }
                                }

                                productStrings.push(`- ${prod.quantity}x ${prod.name}`);

                                if ('removed_ingredients_names' in prod && prod.removed_ingredients_names.length != 0) {
                                    removedIngredients.push(prod.removed_ingredients_names.join(', '));
                                }

                                if ('modifiers' in prod) {
                                    if (prod.modifiers.length != 0) {
                                        let modStrings = [];
                                        for (let mod of prod.modifiers) {
                                            modStrings.push(`${mod.quantity}x ${mod.name}`);
                                        }

                                        extraModifiers.push(modStrings.join(', '));
                                    }
                                }
                            }


                            layoutObj.has_products = '';
                            layoutObj.product_html = productStrings.join('<br>');
                        }

                        if (removedIngredients.length != 0) {
                            layoutObj.has_remove = '';
                            layoutObj.rem_text = removedIngredients.join(', ');
                        }

                        if (extraModifiers.length != 0) {
                            layoutObj.has_add = '';
                            layoutObj.add_text = extraModifiers.join(', ');
                        }

                        itemLayout.insert(layoutObj);
                    }

                }
            }
        }


        let firstLoad = true;
        function checkImagePreview() {
            if (registerId == null) return;
            /*
            if (Date.now() - lastActivity > 15000 && isClean) {
                if (showing && Date.now() - imageData.lastImage < imageData.changeTimer) return;
                */

                $.ajax({
                    method: 'GET',
                    url: `/cs/getImage?cash_register_id=${registerId}`,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: resp => {
                        if (resp.status == 2) {
                            location.href = resp.redirect_uri;
                            return;
                        }

                        if (resp.status == 0) {
                            const holder = document.getElementById('preview-holder');
                            if (!holder) return;

                            const data = resp.data;
                            const images = data.images;

                            const keyMap = data.images.map(element => element.id);

                            const keys = Object.keys(previewImages);
                            for (let key of keys) {
                                if (!keyMap.includes(key)) {
                                    // Remove key
                                    const el = document.querySelector(`[image-id="${key}"]`);
                                    if (el) el.remove();

                                    delete previewImages[key];
                                }
                            }


                            if (images.length != 0) {
                                for (let newImage of images) {
                                    const key = newImage.id;

                                    if (key in previewImages) continue;

                                    previewImages[key] = true;

                                    const uriElement = document.getElementById('uri');
                                    let url = uriElement.getAttribute('url');

                                    const fullUrl = url + newImage.image;

                                    const previewElement = document.createElement('div');
                                    previewElement.classList.add('image-preview');

                                    previewElement.setAttribute('image-id', newImage.id);

                                    previewElement.style.left = `-500vh`;
                                    previewElement.style.top = `-500vw`;
                                    previewElement.style.backgroundImage = `url('${fullUrl}')`;

                                    holder.appendChild(previewElement);

                                }
                            }

                            if (firstLoad) {
                                firstLoad = false;
                                return;
                            }

                            const currentImageTime = Date.now();
                            imageData.lastImage = currentImageTime;

                            const previewKeys = Object.keys(previewImages);
                            imageData.index++;
                            if (imageData.index >= previewKeys.length) {
                                imageData.index = 0;
                            }

                            const key = previewKeys[imageData.index];

                            // This is less efficient but will assure tht one image will always be loaded
                            const currentPreview = document.querySelector(`.image-preview[image-id="${key}"]`);
                            currentPreview.style.display = 'block';
                            currentPreview.style.left = '0';
                            currentPreview.style.top = '0';

                            const prevImages = document.querySelectorAll('.image-preview');
                            for (let element of prevImages) {
                                if (element.getAttribute('image-id') != key) element.style.display = 'none';
                            }
                        }

                        if (resp.message != '') {
                            Swal.fire({
                                text: resp.message,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        }

                        return;
                    },
                    error: err => {
                        console.error(err);
                        Swal.fire({
                            text: 'An unexpected error occured',
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                })
                showing = true;

            // }
        }

        function removeCurtain() {
            /*
            const curtain_element = document.getElementById('image-preview');
            if (curtain_element)
                curtain_element.style.display = 'none';
             */
        }

        function primary_update(stringifiedData) {
            const data = JSON.parse(stringifiedData);

            if ('cash_register' in data) {
                let oldRegister = registerId;
                registerId = data.cash_register;

                if (oldRegister == null) {
                    checkImagePreview();
                }
            }

            if ('reload' in data && data.reload) {
                localStorage.setItem('cash_register_temp', registerId);
                location.reload();
                retrun;
            }

            updateUI(data);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const cashRegisterTemp = localStorage.getItem('cash_register_temp');

            if (cashRegisterTemp) {
                localStorage.removeItem('cash_register_temp');
                primary_update(`{"cash_register":"${cashRegisterTemp}", "clean":"true"}`)
            }
        });


        setInterval(() => {
            checkImagePreview();
        }, 1000);


        // primary_update(`{"cash_register":"9b444c76-6862-4ba3-af93-c3f908ea9090","order_type":"none","table_id":null,"locator":null,"cart_total_item":5,"cart_total_price":62.5,"tax":0,"payment_method_id":0,"is_discount_in_percentage":true,"discount_amount":0,"items":[{"type":"deal","id":"21916247-2f79-4791-98bb-34cfbd9cb094","price_per":14.5,"quantity":1,"name":"Menu chicken wrap","products":[{"id":"7ed43155-a14d-42c5-9ca0-553f8db5d252","price_per":7.9,"name":"Chicken Wrap","quantity":1,"ingredients":["49ee73c4-b547-4f28-9da9-1d74d2d9dc37","ae5985d0-9fdb-4270-b9a6-46dfedebe2c6"],"modifiers":[{"id":"587379b8-410f-4963-b632-8bc7ed959046","price_per":"1.00","name":"Bacon","quantity":1},{"id":"ba7e1c4a-9ff7-4427-9d43-fd6c82236f60","price_per":"0.00","name":"Tartare","quantity":1},{"id":"e6fc5f00-9919-4d2d-ae09-d8c263d87ac1","price_per":"0.60","name":"Maïs","quantity":1},{"id":"1327a33b-7ae7-4bb4-aabb-489c9beee834","price_per":"2.00","name":"Double bacon","quantity":1},{"id":"e99b396c-7cff-4d9d-925e-b14dc2ed2fce","price_per":"2.00","name":"Cheese et bacon","quantity":1},{"id":"9c23c2f5-a2ab-4cad-b215-6cf1b724b1fc","price_per":"2.00","name":"Double cheese","quantity":1},{"id":"f9c7aec2-0c94-46a4-b325-53083bbe6521","price_per":"0.00","name":"Curry","quantity":1}],"size":{"small":null,"medium":null,"large":null},"sub_total":15.5,"removed_ingredients_names":["Mayonnaise","Oignons"]},{"id":"20987fbe-f2a1-4c14-98b4-2155536433ef","price_per":3.9,"name":"Coca Cola Zéro","quantity":1,"ingredients":["64c43f43-d7ac-4896-b8f8-49751db5fc37"],"modifiers":[],"size":{"small":null,"medium":1,"large":null},"sub_total":3.9,"removed_ingredients_names":[]},{"id":"f5f42561-f5e3-4e3e-b154-f551f68f845d","price_per":4.5,"name":"Frites","quantity":1,"ingredients":["2c175f39-b813-40e8-8eed-ca3158c9d8dd"],"modifiers":[],"size":{"small":null,"medium":null,"large":0.6},"sub_total":4.5,"removed_ingredients_names":[]}],"sub_total":23.700000000000003},{"type":"product","id":"7ed43155-a14d-42c5-9ca0-553f8db5d252","price_per":7.9,"quantity":1,"name":"Chicken Wrap","size":{"small":null,"medium":null,"large":null},"ingredients":["49ee73c4-b547-4f28-9da9-1d74d2d9dc37","ae5985d0-9fdb-4270-b9a6-46dfedebe2c6"],"removed_ingredients_names":["Oignons","Mayonnaise"],"modifiers":[{"id":"587379b8-410f-4963-b632-8bc7ed959046","price_per":1,"name":"Bacon","quantity":1},{"id":"ba7e1c4a-9ff7-4427-9d43-fd6c82236f60","price_per":0,"name":"Tartare","quantity":1},{"id":"d5aacc3a-2382-460d-91c8-dd6bbc05e0f9","price_per":0,"name":"Piquante","quantity":1}],"sub_total":8.9},{"type":"product","id":"81a38dee-7399-45e8-b038-d2486e54cf32","price_per":6.9,"quantity":1,"name":"Filet Burger","size":{"small":null,"medium":null,"large":null},"ingredients":["0d9ef60a-0ed5-4224-abfa-53a8ce3b921b"],"removed_ingredients_names":[],"modifiers":[],"sub_total":6.9},{"type":"deal","id":"bf4e0f62-92d0-4a67-91e4-5c6f76a6eb2f","price_per":16.9,"quantity":1,"name":"Menu Maison 4x Filet mignons","products":[{"id":"759afd9f-e45d-43fd-867a-ebc6e34bf654","price_per":7.9,"name":"4 filets mignons","quantity":1,"ingredients":["0d9ef60a-0ed5-4224-abfa-53a8ce3b921b"],"modifiers":[{"id":"e6fc5f00-9919-4d2d-ae09-d8c263d87ac1","price_per":"0.60","name":"Maïs","quantity":1}],"size":{"small":null,"medium":null,"large":null},"sub_total":8.5,"removed_ingredients_names":[]},{"id":"c6cef433-d0bc-4c26-9f31-0417f7350e01","price_per":4.5,"name":"Coca Cola","quantity":1,"ingredients":["64c43f43-d7ac-4896-b8f8-49751db5fc37"],"modifiers":[],"size":{"small":null,"medium":null,"large":1},"sub_total":4.5,"removed_ingredients_names":[]},{"id":"f5f42561-f5e3-4e3e-b154-f551f68f845d","price_per":3.9,"name":"Frites","quantity":1,"ingredients":["2c175f39-b813-40e8-8eed-ca3158c9d8dd"],"modifiers":[],"size":{"small":null,"medium":0,"large":null},"sub_total":3.9,"removed_ingredients_names":[]},{"id":"3285895d-a584-4f25-9083-1aa0d3cfad1e","price_per":4.5,"name":"Riz","quantity":1,"ingredients":["ec73dd76-bac0-48f2-a069-fe8b5bad3f40"],"modifiers":[],"size":{"small":null,"medium":0,"large":null},"sub_total":4.5,"removed_ingredients_names":[]}],"sub_total":18.5},{"type":"product","id":"20987fbe-f2a1-4c14-98b4-2155536433ef","price_per":3.9,"quantity":1,"name":"Coca Cola Zéro","size":{"small":null,"medium":1,"large":null},"ingredients":["64c43f43-d7ac-4896-b8f8-49751db5fc37"],"removed_ingredients_names":[],"modifiers":[{"id":"e6fc5f00-9919-4d2d-ae09-d8c263d87ac1","price_per":0.6,"name":"Maïs","quantity":1}],"sub_total":4.5}],"paid_cash":0,"paid_bank":0,"payment_return":0,"save_order":true,"final_price":"62.50 CHF","final_disc_value":"0.00 CHF","final_disc_percentage":0}`)
    </script>
@endsection
