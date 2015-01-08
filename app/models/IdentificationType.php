<?php

class IdentificationType extends Eloquent {

	protected $table = 'identificationtypes';
	protected $fillable = array('name');
	public $timestamps = false;
}