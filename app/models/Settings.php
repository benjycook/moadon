<?php

class Settings extends Eloquent {

	protected $table = 'settings';
	protected $fillable = array('vat','creditCommission');
	public $timestamps = false;
}