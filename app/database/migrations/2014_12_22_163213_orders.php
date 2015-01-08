<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Orders extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clients', function($table)
		{
			$table->increments('id');
			$table->string('taxId');
			$table->string('firstName');
			$table->string('lastName');
		    $table->string('city');
		    $table->string('invoiceFor')->nullable();
		    $table->string('street');
		    $table->string('house');
		    $table->string('entrance');
		    $table->string('apartment');
		    $table->integer('zipcode');
		    $table->string('phone1');
		    $table->string('phone2');
		    $table->string('fax');
		    $table->string('email');
		    $table->integer('clubs_id')->unsigned()->nullable();
			$table->foreign('clubs_id')->references('id')->on('clubs');
		    $table->tinyInteger('recieveNews')->default(0);
		});

		Schema::create('orders', function($table)
		{
			$table->increments('id');
			$table->integer('clients_id')->unsigned();
			$table->string('firstName');
			$table->string('lastName');
			$table->string('taxId');
			$table->integer('clubs_id')->unsigned();
			$table->string('invoiceFor')->nullable();
		    $table->string('city');
		    $table->string('street');
		    $table->string('house');
		    $table->string('entrance');
		    $table->string('apartment');
		    $table->integer('zipcode');
		    $table->string('phone1');
		    $table->string('phone2');
		    $table->string('fax');
		    $table->string('email');
		    $table->datetime('createdOn');
		});

		Schema::create('orders_items', function($table)
		{
			$table->increments('id');
			$table->integer('orders_id')->unsigned()->nullable();
			$table->foreign('orders_id')->references('id')->on('orders');
			$table->string('name');
			$table->text('description');
			$table->date('expirationDate');
			$table->decimal('listPrice',10,2)->unsigned();
			$table->decimal('clubPrice',10,2)->unsigned();
			$table->decimal('netPrice',10,2)->unsigned();
			$table->integer('items_id')->unsigned();
			$table->integer('suppliers_id')->unsigned();
			$table->string('shortDescription');
			$table->string('sku');
			$table->text('notes');
			$table->decimal('listPriceGroup',10,2)->unsigned();
			$table->decimal('netPriceGroup',10,2)->unsigned();
			$table->integer('minParticipants')->unsigned();
			$table->integer('maxParticipants')->unsigned();	
			$table->integer('itemtypes_id')->unsigned()->nullable();
			$table->integer('qty')->unsigned();
			$table->tinyInteger('fullyRealized')->default(0);
		});

		Schema::create('items_realizations', function($table)
		{
			$table->increments('id');
			$table->integer('orders_items_id')->unsigned()->nullable();
			$table->foreign('orders_items_id')->references('id')->on('orders_items');
			$table->datetime('realizedOn');
			$table->integer('realizedQty')->unsigned();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('clients');
		Schema::dropIfExists('orders');
		Schema::dropIfExists('orders_items');
		Schema::dropIfExists('items_realizations');
	}

}
