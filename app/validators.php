<?php
Validator::extend('alpha_spaces', function($attribute, $value)
{
    return preg_match('/^[\pL\s]+$/u', $value);
});


Validator::extend('id_check', function($attribute, $value)
{
	$test=str_split($value);
	$sum=0;
	for ($i=1; $i < strlen($value)+1; $i++) 
	{ 
		if($i%2)
			$num=$test[$i-1]*1;
		else
			$num=$test[$i-1]*2;
		if($num>9)
		{
			$temp=str_split($num);
			$num=$temp[0]+$temp[1];
		}
		$sum+=$num;
	}
	if($sum%10)
		return false;
	return true;
});