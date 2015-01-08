<?php
class Member extends Eloquent implements UserInterface{

	protected $table = "members";
	protected $fillable = array('firstName','lastName','phone','email','employeeId','idNumber','clubs_id','password');

	public $timestamps = false;
	public static $snakeAttributes = false;
	
	public function club()
	{
		return $this->belongsTo('Club','clubs_id','id');
	}
}
