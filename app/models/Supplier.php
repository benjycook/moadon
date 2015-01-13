<?php

class Supplier extends Eloquent {

	protected $table = 'suppliers';
	protected $fillable = array('name','idNumber','username','password','contactFirstName',
								'contactLastName','contactPhone','contactEmail');
	public function items()
	{
		return $this->hasMany('Item','suppliers_id','id')->with('galleries');
	}

	public function sitedetails()
	{
		return $this->hasOne('SiteDetails','suppliers_id','id')->with('galleries');
	}

	public function regions()
    {
        return $this->belongsToMany('Region','suppliers_regions','suppliers_id','regions_id');
    }

  public function categories()
  {
      return $this->belongsToMany('Category','categories_suppliers', 'suppliers_id','categories_id');
  }
}
