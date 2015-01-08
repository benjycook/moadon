<?php

class ItemRealization extends Eloquent {

	protected $table = 'items_realizations';
	protected $fillable = array('orders_items_id','realizedOn','realizedQty');
	public $timestamps = false;
}

