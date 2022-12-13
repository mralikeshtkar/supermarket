<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Category\Enums\CategoryStatus;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('parent_id')
                ->nullable()
                ->references('id')
                ->on('categories')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('status')->default(CategoryStatus::Pending);
            $table->timestamps();
        });
        Schema::create('categorizables', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->references('id')
                ->on('categories')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->morphs('categorizables');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categorizables');
        Schema::dropIfExists('categories');
    }
};
