<?php

use App\Http\Controllers\Api\EKioskController;
use App\Http\Controllers\Api\PosController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KitchenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/categories', [EKioskController::class, 'categories']);
    Route::get('/products/{categoryId}', [EKioskController::class, 'products']);
    Route::get('/deals', [EKioskController::class, 'deals']);
    Route::get('/modifiers/{categoryId}', [EKioskController::class, 'modifiers']);
    Route::get('/paymentMethods', [EKioskController::class, 'paymentMethods']);
    Route::get('/languages', [EKioskController::class, 'languages']);
    Route::get('/languages/{languageId}', [EKioskController::class, 'language']);
    Route::get('/tax/{type}', [EKioskController::class, 'tax']);
    Route::get('/getAsset', [EKioskController::class, 'asset']);
    Route::get('/products', [EKioskController::class, 'allProducts']);
    Route::get('/currency', [EKioskController::class, 'getCurrency']);
    Route::get('/allModifiers', [EKioskController::class, 'allModifiers']);
    Route::get('/printDevices', [EKioskController::class, 'devices']);
    Route::get('/defaultLanguage', [EKioskController::class, 'defaultLanguage']);
    Route::post('/assignDevices', [EKioskController::class, 'assignDevices']);
    Route::post('/logs', [EKioskController::class, 'logs']);
    Route::post('/settings/login', [EKioskController::class, 'loginSettings']);
    Route::post('/save/order', [EKioskController::class, 'store']);
    Route::post('/logout', [EKioskController::class, 'logout']);
});
Route::post('/register', [EKioskController::class, 'register']);
Route::post('/login', [EKioskController::class, 'login']);

// Routes for kitchen
Route::prefix('kitchen')->group(function () {
    Route::post('/device/register', [KitchenController::class, 'deviceKitchenRegister']);
    Route::post('/device/login', [KitchenController::class, 'deviceKitchenLogin']);
    Route::middleware(['device.token:kitchen'])->group(function () {
        Route::get('users', [KitchenController::class, 'users']);
        Route::post('device/logout', [KitchenController::class, 'logout'])->name('kitchen.device.logout');
        Route::post('/user/login', [KitchenController::class, 'userKitchenLogin']);
        Route::middleware(['auth:sanctum', 'ability:kitchen'])->group(function () {
            Route::post('user/logout', [KitchenController::class, 'logout'])->name('kitchen.user.logout');
            Route::get('/orders', [KitchenController::class, 'orderKitchen']);
            Route::get('/list', [KitchenController::class, 'kitchenList']);
            Route::post('/assignToMe', [KitchenController::class, 'assignToMe']);
            Route::post('/prepare/item', [KitchenController::class, 'prepareItem']);
            Route::post('/confirmOrder', [KitchenController::class, 'confirmOrder']);
            Route::get('/settings', [KitchenController::class, 'settings']);
            Route::post('/approveCancelKitchen', [KitchenController::class, 'approveCancelKitchen']);
            Route::post('/printKitchenOrder', [KitchenController::class, 'printKitchenOrder']);
            Route::post('/printKitchenItem', [KitchenController::class, 'printKitchenItem']);
            Route::post('/assignDeviceInKitchen', [KitchenController::class, 'assignDeviceInKitchen']);
            Route::post('/test', [KitchenController::class, 'test']);
        });
    });
});

Route::prefix('pos')->group(function () {
    Route::post('/device/register', [PosController::class, 'devicePosRegister']);
    Route::post('/device/login', [PosController::class, 'devicePosLogin']);
    Route::middleware(['device.token:pos'])->group(function () {
        Route::get('/users', [PosController::class, 'users']);
        Route::post('/user/login', [PosController::class, 'userPosLogin']);
        Route::post('/device/logout', [PosController::class, 'logout'])->name('pos.device.logout');
        Route::middleware(['auth:sanctum', 'ability:pos'])->group(function () {
            Route::post('/user/logout', [PosController::class, 'logout'])->name('pos.user.logout');
        });
    });
});

