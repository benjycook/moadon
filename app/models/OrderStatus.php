<?php

class OrderStatus extends Eloquent {

	protected $table = 'orders_statuses';
	protected $fillable = array('name');
	public $timestamps = false;
}