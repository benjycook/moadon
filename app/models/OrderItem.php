<?php

class OrderItem extends Eloquent {

	protected $table = 'orders_items';
	protected $fillable = array('name','description','expirationDate','listPrice','clubPrice','netPrice','items_id','suppliers_id','qty',
					'shortDescription','sku','notes','listPriceGroup','netPriceGroup','minParticipants','maxParticipants','itemtypes_id',
					'orders_id','priceSingle','priceGroup',
					);
	public $timestamps = false;

	public function realized()
	{
		return $this->hasMany('Realized','orders_items_id','id');
	}

	public function supplier()
	{
		return $this->hasOne('SiteDetails','suppliers_id','suppliers_id');
	}
}

