<?php

class Gallery extends Eloquent {
	protected $table = 'galleries';
	protected $fillable = array('type');
	public $timestamps = false;

    public function images()
    {
        return $this->hasMany('GalleryImage','galleries_id','id');
    }

}
