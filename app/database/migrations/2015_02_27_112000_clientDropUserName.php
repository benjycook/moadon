<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClientDropUserName extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('clients',function($table){
			$table->dropColumn('username');
			$table->dropColumn('invoiceFor');
			$table->dropColumn('fax');
			$table->dropColumn('phone2');
			$table->dropColumn('phone1');
			$table->string('mobile');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('clients',function($table){
			$table->string('username');
			$table->string('invoiceFor');
			$table->string('fax');
			$table->string('phone2');
			$table->string('phone1');
			$table->dropColumn('mobile');
		});
	}

}
