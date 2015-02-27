<?php
class Cart extends Eloquent{
	protected $table = 'carts';

	public function items()
	{
		return $this->hasMany('CartItem','carts_id','id');
	}
}