<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Rack\Enums\RackStatus;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('racks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->string('title');
            $table->string('url')->nullable();
            $table->text('description')->nullable();
            $table->unsignedFloat('priority')->nullable();
            $table->unsignedTinyInteger('status')->default(RackStatus::Pending);
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
        Schema::dropIfExists('racks');
    }
};
