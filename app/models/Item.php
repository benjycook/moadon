<?php

class Item extends Eloquent {

	protected $table = 'items';
	
	public $timestamps = false;

	protected $fillable = array(
		'name',
		'description',
		'expirationDate',
		'suppliers_id',
		'listPrice',
		'clubPrice',
		'netPrice',
		'states_id',
	    'shortDescription',
	    'sku',
	    'notes',
	    'listPriceGroup',
	    'netPriceGroup',
	    'minParticipants',
	    'maxParticipants',
	    'itemtypes_id',
	    'priceSingle',
	    'priceGroup',
	    'adminNotes',
  );

	//"(clubPrice / 100) * (100 - clubDicount) / 100 * 1"
	//compute profit
	/*
		club discount: 8%
		club commision: 2%
		credit company commision: 1%
		item list price: 40NIS
		item club price: 20NIS
		item net price: 10NIS

		item computed price
		item club price - club disctoun
		(item club price / 100) * (100 - club discount)
		(20 / 100) * (100 - 8) = 18.4

		item computed profit
		item computed price - credit company comision - club commision

		computed credit company commission:
		(item computed price / 100) * commision
		(18.4 / 100) * 1 = 0.184

		computed club commision:
		(item computed price - credit commision) / 100 * club commision
		((18.4 - 0.184) / 100) * 2 = 0.364

		computed income
		item computed price - credit commision - club commision
		(18.4 - 0.184 - 0.364) = 17.848

		computed profit
		computed income - item net price
		17.848 - 10 = 7.848

		computed profit percent
		(100 / computed income) * computed profit
		(100 / 17.848) * 7.848 = 43.97

	*/
	public function orders()
	{
		return $this->hasMany('OrderItem','items_id','id');
	}
	public function supplier()
	{
		return $this->belongsTo('Supplier','suppliers_id','id');
	}

  	public function galleries()
  	{
      return $this->belongsToMany('Gallery','galleries_items', 'items_id','galleries_id')->with('images');
  	}
  	// public function scopeImages($query)
  	// {
  	// 	return 	$query->join('galleries_items','galleries_items.items_id','=','items.id')
  	// 				  ->join('galleriesimages','galleriesimages.galleries_id','=','galleries_items.galleries_id')->select(DB::raw('galleriesimages.src AS Url'));
  	// }
}

