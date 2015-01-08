<?php

class GalleryImage extends Eloquent {
	protected $table = 'galleriesimages';
	protected $fillable = array('galleries_id','pos','src');
	public $timestamps = false;
}
