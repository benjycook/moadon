<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrdersColumnsDropAndRename extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('orders',function($table){
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
		Schema::table('orders',function($table){
			$table->string('invoiceFor');
			$table->string('fax');
			$table->string('phone2');
			$table->string('phone1');
			$table->dropColumn('mobile');
		});
	}

}
