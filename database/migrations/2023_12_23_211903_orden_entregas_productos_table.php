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
        Schema::create('orden_entregas_productos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->unsignedBigInteger('orden_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('cantidad');
            $table->timestamps();
            $table->foreign('orden_id')->references('id')->on('orden_entregas')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('inventarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
