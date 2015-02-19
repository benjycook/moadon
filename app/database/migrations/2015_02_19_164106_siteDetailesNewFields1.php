<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SiteDetailesNewFields1 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('suppliers', function($table)
		{
			$table->dropColumn('views');
		});
		Schema::table('sitedetails', function($table)
		{
			$table->tinyInteger('newBusiness')->unsigned();
			$table->tinyInteger('mostViewed')->unsigned();
			$table->tinyInteger('hotDeal')->unsigned();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sitedetails', function($table)
		{
			$table->dropColumn('newBusiness');
			$table->dropColumn('mostViewed');
			$table->dropColumn('hotDeal');
		});
		Schema::table('suppliers', function($table)
		{
			$table->integer('views')->default(0);
		});
	}

}
