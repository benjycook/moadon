<?php

class Order extends Eloquent {

	protected $table = 'orders';
	protected $fillable = array('clients_id','taxId','invoiceFor','city','street','house','entrance','apartment',
		'zipcode','phone1','phone2','fax','email','createdOn','clubs_id','firstName','lastName');
	public $timestamps = false;

	public function items()
	{
		return $this->hasMany('OrderItem','orders_id','id')->with('realized');
	}
}