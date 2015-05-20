<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewCartLogicChanges extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('creditguardlog',function($table){
			$table->integer('clubs_id')->unsigned();
			$table->dropColumn('reference');
		});

		Schema::create('gateway_items',function($table){
			$table->increments('id');
			$table->integer('carts_id')->unsigned();
			$table->index('carts_id');
			$table->integer('items_id')->unsigned();
			$table->index('items_id');
			$table->integer('qty')->unsigned();
			$table->decimal('price',10,2)->unsigned();
			$table->integer('gateway_id')->unsigned();
			$table->index('gateway_id');
		});
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
