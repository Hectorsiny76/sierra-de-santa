<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('provider_id')->unique();
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->unsignedInteger('amount');
            $table->json('raw_response');
            $table->string('receipt_number')->nullable();
            $table->foreignId('reservation_id')->nullable()->references('id')->on('reservations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
