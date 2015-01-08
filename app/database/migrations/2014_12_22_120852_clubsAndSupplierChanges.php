<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClubsAndSupplierChanges extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('clubs', function($table)
		{
			$table->integer('clubCommission')->unsigned()->nullable();
		});
		Schema::table('sitedetails', function($table)
		{
			$table->string('altHeadline');
			$table->string('phone3');
			$table->string('city');
			$table->text('workingHours');
			$table->text('ageDevision');
			$table->text('miniSiteContext');
			$table->integer('regions_id')->unsigned()->nullable();
			$table->foreign('regions_id')->references('id')->on('regions');
		});
		Schema::dropIfExists('items_regions');
		Schema::dropIfExists('categories_items');
		Schema::create('categories_suppliers', function($table)
		{
			$table->increments('id');
			$table->integer('categories_id')->unsigned();
			$table->foreign('categories_id')->references('id')->on('categories');
			$table->integer('suppliers_id')->unsigned()->onDelete('cascade');
			$table->foreign('suppliers_id')->references('id')->on('suppliers');
		});

		Schema::create('suppliers_regions', function($table)
		{
			$table->increments('id');
			$table->integer('regions_id')->unsigned();
			$table->foreign('regions_id')->references('id')->on('regions');
			$table->integer('suppliers_id')->unsigned()->onDelete('cascade');
			$table->foreign('suppliers_id')->references('id')->on('suppliers');
		});
		Schema::create('itemtypes', function($table)
		{
			$table->increments('id');
			$table->string('name');
		});
		ItemType::create(array('name'=>'בודדים'));
		ItemType::create(array('name'=>'קבוצות'));
		ItemType::create(array('name'=>'שניהם'));
		Schema::table('items', function($table)
		{
			$table->string('shortDescription');
			$table->dropColumn('discountPrecent');
			$table->string('sku');
			$table->text('notes');
			$table->decimal('listPriceGroup',10,2)->unsigned();
			$table->decimal('netPriceGroup',10,2)->unsigned();
			$table->integer('minParticipants')->unsigned();
			$table->integer('maxParticipants')->unsigned();	
			$table->integer('itemtypes_id')->unsigned()->nullable();
			$table->foreign('itemtypes_id')->references('id')->on('itemtypes');	
		});

		Schema::create('galleries_items',function($table){
			$table->increments('id');
			$table->integer('galleries_id')->unsigned();
			$table->foreign('galleries_id')->references('id')->on('galleries');
			$table->integer('items_id')->unsigned();
			$table->foreign('items_id')->references('id')->on('items');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('suppliers', function($table)
		{
			$table->dropColumn('clubCommission')->unsigned()->nullable();
		});
		Schema::create('sitedetails', function($table)
		{
			$table->dropColumn('altHeadline');
			$table->dropColumn('phone3');
			$table->dropColumn('city');
			$table->dropColumn('workingHours');
			$table->dropColumn('ageDevision');
			$table->dropColumn('miniSiteContext');
			$table->dropColumn('regions_id')->unsigned();
		});
		Schema::dropIfExists('suppliers_regions');
		Schema::dropIfExists('categories_suppliers');
		Schema::dropIfExists('itemtypes');
		Schema::create('items', function($table)
		{
			$table->integer('discountPrecent')->unsigned();
			$table->dropColumn('shortDescription');	
			$table->dropColumn('sku');
			$table->dropColumn('notes');
			$table->dropColumn('listPriceGroup',10,2)->unsigned();
			$table->dropColumn('netPriceGroup',10,2)->unsigned();
			$table->dropColumn('minParticipants')->unsigned();
			$table->dropColumn('maxParticipants')->unsigned();	
			$table->dropColumn('itemtypes_id')->unsigned();
		});

		
	}

}
