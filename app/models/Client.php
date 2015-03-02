<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
class Client extends Eloquent implements UserInterface {
	use UserTrait;
	protected $table = 'clients';
	
	protected $fillable = array('taxId',
		'city',
		'street',
		'house',
		'entrance',
		'apartment',
		'zipcode',
		'mobile',
		'email',
		'recieveNews',
		'clubs_id',
		'firstName',
		'lastName',
		'password'
	);
	
	public $timestamps = false;
	
	public function getId()
	{
	  return $this->id;
	}

	public function getAuthPassword()
	{
		return Hash::make($this->password);
	}

	public function club()
	{
		return $this->belongsTo('Club','clubs_id','id');
	}

	public function orders()
	{
		return $this->hasMany('Order','clients_id','id');
	}

	public function cart()
	{
		return $this->hasOne('Cart', 'clients_id', 'id');
	}
}