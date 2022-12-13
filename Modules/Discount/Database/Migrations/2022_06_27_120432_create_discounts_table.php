<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Discount\Enums\DiscountStatus;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->string('code')->nullable();
            $table->string('amount');
            $table->boolean('is_percent')->default(true);
            $table->timestamp('start_at')->nullable(); // null is unlimited date period.
            $table->timestamp('expire_at')->nullable(); // null is unlimited date period.
            $table->unsignedBigInteger('usage_limitation')->nullable(); // null is unlimited usage.
            $table->unsignedBigInteger('uses')->default(0);
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('status')->default(DiscountStatus::Pending);
            $table->unsignedFloat('priority')->nullable();
            $table->timestamps();
        });
        Schema::create('discountables', function (Blueprint $table) {
            $table->foreignId('discount_id');
            $table->morphs('discountable');
            $table->primary(['discount_id', 'discountable_id', 'discountable_type'], 'discountables_key');

            $table->foreign('discount_id')->references('id')->on('discounts')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discountables');
        Schema::dropIfExists('discounts');
    }
};
