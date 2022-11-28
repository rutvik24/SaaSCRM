<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('razor_pay_payments', function (Blueprint $table) {
            $table->id();
            $table->string('subscription_status');
            $table->string('subscription_start_date');
            $table->string('subscription_end_date');
            $table->string('subscription_id');
            $table->string('razorpay_invoice_url');
            $table->string('razorpay_payment_id');
            $table->string('razorpay_invoice_id');
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
        Schema::dropIfExists('razor_pay_payments');
    }
};
