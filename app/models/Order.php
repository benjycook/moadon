<?php

class Order extends Eloquent {

	protected $table = 'orders';
	protected $fillable = array('clients_id','taxId','invoiceFor','city','street','house','entrance','apartment',
		'zipcode','mobile','phone2','fax','email','createdOn','clubs_id','firstName','lastName','key','code');
	public $timestamps = false;

	public function items()
	{
		return $this->hasMany('OrderItem','orders_id','id')->with('realized');
	}

	public function club()
	{
		return $this->belongsTo('Club','clubs_id','id');
	}
	public function payment()
	{
		return $this->hasOne('GatewayLog','orders_id','id');
	}
}