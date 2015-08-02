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
		'cities_id',
		'states_id',
		'visibleOnSite',
		'newBusiness',
		'mostViewed',
		'hotDeal',
	);


	public function categories()
	{
		return $this->belongsToMany('Category', 'categories_suppliers', 'suppliers_id', 'categories_id');
	}

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
		return $query->with('galleries')->with(['items'=>function($iteQuery){
			$iteQuery->where('states_id','=',2);
			$iteQuery->orderBy('pos','ASC');
		}]);
	}

  public function scopeHomePage($query, $filterField, $rand = false)
  {
  	// $mainCategory = "(
   //                SELECT id
   //                FROM   categories
   //                WHERE  id IN (
   //                               (
   //                               SELECT DISTINCT parent_id
   //                               FROM            categories
   //                               WHERE           id IN
   //                                               (
   //                                                      SELECT categories_id
   //                                                      FROM   categories_suppliers
   //                                                      WHERE  suppliers_id = sitedetails.suppliers_id)))
   //                AND    parent_id=0 LIMIT 1) AS mainCategory,";


  	return $query->join('items', 'sitedetails.suppliers_id', '=', 'items.suppliers_id')
  													->where($filterField, '=', '1')
														->where('sitedetails.states_id', '=', '2')
														->where('items.states_id', '=', '2')
														->whereRaw('(100 - FLOOR(items.priceSingle / items.listPrice * 100))')
														->select(DB::raw(
															'sitedetails.*, MAX(100 - FLOOR(items.priceSingle / items.listPrice * 100)) AS discount'
														))
														->orderBy($rand ? DB::raw('RAND()') : DB::raw('MAX(100 - FLOOR(items.priceSingle / items.listPrice * 100))'), 'DESC')
														->groupBy('sitedetails.suppliers_id')
														->with('galleries');
  }
}
