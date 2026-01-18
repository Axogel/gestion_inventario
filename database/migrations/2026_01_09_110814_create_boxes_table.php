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
        Schema::create('boxes', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique(); // Solo una caja por día
            $table->decimal('init', 15, 2); // Monto inicial
            $table->decimal('final', 15, 2)->default(0); // Monto al cerrar
            $table->decimal('final_bs_punto', 15, 2)->default(0); // Monto al cerrar
            $table->decimal('final_bs_transfer', 15, 2)->default(0); // Monto al cerrar
            $table->decimal('final_bs_pagom', 15, 2)->default(0); // Monto al cerrar
            $table->decimal('final_cop_banco', 15, 2)->default(0); // Monto al cerrar
            $table->decimal('final_usd', 15, 2)->default(0); // Monto al cerrar

            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boxes');
    }
};
