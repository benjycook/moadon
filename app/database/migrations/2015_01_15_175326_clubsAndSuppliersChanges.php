<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClubsAndSuppliersChanges extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sitedetails', function($table)
		{
			$table->dropColumn('city');
			
		});
		Schema::table('clubs', function($table)
		{
			$table->string('contactFirstName');
			$table->string('contactLastName');
			$table->string('contactPhone');
			$table->string('contactEmail');
			
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
