<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
class Supplier extends Eloquent implements UserInterface {
	use UserTrait;

	protected $table = 'suppliers';

	protected $fillable = array(
		'name',
		'idNumber',
		'username',
		'password',
		'states_id',
	);
	public function orders()
	{
		return $this->hasMany('OrderItem','suppliers_id','id');
	}
	public function items()
	{
		return $this->hasMany('Item','suppliers_id','id')->with('galleries');
	}

	public function contacts()
	{
		return $this->hasMany('SupplierContact','suppliers_id','id');
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
  	
  public function getId()
	{
	  return $this->id;
	}

	public function getAuthPassword()
	{
		return Hash::make($this->password);
	}

}
