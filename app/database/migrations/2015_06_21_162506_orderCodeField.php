<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrderCodeField extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$orders = Order::all();
		Schema::table('orders',function($table){
			$table->string("code");
		});
		foreach ($orders as $order) {
			$order->code = $order->id;
			$order->save();
		}
		Schema::table('orders',function($table){
			$table->unique("code");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('orders',function($table){
			$table->dropColumn("code");
		});
	}

}
