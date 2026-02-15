<?php

use App\Http\Controllers\Auth\Dealer;
use App\Http\Controllers\Dealer\Ajax\AjaxController;
use App\Http\Controllers\Dealer\DashboardController;
use App\Http\Controllers\Dealer\Report\ReportController;
use App\Http\Controllers\Dealer\AdminController as DealerAdminController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'dealer'], function () {

    Route::controller(Dealer::class)->group(function () {
        Route::view('login', "dealer.login")->name('dealer.login');
        Route::post('login', 'login')->name('dealer.auth');
        Route::post('logout', 'logout')->name('dealer.logout');
    });

    Route::middleware(['dealer.auth'])->group(function () {

        Route::get('/printed/{orderId}', [OrderController::class, 'printed']);

        Route::controller(DashboardController::class)->group(function () {
            Route::get('/dashboard', 'home')->name('dealer.dashboards');
            Route::get('/profile', 'profile')->name('dealer.profile');
            Route::post('/profile', 'profileUpdate')->name('dealer.profile.update');
            Route::get('/get-districts/{cityId}', 'getDistricts')->name('dealer.get_districts');
            Route::get('/orders', 'orders')->name('dealer.orders');
            Route::get('dashboard/filter-by-date', 'filterByDate')->name('dealer.filterByDate');
            Route::get('dashboard/orders/filter', 'filterOrders')->name('dealer.filter');
        });

        Route::get('/dashboard-data', [AjaxController::class, 'getDashboardData'])->name('dealer.dashboards.get_data');

        Route::controller(DealerAdminController::class)->prefix('admin')->group(function () {
            Route::get('/', 'admin')->name('dealer.admin');
            Route::get('/add', 'createAdmin')->name('dealer.admin_create');
            Route::post('/add', 'createAdminRequest')->name('dealer.admin_create_request');
            Route::get('/edit/{id}', 'editAdmin')->name('dealer.admin_edit');
            Route::put('/edit/{id}', 'updateAdmin')->name('dealer.admin_update');
            Route::get('/delete/{id}', 'deleteAdmin')->name('dealer.admin_delete');
            Route::get('/status/{id}', 'statusAdmin')->name('dealer.admin_status');

            Route::get('/topup/list', 'list')->name('dealer.admin_topup_list');
            Route::get('/topup/{id}', 'topup')->name('dealer.admin_topup');
            Route::post('/topup/add', 'topUpCreate')->name('dealer.admin_topup_create');
            Route::get('/topup/approve/{topupId}', 'approve')->name('dealer.approve');
            Route::get('/topup/paid/{topupId}', 'paid')->name('dealer.paid');
            Route::get('/topup/unpaid/{topupId}', 'unPaid')->name('dealer.unpaid');
        });

        Route::controller(ReportController::class)->prefix('reports')->group(function () {
            Route::get('/', 'index')->name('dealer.reports');
            Route::get('/download', 'downloadReport')->name('dealer.reports.download');
        });
    });
});
