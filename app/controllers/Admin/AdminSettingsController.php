<?php

class AdminSettingsController extends BaseController 
{

	public function show($id)
	{
		$settings = Settings::find(1);
		return Response::json($settings,200);
	}

	public function update($id)
	{
		$json=Request::getContent();
	    $data=json_decode($json,true);
		$settings = Settings::find(1);
    	$settings->fill($data);
    	$settings->save();
    	return Response::json(array('sucsess'=>"הצלחה"),201);
	}

}