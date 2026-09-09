<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\addons\included\PromocodeController;
use App\Http\Controllers\web\HomeController;

/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|

*/



Route::group(['namespace' => 'admin', 'prefix' => 'admin'], function () {
    Route::group(['middleware' => 'AuthMiddleware'], function () {
        Route::group(['prefix' => 'coupons'], function () {
            Route::get('', [PromocodeController::class, 'index'])->name('coupons');
            Route::get('/add', [PromocodeController::class, 'add']);
            Route::post('/store', [PromocodeController::class, 'save']);
            Route::get('/edit-{id}', [PromocodeController::class, 'edit']);
            Route::post('/update-{id}', [PromocodeController::class, 'update']);
            Route::get('/status-{id}/{status}', [PromocodeController::class, 'status']);
            Route::get('delete-{id}', [PromocodeController::class, 'delete']);
            Route::post('/reorder_coupon', [PromocodeController::class, 'reorder_coupon']);
        });


        Route::middleware('VendorMiddleware')->group(function () {
            Route::post('/applycoupon', [PromocodeController::class, 'vendorapplypromocode']);
            Route::post('/removecoupon', [PromocodeController::class, 'removepromocode']);
        });
    });
});

Route::namespace('front')->group(function () {
    Route::post('/cart/applypromocode', [HomeController::class, 'applypromocode']);
    Route::post('/cart/removepromocode', [HomeController::class, 'removepromocode']);
});
