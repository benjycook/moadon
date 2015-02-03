<?php
class CartItem extends Eloquent{
	protected $table = 'carts_items';
	protected $fillable = array('carts_id','items_id','qty','price');
	public $timestamps = false;
}