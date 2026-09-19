<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name', 80);
            $table->string('mobile', 11);
            $table->string('subject', 120);
            $table->text('message');
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index('mobile');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
