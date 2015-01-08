<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClientCredentials extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('clients', function($table)
		{
			$table->string('username');
			$table->string('password');
			$table->string('remember_token')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('clients', function($table)
		{
			$table->dropColumn('username');
			$table->dropColumn('password');
			$table->dropColumn('remember_token');
		});
	}

}
