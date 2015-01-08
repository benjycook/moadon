<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SupplierChanges extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		Schema::dropIfExists('galleries_sitedetails');
		Schema::dropIfExists('sitedetails');
		Schema::table('suppliers', function($table)
		{
			$table->string('idNumber');
			$table->dropColumn('description');
		});
		Schema::dropIfExists('galleries_suppliers');
		
		Schema::table('items', function($table)
		{
			$table->integer('states_id')->unsigned()->default(1);
			$table->foreign('states_id')->references('id')->on('states');
		});
		Schema::create('sitedetails', function($table)
		{
			$table->increments('id');
			$table->string('description');
			$table->string('fax');
			$table->string('phone2');
			$table->string('phone1');
			$table->string('email');
			$table->string('supplierName');
			$table->string('address');
			$table->string('site');
			$table->integer('suppliers_id')->unsigned();
			$table->foreign('suppliers_id')->references('id')->on('suppliers');
		});

		Schema::create('galleries_sitedetails',function($table){
			$table->increments('id');
			$table->integer('galleries_id')->unsigned();
			$table->foreign('galleries_id')->references('id')->on('galleries');
			$table->integer('sitedetails_id')->unsigned();
			$table->foreign('sitedetails_id')->references('id')->on('sitedetails');
		});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('galleries_sitedetails');
		Schema::dropIfExists('sitedetails');
		Schema::table('suppliers', function($table)
		{
			$table->dropColumn('idNumber');
			$table->string('description');
		});
	}

}
