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
        Schema::create('orderables', function (Blueprint $table) {
            $table->foreignId('order_id')
                ->references('id')
                ->on('orders')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->morphs('orderable');
            $table->unsignedInteger('quantity');
            $table->string('unit_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orderables');
    }
};
