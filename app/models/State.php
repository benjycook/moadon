<?php

class State extends Eloquent {

	protected $table = 'states';
	protected $fillable = array('name');
	public $timestamps = false;
}