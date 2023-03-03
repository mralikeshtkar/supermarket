<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feature_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_id')
                ->references('id')
                ->on('features')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('value');
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
        Schema::dropIfExists('feature_options');
    }
};
