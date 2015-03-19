<?php
class Page extends Eloquent{
	protected $table = 'pages';
	protected $fillable = array('name','text');
	public $timestamps = false;
	
}