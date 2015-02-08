<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CartsAndCatrItems extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		Schema::create('carts', function($table)
		{
			$table->increments('id');
			$table->timestamps();
		});

		Schema::create('carts_items', function($table)
		{
			$table->increments('id');
			$table->integer('carts_id')->unsigned();
			$table->foreign('carts_id')->references('id')->on('carts');
			$table->integer('items_id')->unsigned();
			$table->index('items_id');
			$table->integer('qty')->unsigned();
			$table->decimal('price',10,2)->unsigned();
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
