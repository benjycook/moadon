<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface,RemindableInterface{
	use UserTrait, RemindableTrait;
    protected $dates = ['deleted_at'];
	protected $table = 'users';
	protected $hidden = array('remember_token');
	protected $fillable = array('lastName','firstName','email','username','password','phone','states_id');
	public $timestamps = false;

	public function getId()
	{
	  return $this->id;
	}

	public function getAuthPassword()
	{
		return Hash::make($this->password);
	}
}