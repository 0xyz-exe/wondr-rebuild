<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->string('reference', 40)->unique();
            $table->foreignId('game_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('player_id', 60);
            $table->string('server_id', 60)->nullable();
            $table->string('customer_email', 190);
            $table->string('payment_method', 40);
            $table->unsignedInteger('amount_idr');
            $table->string('payment_status', 24)->default('pending')->index();
            $table->string('fulfillment_status', 24)->default('pending')->index();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
