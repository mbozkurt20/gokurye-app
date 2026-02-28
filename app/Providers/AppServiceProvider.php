<?php

namespace App\Providers;

use App\Models\AdminSystemFeature;
use App\Models\Categorie;
use App\Models\Courier;
use App\Models\Customer;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\RestaurantCoupon;
use App\Observers\AdminSystemFeautureObserver;
use App\Observers\CourierObserver;
use App\Observers\OrderObserver;
use App\Observers\RestaurantObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Courier::observe(CourierObserver::class);
        Order::observe(OrderObserver::class);
        Restaurant::observe(RestaurantObserver::class);
        AdminSystemFeature::observe(AdminSystemFeautureObserver::class);

        Relation::morphMap([
            'courier' => \App\Models\Courier::class,
        ]);

        View::composer('*', function ($view) {
            if (Auth::guard('restaurant')->check()) {
                $restaurantUser = Auth::guard('restaurant')->user();
                if (!$restaurantUser) {
                    Auth::guard('restaurant')->logout();
                    return;
                }

                $restaurantId = $restaurantUser->id;

                $courierses = Courier::where('status', 'active')
                    ->where('restaurant_id', $restaurantId)
                    ->get();

                $coupons = RestaurantCoupon::where('restaurant_id', $restaurantId)
                    ->get();

                $customers = Customer::where('status', 'active')
                    ->where('restaurant_id', $restaurantId)
                    ->get();

                $categories = Categorie::where('status', 'active')
                    ->where('restaurant_id', $restaurantId)
                    ->get();

                $view->with([
                    'courierses' => $courierses,
                    'customers' => $customers,
                    'coupons' => $coupons,
                    'categories' => $categories,
                ]);
            }

            if (Auth::guard('admin')->check()) {
                $notifications = Notification::where('status', true)
                    ->where('admin_id', Auth::guard('admin')->id())
                    ->get();

                $view->with([
                    'notifications' => $notifications,
                ]);
            }
        });
    }
}
