<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ItemsPosField extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('items',function($table){
			$table->integer('pos')->unsigned();
		});
		$suppliers = Supplier::with('items')->get();
		foreach ($suppliers as $supplier) {
			$count = 1;
			foreach ($supplier['items'] as $item) {
				$item->pos = $count;
				$item->save();
				$count++;
			}
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('items',function($table){
			$table->dropColumn('pos');
		});
	}

}
