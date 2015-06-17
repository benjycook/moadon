<?php

class SiteIndexController extends SiteBaseController {

	public function index()
	{
		return View::make('site.index', ['club' => $this->club]);
	}

	public function contact()
	{
		$json   = Request::getContent();
    	$data   = json_decode($json,true);
    	$rules = [
    		'firstName'	=>	'required',
    		'lastName'	=>	'required',
    		'email'		=>	'required|email',
    		'mobile'	=>	'required',
    		'message'	=>	'required',
    	]; 
    	$validator = Validator::make($data,$rules);
    	if($validator->fails())
    		return Response::json(array('error'=>"אנא וודא שסיפקת את כל הנתונים הדרושים"),501);
    	$template = "mail/contact";
    	$subject  = "התקבלה פנייה חדשה מהאתר מאת: ".$data['firstName']." ".$data['lastName'];
    	$data['date'] = date("d/m/Y");
    	$data['emailMessage'] = $data['message'];
    	unset($data['message']);//message is Mail reserved name
    	SendingService::sendEmail(Config::get('emails.contact'),$template,$subject,$data);
    	return Response::json('נשלח בהצלחה',201);
	}
}