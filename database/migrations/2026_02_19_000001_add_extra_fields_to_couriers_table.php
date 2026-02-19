<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFieldsToCouriersTable extends Migration
{
    public function up()
    {
        Schema::table('couriers', function (Blueprint $table) {
            $table->string('iban')->nullable()->after('birthday');
            $table->string('bank')->nullable()->after('iban');
            $table->string('profile_photo')->nullable()->after('bank');
            $table->string('tc_id')->nullable()->after('profile_photo');
            $table->unsignedTinyInteger('age')->nullable()->after('tc_id');
            $table->string('vehicle_type')->nullable()->after('age'); // motor, otomobil
            $table->string('plate')->nullable()->after('vehicle_type');
            $table->string('blood_type')->nullable()->after('plate');
        });
    }

    public function down()
    {
        Schema::table('couriers', function (Blueprint $table) {
            $table->dropColumn(['iban', 'bank', 'profile_photo', 'tc_id', 'age', 'vehicle_type', 'plate', 'blood_type']);
        });
    }
}
