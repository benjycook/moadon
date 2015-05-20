
<?php

class GatewayItem extends Eloquent {
	protected $table = 'gateway_items';
	protected $fillable = array('carts_id','items_id','qty','price','gateway_id');
	public $timestamps = false;
}
