<?php

class SiteDetails extends Eloquent {

	protected $table = 'sitedetails';
	
	public $timestamps = false;

	protected $fillable = array(
		'supplierName',
		'description',
		'phone1',
		'phone2',
		'fax',
		'address',
		'site',
		'email',
		'suppliers_id',
		'altHeadline',
		'phone3',
		'workingHours',
		'ageDevision',
		'miniSiteContext',
		'regions_id',
		'states_id',
		'visibleOnSite'
	);



	public function galleries()
	{
		return $this->belongsToMany('Gallery','galleries_sitedetails', 'sitedetails_id','galleries_id')->with('images');
	}

	public function supplier()
	{
		return $this->hasOne('Supplier','id','suppliers_id');
	}

	public function scopeSite($query)
  {
  	return $query->select('id','supplierName','altHeadline','city','regions_id','phone1')->take(9)->with('galleries');
  }

  public function items()
	{
		return $this->hasMany('Item','suppliers_id','suppliers_id')->with('galleries');
	}

  public function scopeMini($query)
  {
  	return $query->with('galleries')->with('items');
  }
}
