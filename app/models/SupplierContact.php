<?php
class SupplierContact extends Eloquent{
	protected $table = 'suppliers_contacts';
	protected $fillable = array('suppliers_id','firstName','lastName','mobile','email');
	public $timestamps = false;
	
}