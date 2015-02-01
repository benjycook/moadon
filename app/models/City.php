<?php
class City extends Eloquent{
	protected $table = 'cities';
	protected $fillable = array('name');
	public $timestamps = false;
	
	public function regions()
	{
		return $this->belongsToMany('Region','cities_regions', 'cities_id','regions_id');
	}

	public function suppliers()
	{
		return $this->hasMany('SiteDetails','cities_id','id');
	}
}