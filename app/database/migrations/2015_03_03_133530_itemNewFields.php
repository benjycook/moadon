<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ItemNewFields extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('items',function($table){
			$table->decimal('priceSingle',10,2)->unsigned()->nullable();
			$table->decimal('priceGroup',10,2)->unsigned()->nullable();
		});
		Schema::table('orders_items',function($table){
			$table->decimal('priceSingle',10,2)->unsigned()->nullable();
			$table->decimal('priceGroup',10,2)->unsigned()->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('items',function($table){
			$table->dropColumn('priceSingle');
			$table->dropColumn('priceGroup');
		});
		Schema::table('orders_items',function($table){
			$table->dropColumn('priceSingle');
			$table->dropColumn('priceGroup');
		});
	}

}
