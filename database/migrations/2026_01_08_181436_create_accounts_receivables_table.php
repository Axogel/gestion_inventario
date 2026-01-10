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
        Schema::create('accounts_receivables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('orden_entregas')->onDelete('cascade');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes');
            $table->decimal('monto_total', 15, 2);    // Total de la orden
            $table->decimal('monto_pagado', 15, 2);   // Lo que ha abonado hasta ahora
            $table->decimal('monto_pendiente', 15, 2);// La diferencia (Deuda)
            $table->enum('status', ['pendiente', 'parcial', 'pagado'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_receivables');
    }
};
