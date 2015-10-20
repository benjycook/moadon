<?php
return array(
	'driver' => 'smtp',
	'host' => 'email-smtp.eu-west-1.amazonaws.com',
	'port' => 587,
	'from' => array('address' => "moadon@webt.co.il", 'name' => "מועדון ווב טק"),
	'encryption' => 'tls',
	'username' => "AKIAJTKPTHGN3VMZSL2Q",
	'password' => "AsRPiEEMQJfjJEqaosNmzDzWNzdmz16ugKwKm53/MBWy",
	'sendmail' => '/usr/sbin/sendmail -bs',
	'pretend' => true,
);