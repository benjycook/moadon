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
		'altHeadline',
		'contactFirstName',
		'contactLastName',
		'contactPhone',
		'contactEmail',
		'title',
		'description'
	);

	public $timestamps = false;

	public function clients()
	{
		return $this->hasMany('Client', 'clubs_id', 'id');
	}

	public function scopeSite($query)
	{
		return $query->select('description', 'title', 'name','pageDescription','identificationtypes_id','pageHeadline','logo','altHeadline');
	}
}