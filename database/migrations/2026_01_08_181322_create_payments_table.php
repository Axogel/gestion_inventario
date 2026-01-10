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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('orden_entregas')->onDelete('cascade'); // Asumiendo que tu tabla es 'ordens'
            $table->string('moneda'); // USD, COP, VES
            $table->decimal('monto_original', 15, 2); // Lo que entregó el cliente
            $table->decimal('tasa_cambio', 15, 2);    // Tasa del día
            $table->decimal('monto_base', 15, 2);     // Equivalente en COP (para contabilidad)
            $table->string('metodo_pago');            // Efectivo, Transferencia, Pago Móvil
            $table->string('referencia')->nullable(); // Número de comprobante
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
