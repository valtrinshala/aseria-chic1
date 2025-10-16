<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerScreenController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EKioskAssetController;
use App\Http\Controllers\EKioskAssetPositionController;
use App\Http\Controllers\EKioskController;
use App\Http\Controllers\EKioskIncomingRequest;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\FoodCategoryController;
use App\Http\Controllers\FoodItemController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\KitchenAdminController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\ModifierController;
use App\Http\Controllers\OurLocationController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PosAdminController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\PrinterSettingsController;
use App\Http\Controllers\QueueManagementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SeederController;
use App\Http\Controllers\ServiceTableController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SystemAssetController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use App\Models\ZReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/test', function (){
    if (auth()->check()) {
        return redirect()->route('dashboard');
    } else {
        return view('auth.login');
    }
});

Route::group(['prefix' => "auth"], function () {
    Route::get('login', [AuthController::class, 'loginIndex'])->name('login.index');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('pos/login', [AuthController::class, 'loginPos'])->name('login.pos');
    Route::get('kitchen/login', [AuthController::class, 'loginKitchen'])->name('login.kitchen');
});

Route::get('/queue/{locationName}/{eKioskKey}', [QueueManagementController::class, 'queueResults'])->name('queue.results');
Route::get('/queue/ajax/{queueId}/{locationId}', [QueueManagementController::class, 'queueResultsAjax'])->name('queue.results.ajax');
Route::post('/check/eKiosk/pin', [QueueManagementController::class, 'checkPinForEKiosk'])->name('check.eKiosk.pin');

Route::get('/cs', [CustomerScreenController::class, 'index']);
Route::get('/cs/getImage', [CustomerScreenController::class, 'getImage']);
Route::post('/setDataCS', [CustomerScreenController::class, 'setJsonForCustomerScreen']);
Route::get('/getDataCS', [CustomerScreenController::class, 'getJsonForCustomerScreen']);
Route::post('/removeDataCS', [CustomerScreenController::class, 'clearJsonForCustomerScreen']);

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::group(["prefix" => "admin"], function () {
        Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen.index')->middleware('permissions:kitchen_module');
        Route::get('/kitchen/orders', [KitchenController::class, 'orderKitchen'])->middleware('permissions:kitchen_module');
        Route::get('/kitchen-list', [KitchenController::class, 'kitchenList'])->name('kitchen.list')->middleware('permissions:kitchen_history_view_module');
        Route::post('/prepare/item', [KitchenController::class, 'prepareItem'])->middleware('permissions:kitchen_check_module');
        Route::post('/assignToMe', [KitchenController::class, 'assignToMe'])->middleware('permissions:kitchen_assign_orders_module');
        Route::post('/confirmOrder', [KitchenController::class, 'confirmOrder'])->middleware('permissions:kitchen_confirm_orders_module');
        Route::post('/approveCancelKitchen', [KitchenController::class, 'approveCancelKitchen'])->middleware('permissions:kitchen_module');
//        Route::get('/printKitchenOrder', [KitchenController::class, 'printKitchenOrder'])->middleware('permissions:kitchen_print_orders_slips_module');
        Route::get('/printKitchenItem', [KitchenController::class, 'printKitchenItem'])->middleware('permissions:kitchen_print_item_stickers_module');
        Route::get('/kitchen/settings', [KitchenController::class, 'settings'])->name('kitchen.settings')->middleware('permissions:kitchen_settings_module');
        Route::post('/assignDeviceInKitchen', [KitchenController::class, 'assignDeviceInKitchen'])->middleware('permissions:kitchen_module');

        Route::middleware(['permissions:manage_languages'])->group(function () {
            Route::resource('language', LanguageController::class);
            Route::post('language/delete/selected', [LanguageController::class, 'deleteSelectedItems']);
        });
        Route::get('set/{language}/storage', [LanguageController::class, 'setLanguageInStorage'])->name('set.language.storage');

        // Sales pos
        Route::middleware(['permissions:create_new_sales'])->group(function () {
            Route::resource('order', SaleController::class);
        });

        // Order sys mgmt
        Route::get('order', [SaleController::class, 'index'])->name('order.index')->middleware('permissions:manage_orders_mgmt');
        Route::get('order/{order}', [SaleController::class, 'show'])->name('order.show')->middleware('permissions:manage_orders_mgmt');
        Route::post('order/delete/selected', [SaleController::class, 'deleteSelectedItems'])->middleware('permissions:manage_orders_mgmt');
        Route::get('order/all/OrderByAllFilters', [SaleController::class, 'getOrdersByAllFilters'])->middleware('permissions:manage_orders_mgmt');

        Route::delete('order/{order}', [SaleController::class, 'destroy'])->name('order.destroy');

        Route::middleware(['permissions:queue_management_module'])->group(function () {
            Route::resource('queueManagement', QueueManagementController::class);
            Route::post('queueManagement/delete/selected', [QueueManagementController::class, 'deleteSelectedItems']);
        });

        Route::middleware(['permissions:manage_e_kiosk'])->group(function () {
            Route::resource('eKiosk', EKioskController::class);
            Route::post('eKiosk/delete/selected', [EKioskController::class, 'deleteSelectedItems']);
        });
        Route::middleware(['permissions:manage_e_kiosk_asset'])->group(function () {
            Route::resource('eKioskAsset', EKioskAssetController::class);
            Route::post('eKioskAsset/delete/selected', [EKioskAssetController::class, 'deleteSelectedItems']);
        });
        Route::middleware(['permissions:manage_e_kiosk_position'])->group(function () {
            Route::resource('eKioskAssetPosition', EKioskAssetPositionController::class);
            Route::post('eKioskAssetPosition/delete/selected', [EKioskAssetPositionController::class, 'deleteSelectedItems']);
        });
        Route::middleware(['permissions:manage_e_kiosk_incoming_request'])->group(function () {
            Route::get('incomingRequests', [EKioskIncomingRequest::class, 'incomingRequests']);
            Route::delete('incomingRequest/{incomingRequest}', [EKioskIncomingRequest::class, 'destroy']);
        });

        Route::middleware(['permissions:manage_kitchen'])->group(function () {
            Route::resource('kitchen-android/devices', KitchenAdminController::class);
            Route::post('kitchen-android/devices/delete/selected', [KitchenAdminController::class, 'deleteSelectedItems']);
        });

        Route::middleware(['permissions:manage_kitchen_incoming_requests'])->group(function () {
            Route::get('kitchen-android/incomingRequests', [KitchenAdminController::class, 'kitchenIncomingRequests']);
            Route::delete('kitchen-android/incomingRequests/{incomingRequest}', [KitchenAdminController::class, 'destroyIncomingRequests']);
        });

        Route::middleware(['permissions:manage_pos'])->group(function () {
            Route::resource('pos-android/devices-pos', PosAdminController::class);
            Route::post('pos-android/devices-pos/delete/selected', [PosAdminController::class, 'deleteSelectedItems']);
        });

        Route::middleware(['permissions:manage_pos_incoming_requests'])->group(function () {
            Route::get('pos-android/incomingRequests', [PosAdminController::class, 'posIncomingRequests']);
            Route::delete('pos-android/incomingRequests/{incomingRequest}', [PosAdminController::class, 'destroyIncomingRequests']);
        });

        Route::middleware(['permissions:manage_system_assets'])->group(function () {
            Route::resource('systemAsset', SystemAssetController::class);
            Route::post('systemAsset/delete/selected', [SystemAssetController::class, 'deleteSelectedItems']);
        });

        Route::middleware(['permissions:manage_deals'])->group(function () {
            Route::resource('meal', MealController::class);
            Route::post('meal/delete/selected', [MealController::class, 'deleteSelectedItems']);
        });

        Route::middleware(['permissions:manage_food_categories'])->group(function () {
            Route::resource('foodCategory', FoodCategoryController::class);
            Route::post('foodCategory/delete/selected', [FoodCategoryController::class, 'deleteSelectedItems']);
        });
        Route::middleware(['permissions:manage_food_items'])->group(function () {
            Route::resource('foodItem', FoodItemController::class);
            Route::post('foodItem/delete/selected', [FoodItemController::class, 'deleteSelectedItems']);
        });
        Route::middleware(['permissions:manage_ingredients'])->group(function () {
            Route::resource('ingredient', IngredientController::class);
            Route::post('ingredient/delete/selected', [IngredientController::class, 'deleteSelectedItems']);
        });
        Route::middleware(['permissions:manage_modifiers'])->group(function () {
            Route::resource('modifier', ModifierController::class);
            Route::post('modifier/delete/selected', [ModifierController::class, 'deleteSelectedItems']);
        });
        Route::middleware(['permissions:manage_users'])->group(function () {
            Route::resource('user', UserController::class);
            Route::post('user/delete/selected', [UserController::class, 'deleteSelectedItems']);
        });
        Route::middleware(['permissions:manage_user_roles'])->group(function () {
            Route::resource('userRole', UserRoleController::class);
            Route::post('userRole/delete/selected', [UserRoleController::class, 'deleteSelectedItems']);
        });
        Route::middleware(['permissions:manage_customers'])->group(function () {
            Route::resource('customer', CustomerController::class);
            Route::post('customer/delete/selected', [CustomerController::class, 'deleteSelectedItems']);
        });

        Route::get('report', [ReportController::class, 'reportIndex'])->middleware('permissions:manage_overall_reports');
        Route::get('taxReport', [ReportController::class, 'taxReportIndex'])->middleware('permissions:manage_tax_reports');
        Route::get('zReport', [ReportController::class, 'zReportIndex'])->middleware('permissions:manage_z_reports');
        Route::get('report/excel', [ExcelController::class, 'ZReportExportExcel'])->middleware('permissions:print_overall_reports');
        Route::get('/zReport/all/zReportByDate', [ReportController::class, 'zReportByDate'])->middleware('permissions:manage_z_reports');
        Route::get('stockAlert', [ReportController::class, 'stockAlertIndex'])->middleware('permissions:manage_stock_alerts');
        Route::get('filters', [ReportController::class, 'filters'])->middleware('permissions:manage_overall_reports');
        Route::get('printStockAlert', [PrintController::class, 'printStockAlert'])->middleware('permissions:manage_stock_alerts');
        Route::get('printOverall', [PrintController::class, 'printOverall'])->middleware('permissions:print_overall_reports');
        Route::get('pdfOverall', [PdfController::class, 'pdfOverall'])->name('pdf.reports')->middleware('permissions:print_overall_reports');
        Route::get('pdf/zReport/{zReport}', [PdfController::class, 'pdfZReport'])->name('print.zReport')->middleware('permissions:download_z_report');

        Route::middleware(['permissions:manage_service_tables'])->group(function () {
            Route::resource('serviceTable', ServiceTableController::class);
        });
        Route::middleware(['permissions:manage_payment_methods'])->group(function () {
            Route::resource('paymentMethod', PaymentMethodController::class);
        });
        Route::middleware(['permissions:manage_settings'])->group(function () {
            Route::group(["prefix" => "setting"], function () {
                Route::get('/', [SettingController::class, 'index'])->name('settings');
                Route::get('general', [SettingController::class, 'getGeneral'])->name('settings.get.general')->middleware('permissions:general_configuration');
                Route::post('general', [SettingController::class, 'setGeneral'])->name('settings.set.general')->middleware('permissions:general_configuration');
                Route::get('locationsForAdmin', [SettingController::class, 'locationsForAdmin'])->name('settings.set.location.for.admin');
                Route::resource('ourLocation', OurLocationController::class)->middleware('permissions:locations_access');
                Route::get('ourLocation/check/taxes', [OurLocationController::class, 'checkTaxesForLocation'])->middleware('permissions:locations_access');
                Route::get('appearance', [SettingController::class, 'getAppearance'])->name('settings.get.appearance')->middleware('permissions:appearance_configuration');
                Route::post('appearance', [SettingController::class, 'setAppearance'])->name('settings.set.appearance')->middleware('permissions:appearance_configuration');
                Route::get('client/appearance', [SettingController::class, 'getClientAppearance'])->name('settings.get.client.appearance')->middleware('permissions:client_appearance_configuration');
                Route::post('client/appearance', [SettingController::class, 'setClientAppearance'])->name('settings.set.client.appearance')->middleware('permissions:client_appearance_configuration');
                Route::get('currency', [SettingController::class, 'getCurrency'])->name('settings.get.currency')->middleware('permissions:currency_configuration');
                Route::post('currency', [SettingController::class, 'setCurrency'])->name('settings.set.currency')->middleware('permissions:currency_configuration');
                Route::resource('tax', TaxController::class)->middleware('permissions:tax_configuration');
                Route::resource('unit', UnitController::class)->middleware('permissions:unit_configuration');
                Route::get('authentication', [SettingController::class, 'getAuthentication'])->name('settings.get.authentication');
                Route::post('authentication', [SettingController::class, 'setAuthentication'])->name('settings.set.authentication');
                Route::get('captcha', [SettingController::class, 'getCaptcha'])->name('settings.get.captcha')->middleware('permissions:captcha_configuration');
                Route::post('captcha', [SettingController::class, 'setCaptcha'])->name('settings.set.captcha')->middleware('permissions:captcha_configuration');
                Route::get('localization', [SettingController::class, 'getLocalization'])->name('settings.get.localization')->middleware('permissions:localization_configuration');
                Route::get('invoicePrinting', [SettingController::class, 'getInvoicePrinting'])->name('settings.get.invoicePrinting')->middleware('permissions:printer_configuration');
                Route::post('invoicePrinting', [SettingController::class, 'setInvoicePrinting'])->name('settings.set.invoicePrinting')->middleware('permissions:printer_configuration');
                Route::get('helpContacts', [SettingController::class, 'getHelpContacts'])->name('settings.get.helpContacts')->middleware('permissions:contact_helper_configuration');
                Route::post('helpContacts', [SettingController::class, 'setHelpContacts'])->name('settings.set.helpContacts')->middleware('permissions:contact_helper_configuration');
                Route::resource('printerSettings', PrinterSettingsController::class)->middleware('permissions:printer_settings_configuration');
                Route::post('printerSettings/delete/selected', [PrinterSettingsController::class, 'deleteSelectedItems'])->middleware('permissions:printer_settings_configuration');
                Route::post('clearDeviceFromCashRegisterOrEKiosk/{device}', [PrinterSettingsController::class, 'clearDeviceFromCashRegisterOrEKiosk'])->middleware('permissions:printer_settings_configuration');
            });
        });
        Route::get('getBase64Image', [SettingController::class, 'getBase64Image']);

        //Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('permissions:dashboard_access');
        Route::get('dashboard/states', [DashboardController::class, 'states'])->name('get.data')->middleware('permissions:dashboard_access');


        Route::get('/pos/locationCashRegisterPos', [PosController::class, 'locationsCashRegisterForPos'])->name('set.location.cashRegister.pos');

        Route::middleware(['permissions:pos_module'])->group(function () {
            Route::group(["prefix" => "pos"], function () {
                Route::get('/', [PosController::class, 'index'])->name('pos.index')->middleware('permissions:pos_home_module');
                Route::get('/kitchen', [PosController::class, 'kitchen'])->name('pos.kitchen')->middleware('permissions:pos_kitchen_module');
                Route::get('/kitchen/orders', [KitchenController::class, 'orderKitchen'])->middleware('permissions:pos_kitchen_module');
                Route::get('/ready', [PosController::class, 'ready'])->name('pos.ready')->middleware('permissions:pos_ready_view_module');
                Route::get('/readyOrders', [PosController::class, 'orderReady'])->middleware('permissions:pos_ready_view_module');
                Route::get('/eKiosk', [PosController::class, 'eKiosk'])->name('pos.eKiosk')->middleware('permissions:pos_e_kiosk_module');
                Route::get('/eKioskOrders', [PosController::class, 'eKioskOrders'])->middleware('permissions:pos_e_kiosk_module');
                Route::get('/history', [PosController::class, 'history'])->name('pos.history')->middleware('permissions:pos_history_view_module');
                Route::get('/settings', [PosController::class, 'settings'])->name('pos.settings')->middleware('permissions:pos_settings_module');
                Route::post('/settings', [PosController::class, 'settings'])->name('pos.settings.post')->middleware('permissions:pos_settings_module');
                Route::post('/ack-order', [PosController::class, 'acknowledgeOrder'])->name('pos.acknowledge');
                Route::get('/saveOrder', [PosController::class, 'getSaveOrder'])->name('get.save.order');
                Route::post('/createZReport', [PosController::class, 'createZReport'])->name('create.zReport')->middleware('permissions:pos_z_report_module');
                Route::post('/endZReport', [PosController::class, 'endZReport'])->name('end.zReport')->middleware('permissions:pos_end_z_report_module');
                Route::get('/printXReport', [PosController::class, 'printXReport'])->middleware('permissions:pos_x_report_module');//ajax
                Route::get('/printPosOrder', [PosController::class, 'printPosOrder'])->middleware('permissions:pos_print_order_slip');//ajax
                Route::post('/cancelOrder', [PosController::class, 'cancelOrder'])->name('cancel.order')->middleware('permissions:cancel_order_module');
                Route::get('/getMeals', [PosController::class, 'getMeals']); //ajax
                Route::get('/getTables', [PosController::class, 'getTables']); //ajax
                Route::get('/get/products/{foodCategory}', [PosController::class, 'getProducts']); //ajax
                Route::post('/get/products', [PosController::class, 'getProductsFromCategories']); //ajax
                Route::get('/get/product/ids',[ PosController::class, 'getProductsIds']); //ajax
                Route::get('/getClosingAmount', [PosController::class, 'getClosingAmount']);//ajax
                Route::get('/search', [PosController::class, 'search']);//ajax
                Route::post('/changeCashRegister', [PosController::class, 'changeCashRegisterPos'])->name('change.cash.register');//ajax
                Route::post('/assignDeviceInPos', [PosController::class, 'assignDeviceInPos']);
                Route::post('/queReady', [PosController::class, 'queReady']);
                Route::get('/printLastZReport', [PosController::class, 'printLastZReport']);
                Route::get('/pinToPrintReports', [PosController::class, 'pinToPrintReports']);
            });
        });
        Route::middleware(['permissions:cash_register_module'])->group(function () {
            Route::resource('cashRegister', CashRegisterController::class);
            Route::post('cashRegister/delete/selected', [CashRegisterController::class, 'deleteSelectedItems']);
        });
        Route::get('/backup', [BackupController::class, 'index'])->middleware('permissions:manage_backup'); //ajax
        Route::get('/generateBackup', [BackupController::class, 'generateBackup'])->name('generate.backup')->middleware('permissions:generate_backup');
        Route::get('/downloadBackup/{filename}', [BackupController::class, 'downloadBackup'])->name('download.backup')->middleware('permissions:download_backup');
        Route::post('/deleteBackup/{filename}', [BackupController::class, 'deleteBackup'])->name('delete.backup')->middleware('permissions:delete_backup');
        Route::post('/restoreBackup/{filename}', [BackupController::class, 'restoreBackup'])->name('restore.backup')->middleware('permissions:restore_backup');
    });
    Route::post('/logs', [PosController::class, 'logs']);
  Route::get('/logs/ekiosk', function (){

    try {
	        if (!(auth()->user()->role_id == config('constants.role.adminId'))) {
            return response()->json(['message' => 'Failed to retrieve log.'], 500);
        }
        $date = \Illuminate\Support\Carbon::now()->format('Y-m-d');
        $filename = "terminal_logs/e_kiosk_$date.txt";

        if (Storage::disk('local')->exists($filename)) {
            $logContents = Storage::disk('local')->get($filename);
            return response($logContents, 200)
                ->header('Content-Type', 'text/plain');
        } else {
            return response()->json(['message' => 'Log file not found.'], 404);
        }
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to retrieve log.'], 500);
    }
});

Route::get('/logs/pos', function (){
    try {
	        if (!(auth()->user()->role_id == config('constants.role.adminId'))) {
            return response()->json(['message' => 'Failed to retrieve log.'], 500);
        }
        $date = \Illuminate\Support\Carbon::now()->format('Y-m-d');
        $filename = "terminal_logs/pos_$date.txt";

        if (Storage::disk('local')->exists($filename)) {
            $logContents = Storage::disk('local')->get($filename);
            return response($logContents, 200)
                ->header('Content-Type', 'text/plain');
        } else {
            return response()->json(['message' => 'Log file not found.'], 404);
        }
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to retrieve log.'], 500);
    }
});
});



Route::get('/test', function (Request $request) {

//    dd(\App\Models\Meal::first(), \App\Models\FoodItem::first());
    $inject = new \App\Services\SaleInvoice(\App\Models\Sale::latest()->first());
    $printData = $inject->getDataForPrinter();
    $printString = $inject->prepareString($printData);
        dd($printString);
//    dd($inject->prepareString($inject->getXReportDataForPrinter()));
//    $inject = new \App\Services\ZReportInvoice(\App\Models\ZReport::first());
//    $printData = $inject->getZReportDataForPrinter();
//    dd($printData);
});

Route::get('/apk_direction', function(Request $request) {
    echo '
        <style>
            body {
                font-size: 5rem;
            }

            #backplur {
                position: fixed;
                inset: 0;
                background: #2D1A5B !important;
            }
        </style>
        <a href="#" id="pos">POS</a>
        <a href="#" id="kitchen">Kitchen</a>
        <div id="backplur"></div>
        <script>
            const posDir = "/auth/pos/login";
            const kitchenDir = "/auth/kitchen/login"
            const dir = localStorage.getItem("dir_apk");
            if (dir) {
                if (dir == "pos") {
                    location.href = posDir;
                }

                if (dir == "kitchen") {
                    location.href = kitchenDir;
                }
            } else {
                document.getElementById("backplur").remove();
            }

            setTimeout(() => {
                document.getElementById("backplur").remove();
            }, 7000);

            document.getElementById("pos").addEventListener("click", e => {
                e.preventDefault();

                localStorage.setItem("dir_apk", "pos");
                location.href = posDir;

                return false;
            })

            document.getElementById("kitchen").addEventListener("click", e => {
                e.preventDefault();

                localStorage.setItem("dir_apk", "kitchen");
                location.href = kitchenDir;

                return false;
            })

        </script>
    ';
});

Route::get('/terminal_test', function (Request $request) {
    echo "
        <button style=\"width: 100vw; height: 3rem;\" onclick=\"start()\">Start discovery</button>
        <p id='messages'></p>
        <script>
            function start() {
                if ('Mine' in window) {
                    window.Mine.postMessage('pts:');
                } else {
                    alert('Primary issue with the apk, does not have Mine');
                }
            }

            function terminal_messages(msg) {
                let el = document.createElement('div');
                el.innerHTML = msg;

                document.getElementById('messages').appendChild(el);
            }
        </script>
    ";
});


Route::get('/terminal_test', function (Request $request) {
    echo "
        <div style=\"display: flex;\">
            <input style=\"height: 3rem;flex-grow:1;\" type=\"text\" id=\"terminal_ip\" placeholder=\"Terminal IP\">
            <input style=\"height: 3rem;flex-grow:1;\" type=\"text\" id=\"amount\" placeholder=\"Amount\">
        </div>
        <div style=\"display: flex;\">
            <button style=\"height: 3rem;flex-grow:1;\" onclick=\"step(1)\">Step 1</button>
            <button style=\"height: 3rem;flex-grow:1;\" onclick=\"step(2)\">Step 2</button>
            <button style=\"height: 3rem;flex-grow:1;\" onclick=\"step(3)\">Step 3</button>
            <button style=\"height: 3rem;flex-grow:1;\" onclick=\"step(4)\">Step 4</button>
        </div>
        <div style=\"display: flex\">
            <p style=\"width: 50%; padding-right: 1em; border-right: 1px solid black;\" id='messages'></p>
            <p style=\"width: 50%; padding-left: 1em;\" id='terminal_messages'></p>
        </div>
        <script>
            let debugging = true;
            function step(stepping) {
                const ip = document.getElementById('terminal_ip').value;
                const amount = document.getElementById('amount').value;
                console.log(`test_terminal:\${stepping}:\${ip}:\${amount}:USD`);
                if ('Mine' in window) {
                    window.Mine.postMessage(`test_terminal:\${stepping}:\${ip}:\${amount}:USD`);
                } else {
                    alert('You must be in the apk to do that');
                }

                if (debugging) {
                    if (stepping == 1) {
                       if(typeof(term_logs) == 'function') { term_logs(\"Starting discovery for the terminals\") }
                       if(typeof(terminal_messages) == 'function') { terminal_messages(`Connected:<br>\${ip}`) }
                       if(typeof(term_logs) == 'function') { term_logs(\"Found terminals are shown, if the terminal is listed just proceed to next step\") }
                    }

                    if (stepping == 2) {
                        if(typeof(term_logs) == 'function') { term_logs(\"Selecting specific terminal based on ip\") }
                        if(typeof(term_logs) == 'function') { term_logs(\"Test terminal with ip: \" + ip + \" was found proceed with next step\") }
                    }

                    if (stepping == 3) {
                        if(typeof(term_logs) == 'function') { term_logs(\"Starting the payment process\") }
                        if(typeof(term_messages) == 'function') { term_messages(\"Card 12321<br>Yes<br>2121<br>139210192501\") }
                    }

                    if (stepping == 4) {
                        if(typeof(term_messages) == 'function') { term_messages(\"Card 12321<br>Yes<br>2121<br>139210192501\") }
                    }

                }
            }

            window.term_logs = (message) => {
                document.getElementById('messages').innerHTML += '<br>' + message;
            }

            window.terminal_messages = (message) => {
                document.getElementById('messages').innerHTML += '<br>' + message;
            }

            window.term_messages = (messages) => {
                document.getElementById('terminal_messages').innerHTML = messages;
            }
        </script>
    ";
});



Route::get('/printer_test', function (Request $request) {
    $inject = new \App\Services\XReportInvoice(ZReport::first());
    $printData = $inject->prepareString($inject->getXReportDataForPrinter());
    echo "
        <button style=\"width: 100%;height: 3rem;flex-grow:1;\" onclick=\"connect()\">Manual connect</button>
        <div style=\"display: flex;\">
        <button style=\"height: 3rem;flex-grow:1;\" onclick=\"start(1)\">Test 1</button>
        <button style=\"height: 3rem;flex-grow:1;\" onclick=\"start(2)\">Test 2</button>
        <button style=\"height: 3rem;flex-grow:1;\" onclick=\"start(3)\">Test 3</button>
        <button style=\"height: 3rem;flex-grow:1;\" onclick=\"start(4)\">Test 4</button>
        <button style=\"height: 3rem;flex-grow:1;\" onclick=\"start(5)\">Test 5</button>
        <button style=\"height: 3rem;flex-grow:1;\" onclick=\"start(5)\">Test 6</button>
        </div>
        <p id='messages'></p>
        <script>

// Function for other javascript functions
window.invoicePrinting = (string, item) => {
    printInvoice(string, item);
}

function loadImage(url) {
    return new Promise((resolve, reject) => {
        const image = new Image();
        image.onload = () => { resolve(image); }
        image.onerror = () => { resolve(0); }
        image.src = url;
    })
}

async function printInvoice(printString, item) {
    let settings = {
        logo: {
            height: 150
        },
        spacing: {
            height: 10
        },
        font: {
            size: 24,
            family: 'consolas',
            spacing: 24,
            last: 24
        },
        padding: {
            top: 50,
            left: 0,
            right: 0,
            bottom: 200
        }
    }

    let renderings = {
        /*

        LOGO: function(context, data) {
            return new Promise(async(resolve, reject) => {
                const image = await loadImage(data);
                if (image != 0) {
                    context.filter = 'grayscale(1)';

                    let imageHeight = settings.logo.height;
                    const imageWidth =  image.width + (imageHeight - image.height);

                    let x = (context.can.width * .5) - (imageWidth * .5);
                    context.drawImage(image, 0, 0, image.width, image.height, x, context.currentY, imageWidth, imageHeight);

                    context.currentY += imageHeight + settings.spacing.height;
                }

                resolve(true);
            });
        },

        */

        LR: function(context, amount) {
            // LR Is spacing
            // context.currentY += amount * ;
            context.currentY += parseFloat(amount) * settings.spacing.height;
        },

        R: function(context, data) {
            context.currentY += settings.font.spacing;
        },

        RRL: function(context, text) {
            context.textAlign = 'right';
            context.font = `\${settings.font.size}px \${settings.font.family}`;
            context.fillText(text, context.can.width - settings.padding.right, context.currentY);
        },

        LRL: function(context, text) {
            context.textAlign = 'left';
            context.font = `\${settings.font.size}px \${settings.font.family}`;
            context.fillText(text, settings.padding.left, context.currentY);
        },

        CL: function(context, text) {
            context.textAlign = 'center';
            context.font = `\${settings.font.size}px \${settings.font.family}`;
            context.fillText(text, context.can.width * .5, context.currentY);
            context.currentY += settings.font.spacing;
        }
    }

    const canvas = document.createElement('canvas');

    canvas.width = 550;
    canvas.height = 0;

    canvas.style.width = `\${canvas.width}px`;
    canvas.style.height = `\${canvas.height}px`;
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
        if (i % 2 != 0) context.clearRect(0, 0, canvas.width, canvas.height);

        const string = printString;

        const contexts = string.split(';');


        context.currentY = settings.padding.top;
        for (let con of contexts) {
            let keys = con.split(':');

            const key = keys[0];
            keys.shift();
            const string = keys.join(':');


            if (key in renderings && typeof(renderings[key]) == 'function') {
                await renderings[key](context, string);
            }

        }

        if (settings.font.size != settings.font.last) {
            context.clearRect(0, 0, canvas.width, canvas.height);
            settings.font.last = settings.font.size;
        }

        if (lastY != context.currentY) {

            lastY = context.currentY;
            let lastHeight = context.currentY + settings.padding.bottom;
            canvas.height = lastHeight;
            canvas.style.height = `\${canvas.height}px`;
        }
    };

    let base64 = canvas.toDataURL().split(',');
    base64.shift();
    base64 = base64.join(',')
    if ('Mine' in window) {
        Mine.postMessage('IPI:'+ item +':' + base64);
    }

    document.body.appendChild(canvas);
}


        </script>
        <script>
            function connect() {
                    terminal_messages('Connecting...');
                if ('Mine' in window) {
                    window.Mine.postMessage(\"con:primary:192.168.178.167:9100\");
                }
            }

            function connected(nonce) {
                alert(nonce + ' connected');
            }

            let printString = `" . $printData  . "`;
            function start(item) {
                window.invoicePrinting(printString, item);
            }

            function terminal_messages(msg) {
                let el = document.createElement('div');
                el.innerHTML = msg;

                document.getElementById('messages').appendChild(el);
            }
        </script>
    ";
});


Route::post('/searchurl', function(Request $request) {
    $foodItems = \App\Models\FoodItem::all();
    header('Content-Type: json');
    echo json_encode(['status' => 0, 'data' => $foodItems->jsonSerialize()]);
    return;
});

Route::get('/seed', [SeederController::class, 'seeder']);


Route::get('zReport/month', function (){
    $request = request();
    $cashRegisterId = $request->cash_register_id;
        $month = $request->month;
        $year = $request->year;
        $startDate = null;
        $endDate = null;
        if ($request->date){
            if (str_contains($request->date, 'to')) {
                [$startDate, $endDate] = explode(' to ', $request->date);
                $startDate = Carbon::parse($startDate)->startOfDay();
                $endDate = Carbon::parse($endDate)->endOfDay();
            } else {
                $startDate = Carbon::parse($request->date)->startOfDay();
                $endDate = Carbon::parse($request->date)->endOfDay();
            }
        }

        if ($month || ($startDate && $endDate)){
            $inject = new \App\Services\ZReportInvoicePerMonthService($cashRegisterId, $month, $year, $startDate, $endDate);
            $printData = $inject->getZReportDataForPrinter();
            $printString = $inject->prepareString($printData);
            return response()->json([
                'status' => 0,
                'message' => '',
                'data' => null,
                'redirect_uri' => null,
                'print_order' => $printString
            ]);
        }
});

Route::get('getCashRegisters', function (){
    $cashRegisters = \App\Models\CashRegister::pluck('id')->toArray();
    $validCashRegisters = [];
    foreach ($cashRegisters as $key => $cashRegister){
        $zReports = ZReport::where('cash_register_id', $cashRegister)->where('total_sales', '!=', 0)->get();
        if (count($zReports) > 0) {
            $months = [];
            foreach ($zReports as $zReport){
                if(!in_array(now()::parse($zReport->created_at)->month, $months)){
                    $months[now()::parse($zReport->created_at)->month] = __(now()::parse($zReport->created_at)->format('F'));
                }
            }
            $cR = \App\Models\CashRegister::select('id', 'name', 'key')->find($cashRegister);
            $cR->months = $months;
            $validCashRegisters[] = $cR;
        }
    }
    if ($validCashRegisters){
        return response()->json(['status' => 0, 'data' => $validCashRegisters]);
    }else{
        return response()->json(['status' => 1, 'data' => $validCashRegisters]);
    }
});

Route::get('checkPinToPrintReportsForMonth', function (){
    $request = request();
    if ($request->pin) {
        if (\App\Models\CashRegister::find($request->cash_register_id)?->pin_to_print_reports == $request->pin) {
            return response()->json(['status' => 0]);
        } else {
            return response()->json(['status' => 1, 'message' => __('The pin was incorrect')]);
        }
    }else {
        return response()->json(['status' => 1, 'message' => __('The pin was incorrect')]);
    }
});
