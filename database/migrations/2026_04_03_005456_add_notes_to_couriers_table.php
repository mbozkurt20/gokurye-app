<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotesToCouriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('couriers', function (Blueprint $table) {
            $table->json('notes')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('couriers', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
}
