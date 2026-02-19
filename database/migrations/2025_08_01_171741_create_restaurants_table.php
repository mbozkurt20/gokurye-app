<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->string('restaurant_code');
            $table->string('restaurant_name');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('password');
            $table->tinyText('address')->nullable();
            $table->string('tax_name')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('package_price');
            $table->json('getir')->nullable();
            $table->json('yemeksepeti')->nullable();
            $table->json('trendyol')->nullable();
            $table->json('migros')->nullable();
            $table->string('gpsyemek_api_key')->nullable();
            $table->unsignedBigInteger('entegra_restaurant_id')->nullable();
            $table->string('menu_template')->nullable();
            $table->string('remember_token')->nullable();
            $table->boolean('email_verified_at')->nullable();
            $table->enum('status',['active','deactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurants');
    }
}
