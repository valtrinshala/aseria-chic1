<!DOCTYPE html>

<html lang="{{ session()->get('locale') ?? app()->getLocale() }}" data-bs-theme="light">

<head>
    <meta charset="utf-8"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo/favicon.png') }}"/>

    <title>@yield('title') | {{ getenv('APP_NAME') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700"/>
    @yield('page-style')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.bundle.css') }}"> {{--Bootstrap 5--}}
    <link rel="stylesheet" type="text/css"
          href="{{ asset('assets/plugins/global/plugins.bundle.css') }}"> {{-- Theme css--}}
    <link href="{{ asset('assets/css/horizon.css') }}" rel="stylesheet" type="text/css">

    <script> document.documentElement.setAttribute("data-bs-theme", 'light');</script>

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script> {{--JQuery Library--}}
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script> {{--Theme Library--}}
    @vite('resources/assets/js/custom/apps/main.js')

    @yield('setup-script')

</head>

<body id="kt_app_body" dir="{{ \App\Helpers\Helpers::currentLanguage()?->direction ?? 'ltr' }}" data-kt-app-header-fixed="true"
      data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="false"
      data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true"
      data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">
<script>
    @if($errors->first('pos_error') != null || $errors->first('kitchen_error') != null)
    Swal.fire({
        text: `{{ $errors->first('pos_error') != null ? $errors->first('pos_error') : $errors->first('kitchen_error')}}`,
        icon: "error",
        buttonsStyling: false,
        confirmButtonText: "Ok!",
        customClass: {
            confirmButton: "btn btn-primary"
        }
    })
    @endif
        window.invoicePrintSettings = JSON.parse(`{!! json_encode(App\Helpers\Helpers::invoicePrintSettings()); !!}`);

        window.keys = {
        com_orders: `{{__('orders completed')}}`,
        ekiosk: `{{__('eKiosk has been successfully added!')}}`,
        ekioskErrorMessage: `{{__('Sorry, looks like there are some errors detected')}}`,
        ekioskErrorsDetected: `{{__('Sorry, looks like there are some errors detected, please try again.')}}`,
        ekioskServerErrors: `{{__('Sorry, looks like there are some errors in server, please try again.')}}`,
        confirmButtonOk: `{{__('Ok, got it!')}}`,
        ekioskValidationName: `{{__('eKiosk name is required')}}`,
        ekioskValidationId: `{{__('eKiosk id is required')}}`,
        deleteText: `{{__('Are you sure you want to delete')}}`,
        confirmButtonText: `{{__('Yes, delete!')}}`,
        cancelButtonText: `{{__('No, cancel')}}`,
        notDeleted: `{{__('was not deleted.')}}`,
        selectedText: `{{__('Selected e kiosks was not deleted')}}`,
        assetValidationName: `{{__('Asset name is required')}}`,
        assetValidationPosition: `{{__('Position is required')}}`,
        assetValidationEkiosk: `{{__('E-kiosk is required')}}`,
        ekioskSuccesfullyAdded: `{{__('eKiosk asset position has been successfully added!')}}`,
        assetValidationPositionName: `{{__('Position name is required')}}`,
        positionUrl: `{{__('Url is required')}}`,
        positionEkiosk: `{{__('e Kiosk is required')}}`,
        cashRegisterRequired: `{{__('Cash register name is required')}}`,
        cashRegisterAdded: `{{__('The cash register has been successfully added!')}}`,
        deletedCustomers: `{{__('Are you sure you want to delete selected customers?')}}`,
        selectedCustomers: `{{__('Selected customers was not deleted.')}}`,
        customerRequired: `{{__('Customer name is required')}}`,
        userEmailRequired: `{{__('User email is required')}}`,
        emailAddresRequired: `{{__('The value is not a valid email address')}}`,
        customerSuccessfullyAdded: `{{__('Customer has been successfully added!')}}`,
        selectedCategories: `{{__('Selected food categories was not deleted.')}}`,
        deleteSelectedCategories: `{{__('Are you sure you want to delete selected categories?')}}`,
        categoryNameRequired: `{{__('Category name is required')}}`,
        categorySuccessfullyAdded: `{{__('The category has been successfully added!')}}`,
        deleteSelectedProducts: `{{__('Are you sure you want to delete selected products?')}}`,
        selectedProducts: `{{__('Selected food items was not deleted.')}}`,
        productNameRequired: `{{__('Product name is required')}}`,
        ingredientsRequired: `{{__('Ingredients are required')}}`,
        categoryRequired: `{{__('The category is required')}}`,
        priceRequired: `{{__('Price is required')}}`,
        regexRequired: `{{__('Please enter a valid number')}}`,
        costRequired: `{{__('Cost is required')}}`,
        sizeProducts: `{{__('You cannot select only one size, you must select 2 or more, or you can create products without sizes!')}}`,
        productSuccessfullyAdded: `{{__('The product has been successfully added')}}`,
        selectedIngredients: `{{__('Selected ingredients was not deleted')}}`,
        deleteSelectedIngredients: `{{__('Are you sure you want to delete selected ingredients?')}}`,
        ingredientNameRequired: `{{__('Ingredient name is required')}}`,
        ingredientCostRequired: `{{__('Ingredient cost is required')}}`,
        ingredientPriceRequired: `{{__('Ingredient price is required')}}`,
        ingredientUnitRequired: `{{__('Ingredient unit is required')}}`,
        ingredientQuantityRequired: `{{__('Ingredient quantity is required')}}`,
        ingredientAlertRequired: `{{__('Ingredient alert quantity is required')}}`,
        ingredientSuccessfullyAdded: `{{__('The ingredient has been successfully added!')}}`,
        selectedLanguages: `{{__('Are you sure you want to delete selected languages?')}}`,
        selectedLanguagesNotDeleted: `{{__('Selected languages was not deleted.')}}`,
        languageName: `{{__('Language name is required')}}`,
        localeName: `{{__('Locale is required')}}`,
        languageAdded: `{{__('Language has been successfully added!')}}`,
        selectedMeal: `{{__('Selected deal was not deleted')}}`,
        deleteSelectedDeal: `{{__('Are you sure you want to delete selected deals?')}}`,
        dealName: `{{__('Deal name is required')}}`,
        productName: `{{__('Food items are required')}}`,
        dealSuccessfullyAdded: `{{__('The deal has been successfully added!')}}`,
        selectedeKiosk: `{{__('Selected e kiosks was not deleted.')}}`,
        dealSuccessfullyUpdated: `{{__('The deal has been successfully updated!')}}`,
        selectedModifiers: `{{__('Selected modifiers was not deleted.')}}`,
        deleteModifiers: `{{__('Are you sure you want to delete selected modifiers?')}}`,
        modifierTitle: `{{__('Modifier title is required')}}`,
        modifierAdded: `{{__('The modifier has been successfully added!')}}`,
        modifierUpdated: `{{__('The modifier has been successfully updated!')}}`,
        foodItemUpdated: `{{__('The product has been successfully updated!')}}`,
        locationName: `{{__('Location name is required')}}`,
        locationAddress: `{{__('Location address is required')}}`,
        createButton: `{{__('Ok, create it!')}}`,
        locationAdded: `{{__('Location has been successfully added!')}}`,
        paymentRequired: `{{__('Payment method name is required')}}`,
        selectedQueue: `{{__('Selected queue was not deleted.')}}`,
        deleteQueue: `{{__('Are you sure you want to delete selected queue managements?')}}`,
        queueName: `{{__('Queue name is required')}}`,
        urlRequired: `{{__('URL is required')}}`,
        queueIdRequired: `{{__('ID is required')}}`,
        deleteSelectedStockAlerts: `{{__('Are you sure you want to delete selected stock alerts?')}}`,
        selectedStockAlert: `{{__('Selected stockAlert was not deleted.')}}`,
        selectedReports: `{{__('Selected reports were not deleted.')}}`,
        deleteReports: `{{__('Are you sure you want to delete selected z reports?')}}`,
        deleteSelectedOrders: `{{__('Are you sure you want to delete selected orders?')}}`,
        selectedOrders: `{{__('Selected orders were not deleted.')}}`,
        tableName: `{{__('Table name is required')}}`,
        tableAdded: `{{__('The table has been successfully added!')}}`,
        appUrl: `{{__('App url is required')}}`,
        appName: `{{__('App name is required')}}`,
        generalSettingsUpdated: `{{__('General setting has been successfully updated!')}}`,
        logoUpdated: `{{__('Logo has been successfully updated!')}}`,
        selectedAsset: `{{__('Selected asset was not deleted.')}}`,
        cashRegister: `{{__('Cash register is required')}}`,
        systemAssetAdded: `{{__('System asset position has been successfully added!')}}`,
        taxRate: `{{__('Tax rate is required')}}`,
        taxId: `{{__('Tax id is required')}}`,
        taxAdded: `{{__('The Tax has been successfully added!')}}`,
        unitName: `{{__('Unit name is required')}}`,
        unitSuffix: `{{__('Suffix is required')}}`,
        unitAdded: `{{__('The Unit has been successfully added!')}}`,
        userRoloeAdded: `{{__('User role has been successfully added!')}}`,
        userRoleRequired: `{{__('User role name is required')}}`,
        selectedUsers: `{{__('Selected users was not deleted.')}}`,
        deleteSelectedUsers: `{{__('Are you sure you want to delete selected users?')}}`,
        userRequired: `{{__('User name is required')}}`,
        userEmail: `{{__('User email is required')}}`,
        valueEmail: `{{__('The value is not a valid email address')}}`,
        userRoleIsRequired: `{{__('User role is required')}}`,
        downloadZReport: `{{__('Are you sure you want to download this report?')}}`,
        confirmDownloadText: `{{__('Yes, download!')}}`,
        printerSettingsDeviceNameRequired: `{{__('Device name is required')}}`,
        printerSettingsDeviceIpRequired: `{{__('Device ip is required')}}`,
        printerSettingsDevicePortRequired: `{{__('Device port is required')}}`,
        printerSettingsDeviceTypeRequired: `{{__('Device type is required')}}`,
        printerSettingsSuccessfullyAdded: `{{__('The printer has been successfully added!')}}`,
        selectedPrinterSettings: `{{__('Selected printers was not deleted')}}`,
        deletePrinterSettings: `{{__('Are you sure you want to delete selected printers?')}}`,
        restoreText: `{{__('Are you sure you want to restore')}}`,
        confirmRestoreButtonText: `{{__('Yes, restore!')}}`,
        backupHasBeenRestored: `{{__('has been restored!')}}`,
        notRestored: `{{__('was not restored!')}}`,
        jan: `{{__('Jan')}}`,
        feb: `{{__('Feb')}}`,
        mar: `{{__('Mar')}}`,
        apr: `{{__('Apr')}}`,
        may: `{{__('May')}}`,
        jun: `{{__('Jun')}}`,
        jul: `{{__('Jul')}}`,
        aug: `{{__('Aug')}}`,
        sep: `{{__('Sep')}}`,
        oct: `{{__('Oct')}}`,
        now: `{{__('Now')}}`,
        dec: `{{__('Dec')}}`,
        queueManagementSuccessfully: `{{__('Queue management has been successfully added!')}}`,
        userSuccess: `{{__('User has been successfully added!')}}`,
        yes: `{{__('Yes!')}}`,
        no: `{{__('No!')}}`,
        askReleaseDevice: `{{__('Do you want to release this device from the cash register or eKiosk?')}}`,
        yesClear: `{{__('Yes, clear!')}}`,
        notCleaned: `{{__('has not been cleared.')}}`,
        refunded: `{{__('Refunded')}}`,
        completed: `{{__('Completed')}}`,
        inProgress: `{{__('In Progress')}}`,
        waiting: `{{__('Waiting')}}`,
        kitchenValidationName: `{{__('Kitchen name is required')}}`,
        kitchenValidationId: `{{__('Kitchen id is required')}}`,
        kitchen: `{{__('Kitchen has been successfully added!')}}`,
    }
</script>
@yield('masterContent')
@yield('page-script')
</body>

</html>
