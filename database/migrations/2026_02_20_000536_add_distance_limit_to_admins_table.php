<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDistanceLimitToAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->decimal('distance_limit', 8, 2)->default(50)->after('district_id');
            $table->unsignedInteger('max_package_limit')->default(4)->after('distance_limit');
        });
    }

    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['distance_limit', 'max_package_limit']);
        });
    }
}
