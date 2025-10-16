<?php


use App\Http\Controllers\VerifyKeyController;
use Illuminate\Support\Facades\Route;

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
Route::get('/', [VerifyKeyController::class, 'verifyKeyIndex'])->name('create.verify.key.index');
Route::post('login/verifyKey', [VerifyKeyController::class, 'verifyKeyPost'])->name('login.verify.key.post');

Route::get('create/account', [VerifyKeyController::class, 'createAccountIndex'])->name('create.account.index');
Route::post('create/account', [VerifyKeyController::class, 'createAccountPost'])->name('create.account.post');

Route::get('/key/checked', function (){
    return 'U2FsdGVkX18QXeWLV3smJf5ifiUCr4px0CBp0gbRiZEZfBhW88irMr6ln4XMpblZ';
});



