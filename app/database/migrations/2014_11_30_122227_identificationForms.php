<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IdentificationForms extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		Schema::create('identificationtypes', function($table)
		{
			$table->increments('id');
			$table->string('name');
		});
		IdentificationType::create(array('name'=>'קוד מועדון'));
		IdentificationType::create(array('name'=>'משתמשים'));
		Schema::table('clubs', function($table)
		{
			$table->dropForeign('clubs_discounttypes_id_foreign');
			$table->dropColumn('discounttypes_id');
			$table->dropColumn('discount');
			$table->integer('identificationtypes_id')->unsigned();
			$table->foreign('identificationtypes_id')->references('id')->on('identificationtypes');
			$table->integer('regularDiscount')->unsigned()->defualt(0);
			$table->string('clubCode')->nullable();
			$table->integer('creditDiscount')->unsigned()->defualt(0);
		});
		Schema::dropIfExists('discounttypes');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		Schema::dropIfExists('identificationtypes');
		Schema::dropIfExists('discounttypes');
		// Schema::create('discounttypes', function($table)
		// {
		// 	$table->increments('id');
		// 	$table->string('name');
		// });
		// Schema::table('clubs', function($table)
		// {
		// 	$table->dropForeign('identificationtypes_id');
		// 	$table->dropColumn('regularDiscount');
		// 	$table->dropColumn('creditDiscount');
		// 	$table->dropColumn('clubCode');
		// 	$table->integer('discounttypes_id')->unsigned();
		// 	$table->integer('discount')->unsigned();
		// });
		
	}

}
