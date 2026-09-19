<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('balance')->default(0);
            $table->timestamps();
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('order_id')->nullable();
            $table->string('type', 10);
            $table->string('reason', 50);
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('balance_after');
            $table->string('description', 255)->nullable();
            $table->string('idempotency_key', 80)->nullable()->unique();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('order_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('wallet_used_amount')->default(0)->after('total_price');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('wallet_used_amount');
        });

        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
    }
};
