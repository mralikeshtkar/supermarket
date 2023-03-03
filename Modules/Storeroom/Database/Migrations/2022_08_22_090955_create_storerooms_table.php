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
        Schema::create('storerooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('province_id')
                ->nullable()
                ->references('id')
                ->on('provinces')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('city_id')
                ->nullable()
                ->references('id')
                ->on('cities')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->string('name');
            $table->text('address');
            $table->json('phone_numbers')->nullable();
            $table->double('lat');
            $table->double('lng');
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
        Schema::dropIfExists('storerooms');
    }
};
