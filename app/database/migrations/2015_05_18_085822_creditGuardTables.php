<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreditGuardTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('payments');
		Schema::create('creditguardlog', function($table)
		{
			$table->increments('id');
			$table->tinyInteger('status')->unsigned();
			$table->tinyInteger('maxpayments')->unsigned();
			$table->decimal('amount', 15, 2);
			$table->string('reference');
			$table->string('uniqueid');
			$table->string('code');
			$table->text('message');	
			$table->text('info');
			$table->text('url');	
			$table->text('tranid');
			$table->text('txid');		
			$table->string('cardmask');
			$table->string('exp');
			$table->string('cardtoken');
			$table->string('holderid');
			$table->string('holdername');
			$table->string('auth');
			$table->string('rcode');
			$table->tinyInteger('success')->unsigned();
			$table->tinyInteger('payments')->unsigned();
			$table->decimal('firstpayment', 15, 2);
			$table->decimal('otherpayment', 15, 2);
			$table->tinyinteger('store')->unsigned()->default(0);
			$table->integer('orders_id')->unsigned();
			$table->integer('clients_id')->unsigned();
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
		Schema::dropIfExists('wt_creditguardlog');
	}

}
