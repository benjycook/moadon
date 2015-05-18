<?php
class GatewayLog extends Eloquent {
	protected $table = 'creditguardlog';
	public $timestamps = true;

	public function item()
	{
		return $this->belongsTo('Item','reference', 'id');
	}
	
}