<?php

class Region extends Eloquent {
	
	protected $table = 'regions';
	protected $fillable = array('name','parent_id');
	public $timestamps = false;

   

	public function suppliers()
    {
        return $this->belongsToMany('Supplier','suppliers_regions','regions_id','suppliers_id');
    }

    public function children()
    {
        return $this->hasMany('Region','parent_id','id')->with('children');
    }

    public function parents()
    {
        return $this->hasMany('Region','id','parent_id')->with('parents');
    }
    public static function boot()
    {
        parent::boot();
        Region::deleting(function($reg)
        {   
           $reg->children()->delete();
        });
    }
}