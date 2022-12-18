<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Order\Enums\OrderInvoiceStatus;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('order_id')
                ->references('id')
                ->on('orders')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('transactionId');
            $table->string('gateway');
            $table->string('amount');
            $table->unsignedTinyInteger('status')->default(OrderInvoiceStatus::Pending);
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
        Schema::dropIfExists('invoices');
    }
};
