<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Product\Enums\ProductUnitStatus;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->string('title');
            $table->unsignedTinyInteger('status')->default(ProductUnitStatus::Pending);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_units');
    }
};
