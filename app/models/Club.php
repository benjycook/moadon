<?php

class Club extends Eloquent {

	protected $table = 'clubs';
	
	protected $fillable = array(
		'name',
		'pageDescription',
		'identificationtypes_id',
		'clubId',
		'password',
		'pageHeadline',
		'logo',
		'regularDiscount',
		'creditDiscount',
		'clubCode',
		'clubCommission',
		'urlName',
		'altHeadline'
	);

	public $timestamps = false;

	public function clients()
	{
		return $this->hasMany('Client');
	}

	public function scopeSite($query)
	{
		return $query->select('name','pageDescription','identificationtypes_id','pageHeadline','logo','altHeadline');
	}
}