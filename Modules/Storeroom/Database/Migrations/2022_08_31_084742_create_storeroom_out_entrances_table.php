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
        Schema::create('storeroom_out_entrances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('storeroom_out_id')
                ->references('id')
                ->on('storeroom_outs')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('storeroom_entrance_id')
                ->references('id')
                ->on('storeroom_entrances')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
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
        Schema::dropIfExists('storeroom_out_entrances');
    }
};
