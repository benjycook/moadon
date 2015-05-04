<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('settings');
		Schema::create('settings',function($table){
			$table->increments('id');
			$table->double('vat',10,2)->unsigned();
			$table->double('creditCommission',10,2)->unsigned();
		});
		Settings::create(['vat'=>18,'creditCommission'=>1]);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('settings');
	}

}
