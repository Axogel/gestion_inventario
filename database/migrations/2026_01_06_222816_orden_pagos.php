<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orden_pagos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_id');

            $table->string('method');      // EFECTIVO, TRANSFERENCIA, PAGO_MOVIL
            $table->string('currency', 3); // COP, USD, Bs
            $table->decimal('amount', 14, 2); // monto en esa moneda
            $table->decimal('exchange_rate', 14, 6); // tasa usada
            $table->decimal('amount_base', 14, 2); // convertido a moneda base
            $table->enum('type', ['sale', 'debt'])->default('sale');
            $table->timestamps();

            $table->foreign('orden_id')->references('id')->on('orden_entregas')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_pagos');
    }
};
