<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Cities extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cities', function($table)
		{
			$table->increments('id');
			$table->string('name');
		});
		Schema::create('cities_regions', function($table)
		{
			$table->increments('id');
			$table->integer('regions_id')->unsigned();
			$table->foreign('regions_id')->references('id')->on('regions');
			$table->integer('cities_id')->unsigned();
			$table->foreign('cities_id')->references('id')->on('cities');
		});
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		Schema::dropIfExists('suppliers_regions');
		Schema::table('sitedetails', function($table)
		{
			$table->dropForeign('sitedetails_regions_id_foreign');
			$table->dropColumn('regions_id');
			$table->integer('cities_id')->unsigned();
			$table->foreign('cities_id')->references('id')->on('cities');
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
