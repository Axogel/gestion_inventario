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
            $table->enum('type', ['PRODUCT', 'SERVICE']);
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->integer('cantidad');
            $table->decimal('subtotal', 14, 2);
            $table->timestamps();
            $table->foreign('orden_id')->references('id')->on('orden_entregas')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('inventarios')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');


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
