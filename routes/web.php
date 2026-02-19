<?php

use App\Http\Controllers\TamiPaymentController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\GpsYemekController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EntegraWebhookController;
use App\Http\Controllers\PayTrPaymentController;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\CourierApplyController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::controller(MenuController::class)->group(function () {
    Route::get('/restaurant/{restaurantId}/menu', 'show')->name('restaurant.menu');
});

Route::get('/pSwAIk2Jo6edRFcHME/gpskurye', [GpsYemekController::class, 'index'])->name('restaurant.couriers.index');
Route::get('/pSwAIk2Jo6edRFcHME/jobs', [JobController::class, 'index']);

Auth::routes();

include __DIR__ . '/app/superAdminRoutes.php';
include __DIR__ . '/app/adminRoutes.php';
include __DIR__ . '/app/restaurantRoutes.php';
include __DIR__ . '/app/partnerRoutes.php';

Route::controller(EntegraWebhookController::class)->prefix('entegra')->group(function () {
    Route::post('/add-order', 'addOrder');
    Route::post('/cancel-order', 'cancelOrder');
});

Route::controller(PayTrPaymentController::class)->prefix('paytr')->group(function () {
    Route::post('/callback', 'paytrCallback')->name('paytr.callback');
    Route::get('/success', 'payTrSuccess')->name('paytr.success');
    Route::get('/fail', 'payTrFail')->name('paytr.fail');
});

Route::controller(TamiPaymentController::class)->group(function () {
    Route::post('/payment/callback', 'callback')
        ->name('payment.callback')
        ->withoutMiddleware(['web']);

    Route::middleware('auth:admin')->group(function () {
        Route::get('/payment/success', 'successPage')->name('payment.success');
        Route::get('/payment/fail', 'failPage')->name('payment.fail');
    });
});

Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/home', 'index')->name('home.dashboard');
    Route::get('/get-districts/{cityId}', 'getDistricts');
});

// Demo hesabı formu & oluştur
Route::get('/demo', [DemoController::class, 'index'])->name('demo.index');
Route::post('/demo', [DemoController::class, 'create'])->name('demo.create');

// Kurye başvuru (public)
Route::get('/kurye-basvuru', [CourierApplyController::class, 'index'])->name('courier.apply');
Route::post('/kurye-basvuru', [CourierApplyController::class, 'store'])->name('courier.apply.store');
