<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Base extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('discounttypes', function($table)
		{
			$table->increments('id');
			$table->string('name');
		});
		Schema::create('clubs', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('clubId');
			$table->string('password');
			$table->string('pageHeadline');
			$table->text('pageDescription');
			$table->integer('discounttypes_id')->unsigned();
			$table->foreign('discounttypes_id')->references('id')->on('discounttypes');
			$table->integer('discount')->unsigned();
			$table->string('logo');
		});

		Schema::create('suppliers', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('description');
			$table->string('contactFirstName');
			$table->string('contactLastName');
			$table->string('contactPhone');
			$table->string('contactEmail');
			$table->string('username');
			$table->string('password');
			$table->string('email');
		});

		Schema::create('regions', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->integer('parent_id')->unsigned();
		});

		Schema::create('categories', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->integer('parent_id')->unsigned();
		});

		Schema::create('items', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->text('description');
			$table->date('expirationDate');
			$table->decimal('listPrice',10,2)->unsigned();
			$table->decimal('clubPrice',10,2)->unsigned();
			$table->decimal('netPrice',10,2)->unsigned();
			$table->integer('discountPrecent')->unsigned();
			$table->integer('suppliers_id')->unsigned();
		});

		Schema::create('categories_items', function($table)
		{
			$table->increments('id');
			$table->integer('categories_id')->unsigned();
			$table->foreign('categories_id')->references('id')->on('categories');
			$table->integer('items_id')->unsigned()->onDelete('cascade');
			$table->foreign('items_id')->references('id')->on('items');
		});

		Schema::create('items_regions', function($table)
		{
			$table->increments('id');
			$table->integer('regions_id')->unsigned();
			$table->foreign('regions_id')->references('id')->on('regions');
			$table->integer('items_id')->unsigned()->onDelete('cascade');
			$table->foreign('items_id')->references('id')->on('items');
		});


		Schema::create('members', function($table)
		{
			$table->increments('id');
			$table->string('firstName');
			$table->string('lastName');
			$table->string('phone');
			$table->string('email');
			$table->string('password');
			$table->string('idNumber');
			$table->integer('clubs_id')->unsigned();
		});

		Schema::create('galleries',function($table){
			$table->increments('id');
			$table->string('type');
		});

		Schema::create('galleriesimages',function($table){
			$table->increments('id');
			$table->integer('galleries_id')->unsigned();
			$table->foreign('galleries_id')->references('id')->on('galleries');
			$table->string('pos');
			$table->string('src');
		});

		Schema::create('galleries_suppliers',function($table){
			$table->increments('id');
			$table->integer('galleries_id')->unsigned();
			$table->foreign('galleries_id')->references('id')->on('galleries');
			$table->integer('suppliers_id')->unsigned();
			$table->foreign('suppliers_id')->references('id')->on('suppliers');
		});
		Schema::create('users', function($table)
		{
			$table->increments('id');
			$table->string('firstName');
			$table->string('lastName');
			$table->string('username');
			$table->string('password');
			$table->string('email');
			$table->string('phone');
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
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		Schema::dropIfExists('members');
		Schema::dropIfExists('clubs');
		Schema::dropIfExists('items');
		Schema::dropIfExists('suppliers');
		Schema::dropIfExists('users');
		Schema::dropIfExists('images');
		Schema::dropIfExists('discounttypes');
		Schema::dropIfExists('regions');
		Schema::dropIfExists('categories');
		Schema::dropIfExists('items_regions');
		Schema::dropIfExists('categories_items');
		Schema::dropIfExists('galleries');
		Schema::dropIfExists('galleriesimages');
		Schema::dropIfExists('galleries_suppliers');
	}

}
