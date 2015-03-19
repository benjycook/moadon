<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SupplierContacts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$suppliers = Supplier::all();
		Schema::create('suppliers_contacts',function($table){
			$table->increments('id');
			$table->integer('suppliers_id')->unsigned();
			$table->index('suppliers_id');
			$table->string('firstName');
			$table->string('lastName');
			$table->string('mobile');
			$table->string('email');
		});
		foreach ($suppliers as $supplier) {
			SupplierContact::create(array(
				'suppliers_id' 	=> $supplier->id,
				'firstName' 	=> $supplier->contactFirstName,
				'lastName'	 	=> $supplier->contactLastName,
				'mobile' 		=> $supplier->contactPhone,
				'email' 		=> $supplier->contactEmail,
				));
		}
		Schema::table('suppliers',function($table){
			$table->dropColumn('contactFirstName');
			$table->dropColumn('contactLastName');
			$table->dropColumn('contactPhone');
			$table->dropColumn('contactEmail');
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
