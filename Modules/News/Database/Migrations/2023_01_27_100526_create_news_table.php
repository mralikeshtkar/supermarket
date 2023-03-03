<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\News\Enums\NewsStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('category_id')
                ->nullable()
                ->references('id')
                ->on('news_categories')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->string('title');
            $table->text('body');
            $table->string('status')->default(NewsStatus::Pending);
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
        Schema::dropIfExists('news');
    }
};
