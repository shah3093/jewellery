<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers')
                    ->onDelete("CASCADE")->onUpdate("CASCADE");

            $table->text('cart');
            $table->text('shippingaddress');
            $table->string('orderid');
            
            $table->string('paymentType');
            $table->string('payment_id');

            $table->enum('status', ['PENDING', 'CONFIRMED', 'CANCELED']);
            $table->string('comment');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('orders');
    }

}
