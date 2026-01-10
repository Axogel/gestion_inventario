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
        Schema::create('movement_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('inventarios')->onDelete('cascade');
            $table->enum('type', ['input', 'output']);
            $table->integer('quantity');
            $table->string('reason');
            $table->text('description')->nullable();
            $table->integer('balance_after')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movement_inventories');
    }
};
