<?php

class Category extends Eloquent {
    
	protected $table = 'categories';
	
    protected $fillable = array(
        'name',
        'parent_id'
    );

	public $timestamps = false;

	public function suppliers()
    {
        return $this->belongsToMany('Supplier','categories_suppliers','categories_id','suppliers_id');
    }

    public function children()
    {
    	return $this->hasMany('Category','parent_id','id')->with('children');
    }

    public function parents()
    {
        return $this->hasMany('Category','id','parent_id')->with('parents');
    }
    
    public static function boot()
    {
        parent::boot();

        Category::deleting(function($cat)
        {   
           $cat->children()->delete();
        });
    }
}