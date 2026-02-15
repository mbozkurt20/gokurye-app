<?php

use App\Http\Controllers\Auth\SuperAdmin;
use App\Http\Controllers\SuperAdmin\Ajax\AjaxController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\Report\ReportController;
use App\Http\Controllers\SuperAdmin\AdminController as SuperAdminAdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'superadmin'], function () {

    Route::controller(HomeController::class)->group(function () {
        Route::get('/partner', 'dealer')->name('dealer');
        Route::post('/new-partner', 'createDealerRequest')->name('createDealerRequest');
    });

    Route::controller(SuperAdmin::class)->group(function () {
        Route::view('login', "superadmin.login")->name('superadmin.login');
        Route::post('login', 'login')->name('superadmin.auth');
        Route::post('logout', 'logout')->name('superadmin.logout');
    });

    Route::middleware(['superadmin.auth'])->group(function () {

        Route::controller(DashboardController::class)->group(function () {
            Route::get('/dashboard', 'home')->name('superadmin.dashboards');
            Route::get('/orders/ajax', 'ajax')->name('superadmin.orders.ajax');
            Route::get('/get-couriers', 'getCourier');
            Route::get('/profile', 'profile')->name('superadmin.profile');
            Route::post('/profile', 'profileUpdate')->name('superadmin.profile.update');
            Route::get('/get-districts/{cityId}', 'getDistricts')->name('superadmin.get_districts');
            Route::get('/orders', 'orders')->name('superadmin.orders');
            Route::get('dashboard/filter-by-date', 'filterByDate')->name('superadmin.filterByDate');
            Route::get('dashboard/orders/filter', 'filterOrders')->name('superadmin.filter');
        });

        Route::controller(OrderController::class)->group(function () {
            Route::get('/printed/{orderId}', 'printed');
            Route::post('/telefonsiparis/updateOrderStatus', 'updateOrderStatus');
            Route::post('/yemeksepeti/updateOrderStatus', 'updateOrderStatus');
            Route::post('/getir/updateOrderStatus', 'updateOrderStatus');
            Route::post('/orders/message', 'message');
        });

        Route::controller(MyController::class)->group(function () {
            Route::get('/payment/entegrations', 'paymentEntegrations')->name('superadmin.payment.entegrations');
            Route::post('/payment/paytr', 'paymentUpdateEntegrations')->name('superadmin.payment.paytr.update');
        });

        Route::controller(DashboardController::class)->prefix('dealer')->group(function () {
            Route::get('/', 'dealer')->name('superadmin.dealer');
            Route::get('/add', 'createDealer')->name('superadmin.dealer_create');
            Route::post('/add', 'createDealerRequest')->name('superadmin.dealer_create_request');
            Route::get('/edit/{id}', 'editDealer')->name('superadmin.dealer_edit');
            Route::put('/edit/{id}', 'updateDealer')->name('superadmin.dealer_update');
            Route::get('/delete/{id}', 'deleteDealer')->name('superadmin.dealer_delete');
            Route::get('/status/{id}', 'statusDealer')->name('superadmin.dealer_status');
        });

        Route::controller(SuperAdminAdminController::class)->prefix('admin')->group(function () {
            Route::get('/', 'admin')->name('superadmin.admin');
            Route::get('/add', 'createAdmin')->name('superadmin.admin_create');
            Route::post('/add', 'createAdminRequest')->name('superadmin.admin_create_request');
            Route::get('/edit/{id}', 'editAdmin')->name('superadmin.admin_edit');
            Route::put('/edit/{id}', 'updateAdmin')->name('superadmin.admin_update');
            Route::get('/delete/{id}', 'deleteAdmin')->name('superadmin.admin_delete');
            Route::get('/status/{id}', 'statusAdmin')->name('superadmin.admin_status');

            Route::get('/topup/list', 'list')->name('superadmin.admin_topup_list');
            Route::get('/topup/{id}', 'topup')->name('superadmin.admin_topup');
            Route::post('/topup/add', 'topUpCreate')->name('superadmin.admin_topup_create');
            Route::get('/topup/approve/{topupId}', 'approve')->name('superadmin.approve');
            Route::get('/topup/paid/{topupId}', 'paid')->name('superadmin.paid');
            Route::get('/topup/unpaid/{topupId}', 'unPaid')->name('superadmin.unpaid');
        });

        Route::controller(ReportController::class)->prefix('reports')->group(function () {
            Route::get('/', 'index')->name('superadmin.reports');
            Route::get('/download', 'downloadReport')->name('superadmin.reports.download');
        });
    });
});
