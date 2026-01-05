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
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique()->index();
            $table->string("producto");
            $table->bigInteger("precio");
            $table->bigInteger("precio_sin_iva");
            $table->bigInteger("costo");
            $table->bigInteger("costo_sin_iva");
            $table->bigInteger("columna2");

            $table->string("stock");
            $table->string("stock_min");
            $table->string("usd_ref")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};
