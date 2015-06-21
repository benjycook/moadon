<?php
class CartItem extends Eloquent{
	protected $table = 'carts_items';
	protected $fillable = array('carts_id','items_id','qty','price');
	public $timestamps = false;

	public function cart()
	{
		return  $this->belongsTo('Cart','carts_id','id');
	}

	public function galleries()
	{
    return $this->belongsToMany('Gallery','galleries_items', 'items_id','galleries_id')->with('images');
	}
}