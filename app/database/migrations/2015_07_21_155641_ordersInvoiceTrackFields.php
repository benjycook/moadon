<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrdersInvoiceTrackFields extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders_statuses',function($table){
			$table->increments('id');
			$table->string('name');
		});
		OrderStatus::create(['name'=>'שולמה']);
		OrderStatus::create(['name'=>'מומשה חלקית']);
		OrderStatus::create(['name'=>'מומשה במלואה']);
		OrderStatus::create(['name'=>'בוטלה']);
		Schema::table('orders',function($table){
			$table->string('docNumber');
			$table->integer('orders_statuses_id')->unsigned();
			$table->index('orders_statuses_id');
		});
		$orders = Order::orderBy('id','ASC')->get();
		$index  = 50001;

		foreach ($orders as $order) {
			$order->docNumber = $index;
			$index++;
			$itemsIds = $order->items->lists('id');
			$realizedQty = ItemRealization::whereIn('orders_items_id',$itemsIds)->sum('realizedQty');
			if($order->items->sum('qty')==$realizedQty)
				$fullyRealizedOrder = 1;
			else
				$fullyRealizedOrder = 0;
			if($fullyRealizedOrder)
				$order->orders_statuses_id = 3;
			elseif(!$fullyRealizedOrder&&!$realizedQty)
			{
				$order->orders_statuses_id = 1;
			}
			else
			{
				$order->orders_statuses_id = 2;
			}
			$order->save();
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
