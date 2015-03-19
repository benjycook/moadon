<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payments', function($table)
		{
			$table->increments('id');
			$table->integer('orders_id')->unsigned();
			$table->foreign('orders_id')->references('id')->on('orders');
			$table->string('ownerName');
			$table->string('ownerId');
			$table->integer('creditCardType')->unsigned();
			$table->integer('creditDealType')->unsigned();
			$table->integer('paymentType')->unsigned();
			$table->string('cardNumber');
			$table->string('cvv');
			$table->date('date');
			$table->integer('numberOfPayments')->unsigned();
			$table->decimal('firstPayment',12,2)->unsigned();
			$table->decimal('total',12,2)->unsigned();
			$table->string('tranId');
			$table->string('voucher');
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
		Schema::drop('payments');
	}

}