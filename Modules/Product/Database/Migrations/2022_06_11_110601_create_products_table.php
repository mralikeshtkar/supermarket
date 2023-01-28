<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Product\Enums\ProductStatus;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('brand_id')
                ->nullable()
                ->references('id')
                ->on('brands')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('unit_id')
                ->nullable()
                ->references('id')
                ->on('product_units')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('price');
            $table->string('price');
            $table->string('old_price')->nullable();
            $table->string('additional_price')->nullable();
            $table->boolean('delivery_is_free')->default(false);
            $table->boolean('has_tax_exemption')->default(false);
            $table->unsignedSmallInteger('status')->default(ProductStatus::Pending);
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
        Schema::dropIfExists('products');
    }
};
