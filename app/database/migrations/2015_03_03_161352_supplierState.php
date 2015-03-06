<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SupplierState extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('suppliers',function($table){
			$table->integer('states_id')->unsigned();
			$table->index('states_id');
			$table->string('remember_token')->nullable();
		});
		DB::table('suppliers')->update(array('states_id'=>2));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('suppliers',function($table){
			$table->dropColumn('states_id');
			$table->dropColumn('remember_token');
		});
	}

}
