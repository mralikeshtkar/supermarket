<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_storeroom_entrance', function (Blueprint $table) {
            $table->foreignId('storeroom_entrance_id')
                ->references('id')
                ->on('storeroom_entrances')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->primary(['storeroom_entrance_id', 'product_id']);
            $table->unsignedInteger('quantity')->nullable();
            $table->unsignedInteger('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_storeroom_entrance');
    }
};
