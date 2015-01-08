<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class States extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		Schema::create('states', function($table)
		{
			$table->increments('id');
			$table->string('name');
		});
		State::create(array('name'=>'לא פעיל'));
		State::create(array('name'=>'פעיל'));

		Schema::table('users', function($table)
		{
			$table->integer('states_id')->unsigned();
			$table->foreign('states_id')->references('id')->on('states');
		});
		User::where('states_id','=',0)->update(array('states_id'=>2));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		Schema::table('users', function($table)
		{
			$table->dropForeign('users_states_id_foreign');
			$table->dropColumn('states_id');
		});
		Schema::dropIfExists('states');
		
	}

}
