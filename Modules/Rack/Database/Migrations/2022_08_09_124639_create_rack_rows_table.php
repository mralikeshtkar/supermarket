<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Rack\Enums\RackRowStatus;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rack_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('rack_id')
                ->references('id')
                ->on('racks')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('title');
            $table->unsignedSmallInteger('priority')->default(0);
            $table->unsignedTinyInteger('status')->default(RackRowStatus::Active);
            $table->unsignedSmallInteger('number_limit');
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
        Schema::dropIfExists('rack_rows');
    }
};
