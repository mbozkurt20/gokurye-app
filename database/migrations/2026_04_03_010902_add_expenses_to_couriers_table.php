<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpensesToCouriersTable extends Migration
{
    public function up()
    {
        Schema::table('couriers', function (Blueprint $table) {
            $table->json('expenses')->nullable()->after('notes');
        });
    }

    public function down()
    {
        Schema::table('couriers', function (Blueprint $table) {
            $table->dropColumn('expenses');
        });
    }
}
