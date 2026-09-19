<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->unsignedBigInteger('pending_balance')->default(0)->after('balance');
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')->default(2)->after('reason');
            $table->index(['reason', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex(['reason', 'status']);
            $table->dropColumn('status');
        });

        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn('pending_balance');
        });
    }
};
