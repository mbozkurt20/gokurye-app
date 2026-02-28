<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommissionToUsersAndTopupMovements extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->default(20.00)->after('is_active');
            $table->decimal('commission_balance', 12, 2)->default(0.00)->after('commission_rate');
        });

        Schema::table('topup_movements', function (Blueprint $table) {
            $table->decimal('dealer_commission', 12, 2)->nullable()->after('payment_details');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['commission_rate', 'commission_balance']);
        });
        Schema::table('topup_movements', function (Blueprint $table) {
            $table->dropColumn('dealer_commission');
        });
    }
}
