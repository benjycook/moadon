<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTokens extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Schema::create('tokens', function($table)
		// 	{
		// 	    $table->increments('id');
		// 	    $table->string('name');
		// 	    $table->string('password');
		// 	    $table->string('token');
		// 	    $table->timestamps();
		// 	});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	
		//Schema::dropIfExists('tokens');
	}

}
