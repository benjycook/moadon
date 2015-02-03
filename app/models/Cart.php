<?php
class Cart extends Eloquent{
	protected $table = 'carts';

	public function suppliers()
	{
		return $this->hasMany('CartItem','carts_id','id');
	}
}