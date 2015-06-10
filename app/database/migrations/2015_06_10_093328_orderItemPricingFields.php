<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrderItemPricingFields extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('orders_items', function($table)
		{
			$table->decimal('noCreditDiscountPrice',12,2)->unsigned();
			$table->decimal('noDiscountPrice',12,2)->unsigned();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('orders_items', function($table)
		{
			$table->dropColumn('noCreditDiscountPrice');
			$table->dropColumn('noDiscountPrice');
		});
	}

}
