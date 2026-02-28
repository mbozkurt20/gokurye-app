<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SiparislerController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\EntegraController;
use App\Http\Controllers\Restaurant\InsightsController;
use App\Http\Controllers\Restaurant\DailyReportController;
use App\Http\Controllers\Restaurant\MenuPerformanceController;
use App\Http\Controllers\Restaurant\CrmController;
use App\Http\Controllers\Restaurant\StockController;
use App\Http\Controllers\Restaurant\WorkingHoursController;
use App\Http\Controllers\Restaurant\NoteTemplateController;
use App\Http\Controllers\Restaurant\InvoiceController;

Route::group(['prefix' => 'restaurant'], function () {

    Route::group(['middleware' => ['guest.restaurant']], function () {
        Route::view('login', 'restaurant.login')->name('restaurant.login');
        Route::view('payment', 'restaurant.payment')->name('restaurant.payment');

        Route::controller(RestaurantController::class)->group(function () {
            Route::post('login', 'login')->name('restaurant.auth');
            Route::post('register', 'register')->name('restaurant.create');
        });
    });

    // Public menu page - no auth required
    Route::get('/{restaurantId}/menu', [MenuController::class, 'show'])->name('restaurant.menu');

    Route::group(['middleware' => ['restaurant.auth']], function () {

        Route::controller(RestaurantController::class)->group(function () {
            Route::get('/', 'home')->name('restaurant.index');
            Route::get('/orders/ajax', 'ajax')->name('restaurant.orders.ajax');
            Route::get('/customers/get-by-phone/{phone}', 'getByPhone');
            Route::post('logout', 'logout')->name('restaurant.logout');
            Route::get('/filter-by-date', 'filterByDate')->name('restaurant.filterByDate');
            Route::get('/orders/filter', 'filterOrders')->name('orders.filter');
        });

        Route::controller(OrderController::class)->group(function () {
            Route::get('/printed/{orderId}', 'printed');
            Route::post('/quick-order', 'storeQuick')->name('quick.order.store');
            Route::post('/orders/message', 'message');
            Route::post('/orders/message2', 'message2');
            Route::get('/orders/new', 'new')->name('restaurant.orders.new');
            Route::get('/orders/removePOS', 'removePOS')->name('restaurant.removePOS');
            Route::get('/orders/{link}', 'index')->name('restaurant.orders');
            Route::get('/orders/sendCourier/{orderID}/{courierID}', 'sendCourier')->name('restaurant.orders.sendCourier');
            Route::get('/orders/addPOS/{id}', 'addPOS')->name('restaurant.addPOS');
            Route::get('/get-pos-items', 'getPosItems');
            Route::get('/orders/updatePlusPOS/{id}', 'updatePlusPOS')->name('restaurant.updatePlusPOS');
            Route::get('/orders/updateMinusPOS/{id}/{qty}', 'updateMinusPOS')->name('restaurant.updateMinusPOS');
            Route::get('/orders/customerpos/{id}', 'customerpos')->name('restaurant.customerpos');
            Route::post('/orders/addOrder', 'addOrder');
            Route::get('/check-orders', 'checkOrders');
            Route::post('/telefonsiparis/updateOrderStatus', 'updateOrderStatus');
        });

        Route::controller(MyController::class)->group(function () {
            Route::get('profile', 'profile')->name('restaurant.profile');
            Route::post('/profile', 'profileUpdate')->name('restaurant.profile.update');
            Route::get('/entegrations', 'entegrations')->name('restaurant.entegrations');
            Route::post('/entegrations/update', 'entegrastion_update')->name('restaurant.entegrations.entegrastion_update');
        });

        Route::controller(ReportController::class)->group(function () {
            Route::get('/reports/orders', 'orders')->name('restaurant.reports.orders');
            Route::get('/reports/couriers', 'couriers')->name('restaurant.reports.couriers');
            Route::post('/reports/globalFilter', 'globalFilter');
            Route::post('/reports/globalFilterOrder', 'globalFilterOrder');
        });

        Route::controller(CourierController::class)->group(function () {
            Route::get('/get-couriers', 'getCourier');
            Route::get('/couriers', 'index')->name('restaurant.couriers');
            Route::get('/couriers/new', 'new')->name('restaurant.couriers.new');
            Route::get('/couriers/edit/{id}', 'edit')->name('restaurant.couriers.edit');
            Route::get('/couriers/delete/{id}', 'delete')->name('restaurant.couriers.delete');
            Route::get('/couriers/report/{id}', 'report')->name('restaurant.couriers.report');
            Route::post('/couriers/create', 'create')->name('restaurant.couriers.create');
            Route::post('/couriers/update', 'update')->name('restaurant.couriers.update');
        });

        Route::controller(ProductController::class)->prefix('products')->group(function () {
            Route::get('/', 'index')->name('restaurant.products');
            Route::get('/new', 'new')->name('restaurant.products.new');
            Route::get('/edit/{id}', 'edit')->name('restaurant.products.edit');
            Route::get('/delete/{id}', 'delete')->name('restaurant.products.delete');
            Route::post('/create', 'create')->name('restaurant.products.create');
            Route::post('/update', 'update')->name('restaurant.products.update');
        });

        Route::controller(CouponController::class)->prefix('coupons')->group(function () {
            Route::get('/', 'index')->name('restaurant.coupons');
            Route::get('/new', 'create')->name('restaurant.coupons.new');
            Route::get('/edit/{id}', 'edit')->name('restaurant.coupons.edit');
            Route::get('/delete/{id}', 'delete')->name('restaurant.coupons.delete');
            Route::post('/create', 'store')->name('restaurant.coupons.create');
            Route::post('/update', 'update')->name('restaurant.coupons.update');
        });

        Route::controller(PrinterController::class)->group(function () {
            Route::get('/apps', 'apps')->name('restaurant.apps');
            Route::get('/prints', 'index')->name('restaurant.prints');
            Route::get('/prints/new', 'create')->name('restaurant.prints.new');
            Route::get('/prints/edit/{id}', 'edit')->name('restaurant.prints.edit');
            Route::get('/prints/delete/{id}', 'delete')->name('restaurant.prints.delete');
            Route::post('/prints/create', 'store')->name('restaurant.prints.create');
            Route::post('/prints/update', 'update')->name('restaurant.prints.update');
        });

        Route::controller(CategorieController::class)->prefix('categories')->group(function () {
            Route::get('/', 'index')->name('restaurant.categories');
            Route::get('/new', 'new')->name('restaurant.categories.new');
            Route::get('/edit/{id}', 'edit')->name('restaurant.categories.edit');
            Route::get('/delete/{id}', 'delete')->name('restaurant.categories.delete');
            Route::post('/create', 'create')->name('restaurant.categories.create');
            Route::post('/update', 'update')->name('restaurant.categories.update');
            Route::post('/reorder', 'reorder')->name('restaurant.categories.reorder');
        });

        Route::controller(CustomerController::class)->prefix('customers')->group(function () {
            Route::get('/get-customers', 'getCustomers')->name('restaurant.getCustomers');
            Route::get('/', 'index')->name('restaurant.customers');
            Route::get('/new', 'new')->name('restaurant.customers.new');
            Route::get('/edit/{id}', 'edit')->name('restaurant.customers.edit');
            Route::get('/delete/{id}', 'delete')->name('restaurant.customers.delete');
            Route::post('/create', 'create')->name('restaurant.customers.create');
            Route::post('/update', 'update')->name('restaurant.customers.update');
        });

        Route::controller(SiparislerController::class)->group(function () {
            Route::get('/deletedOrders', 'deletedOrders')->name('restaurant.deletedOrders');
            Route::get('/deliveredOrders', 'deliveredOrders')->name('restaurant.deliveredOrders');
        });

        Route::controller(MenuController::class)->group(function () {
            Route::get('/menus', 'index')->name('restaurant.menus');
            Route::get('/menus/qr', 'qrCode')->name('restaurant.menus.qr');
            Route::post('/menus/select', 'store')->name('restaurant.menu.template.select');
            Route::get('/menus/edit/{id}', 'edit')->name('restaurant.menus.edit');
            Route::post('/menus/create', 'create')->name('restaurant.menus.create');
            Route::post('/menus/update', 'update')->name('restaurant.menus.update');
        });

        Route::controller(EntegraController::class)->group(function () {
            Route::post('/getir/updateOrderStatus', 'updateOrderStatus');
            Route::post('/yemeksepeti/updateOrderStatus', 'updateOrderStatus');
            Route::post('/migros/updateOrderStatus', 'updateOrderStatus');
            Route::post('/trendyol/updateOrderStatus', 'updateOrderStatus');
            Route::get('/entegra/reject-statuses/{orderId}', 'getRejectReasons');
        });

        Route::get('/insights', [InsightsController::class, 'index'])->name('restaurant.insights');

        // Gün Sonu Raporu
        Route::get('/daily-report', [DailyReportController::class, 'index'])->name('restaurant.daily-report');

        // Menü Performansı
        Route::get('/menu-performance', [MenuPerformanceController::class, 'index'])->name('restaurant.menu-performance');

        // Müşteri CRM
        Route::get('/crm', [CrmController::class, 'index'])->name('restaurant.crm');

        // Stok Takibi
        Route::get('/stock', [StockController::class, 'index'])->name('restaurant.stock');
        Route::post('/stock/update', [StockController::class, 'update'])->name('restaurant.stock.update');
        Route::post('/stock/adjust/{id}', [StockController::class, 'adjust'])->name('restaurant.stock.adjust');

        // Çalışma Saatleri
        Route::get('/working-hours', [WorkingHoursController::class, 'index'])->name('restaurant.working-hours');
        Route::post('/working-hours/update', [WorkingHoursController::class, 'update'])->name('restaurant.working-hours.update');

        // Sipariş Notu Şablonları
        Route::get('/note-templates', [NoteTemplateController::class, 'index'])->name('restaurant.note-templates');
        Route::post('/note-templates', [NoteTemplateController::class, 'store'])->name('restaurant.note-templates.store');
        Route::delete('/note-templates/{id}', [NoteTemplateController::class, 'destroy'])->name('restaurant.note-templates.destroy');
        Route::get('/note-templates/api', [NoteTemplateController::class, 'apiList'])->name('restaurant.note-templates.api');

        // E-Fatura
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('restaurant.invoices');
        Route::get('/invoices/order/{id}', [InvoiceController::class, 'order'])->name('restaurant.invoices.order');
        Route::get('/invoices/daily', [InvoiceController::class, 'daily'])->name('restaurant.invoices.daily');
    });
});
