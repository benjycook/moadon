<?php

class ItemType extends Eloquent {

	protected $table = 'itemtypes';
	protected $fillable = array('name');
	public $timestamps = false;
}