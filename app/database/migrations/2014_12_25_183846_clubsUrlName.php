<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClubsUrlName extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('clubs', function($table)
		{
			$table->string('urlName');
			$table->string('altHeadline');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('clubs', function($table)
		{
			$table->dropColumn('altHeadline');
			$table->dropColumn('urlName');
		});
	}

}
