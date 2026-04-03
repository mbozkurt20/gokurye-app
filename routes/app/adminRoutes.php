<?php

use App\Helpers\NotificationHelper;
use App\Http\Controllers\TamiPaymentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MyController;
use App\Http\Controllers\PayTrPaymentController;
use App\Http\Controllers\Admin\SiparislerController;
use App\Http\Controllers\Admin\RestaurantsController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ProgressPaymentController;
use App\Http\Controllers\Admin\ExpensesController;
use App\Http\Controllers\Admin\CourierController;
use App\Http\Controllers\Admin\InsightsController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\HeatmapController;
use App\Http\Controllers\Admin\OrderManagementController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin'], function () {
    Route::group(['middleware' => ['guest.admin']], function () {
        Route::view('login', 'auth.login')->name('admin.login');
        Route::post('login', [AdminController::class, 'login'])->name('admin.auth');
    });

    Route::group(['middleware' => ['admin.auth']], function () {
        Route::controller(AdminController::class)->group(function () {
            Route::get('/', 'home')->name('admin.index');
            Route::get('/get-districts/{cityId}', 'getDistricts')->name('admin.get_districts');
            Route::get('/statistics', 'statistics')->name('admin.statistics');
            Route::get('/orders/ajax', 'ajax')->name('admin.orders.ajax');
            Route::get('notifications/clear-all', 'notifications')->name('admin.notifications');
            Route::get('notifications/{id}', 'notificationDelete');
            Route::get('profile', 'profile')->name('admin.profile');
            Route::post('/profile', 'profileUpdate')->name('admin.profile.update');
            Route::get('/features', 'features')->name('admin.features');
            Route::get('/features/update/{id}', 'featuresUpdate')->name('admin.features.update');
            Route::get('/top-up-balance', 'balance')->name('admin.balance');
            Route::get('topup/talep', 'topupTalep')->name('admin.topupTalep');
            Route::get('/tt', 'tt');
            Route::post('logout', 'logout')->name('admin.logout');
            Route::get('/filter-by-date', 'filterByDate')->name('admin.filterByDate');
            Route::get('/orders/filter', 'filterOrders')->name('admin.filter');
            Route::get('/order/auto_order/{status}', 'auto_order')->name('admin.couriers.auto_order');
        });

        Route::post('/payment/paytr', [PayTrPaymentController::class, 'payTrPayment'])->name('admin.payment.paytr.form');

        Route::controller(TamiPaymentController::class)->group(function () {
            Route::get('/payment/form', 'showForm')->name('admin.payment.tami.form');
            Route::post('/payment/start', 'start')->name('payment.start');
        });

        Route::controller(OrderController::class)->group(function () {
            Route::get('/printed/{orderId}', 'printed');
            Route::post('/telefonsiparis/updateOrderStatus', 'updateOrderStatus');
            Route::post('/yemeksepeti/updateOrderStatus', 'updateOrderStatus');
            Route::post('/getir/updateOrderStatus', 'updateOrderStatus');
            Route::post('/orders/message', 'message');
            Route::get('orders/delete/{id}', 'deleteOrder');
        });

        Route::controller(MyController::class)->group(function () {
            Route::get('/sms/entegrations', 'smsEntegrations')->name('admin.sms.entegrations');
            Route::post('/sms/entegrations/update', 'smsEntegrastionUpdate')->name('admin.sms.entegrations.update');
            Route::post('/sms/entegrations/test', 'smsEntegrastionTest')->name('admin.sms.entegrations.test');
            Route::get('/update/entegrations/status', 'smsEntegrastionStatus')->name('admin.sms.entegrations.status');
        });

        Route::controller(SiparislerController::class)->group(function () {
            Route::get('/deletedOrders', 'deletedOrders')->name('admin.deletedOrders');
            Route::get('/deliveredOrders', 'deliveredOrders')->name('admin.deliveredOrders');
        });

        Route::controller(RestaurantsController::class)->prefix('restaurants')->group(function () {
            Route::get('/', 'index')->name('admin.restaurants');
            Route::get('/new', 'new')->name('admin.restaurants.new');
            Route::get('/edit/{id}', 'edit')->name('admin.restaurants.edit');
            Route::get('/delete/{id}', 'delete')->name('admin.restaurants.delete');
            Route::post('/create', 'create')->name('admin.restaurants.create');
            Route::post('/update', 'update')->name('admin.restaurants.update');
        });

        Route::controller(ReportController::class)->group(function () {
            Route::get('/reports', 'index')->name('admin.reports');
            Route::post('/reports/globalFilter', 'globalFilter');
        });

        Route::controller(ProgressPaymentController::class)->prefix('progress-payment')->group(function () {
            Route::get('/restaurant', 'restaurant')->name('admin.progress_payment.restaurant');
            Route::get('/courier', 'courier')->name('admin.progress_payment.courier');
            Route::post('/records', 'storeRecords')->name('admin.progress.payments.store');
            Route::post('/restaurant', 'restaurantFilter');
            Route::post('/courier', 'courierFilter');
            Route::get('/record/delete/{recordId}', 'deleteRecords');
        });

        Route::controller(ExpensesController::class)->prefix('expenses')->group(function () {
            Route::get('/', 'index')->name('admin.expenses.index');
            Route::get('/new', 'create')->name('admin.expenses.new');
            Route::get('/edit/{id}', 'edit')->name('admin.expenses.edit');
            Route::post('/update/{id}', 'update')->name('admin.expenses.update');
            Route::post('/', 'store')->name('admin.expenses.store');
            Route::get('/delete/{id}', 'destroy')->name('admin.expenses.destroy');
        });

        Route::get('/insights', [InsightsController::class, 'index'])->name('admin.insights');

        Route::controller(CommissionController::class)->prefix('commissions')->group(function () {
            Route::get('/', 'index')->name('admin.commissions.index');
            Route::post('/update', 'update')->name('admin.commissions.update');
        });

        Route::get('/heatmap', [HeatmapController::class, 'index'])->name('admin.heatmap');

        Route::controller(OrderManagementController::class)->prefix('order-management')->group(function () {
            Route::get('/', 'index')->name('admin.order.management');
            Route::post('/merge', 'merge')->name('admin.order.merge');
            Route::post('/reassign/{orderId}', 'reassign')->name('admin.order.reassign');
        });

        Route::post('/orders/bulk-assign', [OrderManagementController::class, 'bulkAssign'])->name('admin.orders.bulk-assign');

        Route::controller(CourierController::class)->group(function () {
            Route::get('/courier-performance', 'performance')->name('admin.courier.performance');
            Route::get('/couriers/shifts', 'shifts')->name('admin.couriers.shifts');
            Route::get('/get-couriers', 'getCourier');
            Route::get('/couriers', 'index')->name('admin.couriers.index');
            Route::get('/couriers/maps', 'maps')->name('admin.couriers.maps');
            Route::get('/couriers/new', 'new')->name('admin.couriers.new');
            Route::get('/couriers/edit/{id}', 'edit')->name('admin.couriers.edit');
            Route::get('/couriers/delete/{id}', 'delete')->name('admin.couriers.delete');
            Route::get('/couriers/report/{id}', 'report')->name('admin.couriers.report');
            Route::post('/couriers/create', 'create')->name('admin.couriers.create');
            Route::post('/couriers/update', 'update')->name('admin.couriers.update');
            Route::get('/couriers/approve/{id}', 'approveApplication')->name('admin.couriers.approve');
            Route::get('/couriers/reject/{id}', 'rejectApplication')->name('admin.couriers.reject');
            Route::get('/orders/sendCourier/{orderId}/{courierId}', 'sendCourier');
        });
    });
});
