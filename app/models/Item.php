<?php

class Item extends Eloquent {

	protected $table = 'items';
	
	public $timestamps = false;

	protected $fillable = array(
		'name',
		'description',
		'listPrice',
		'expirationDate',
		'suppliers_id',
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
  );

	public function supplier()
	{
		return $this->belongsTo('Supplier','suppliers_id','id');
	}

  public function galleries()
  {
      return $this->belongsToMany('Gallery','galleries_items', 'items_id','galleries_id')->with('images');
  }
}

