<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SiteDetailesStates extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		Schema::table('sitedetails', function($table)
		{
			$table->tinyInteger('visibleOnSite')->unsigned();
			$table->integer('states_id')->unsigned();
			$table->foreign('states_id')->references('id')->on('states');
		});
		$sitedetails = SiteDetails::all();
		foreach ($sitedetails as $site) {
			$site->states_id = 2;
			$site->visibleOnSite = 1;
			$site->save();
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
