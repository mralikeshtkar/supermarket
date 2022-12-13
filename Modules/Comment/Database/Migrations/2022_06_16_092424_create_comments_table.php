<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Comment\Enums\CommentStatus;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->morphs('commentable');
            $table->foreignId('parent_id')
                ->nullable()
                ->references('id')
                ->on('comments')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->string('title');
            $table->text('body');
            $table->unsignedTinyInteger('rate')->nullable();
            $table->json('advantage')->nullable();
            $table->json('disadvantage')->nullable();
            $table->unsignedTinyInteger('status')->default(CommentStatus::Pending);
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
        Schema::dropIfExists('comments');
    }
};
