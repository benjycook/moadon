<?php

class SiteClientController extends SiteBaseController 
{

	public function register()
	{
        $club = $this->club;
		$json   = Request::getContent();
    	$data   = json_decode($json,true);
    	$rules = array( 
            'email' 	 	=> 'required|email',
            'firstName'  	=> 'required',
            'lastName'   	=> 'required',
            'password'   	=> 'required',
            'mobile'     	=> 'required',
           // 'recieveNews'  	=> 'required',
           // 'taxId'  		=> 'sometimes|id_check',
           // 'cart_id'		=> 'required'
        );
        $validator = Validator::make($data, $rules);

        if($validator->fails()) 
            return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים"),501);

    	if(isset($data['taxId'])&&Client::where('taxId','=',$data['taxId'])->where('id','=',$club->id)->count())
    		return Response::json(array('error'=>'ת"ז זו כבר קיימת במערכת'),501);

    	if($count = Client::where('email','=',$data['email'])->where('clubs_id','=',$club->id)->count())
    		return Response::json(array('error'=>'דוא"ל זה כבר קיימת במערכת'),501);

    	$data['clubs_id'] = $club->id;
    	$client = Client::create($data);


        $this->bindCart($client->id);

        $claims = array(
            'user'          => $client->id,
            'loginType'     => 'client'
        );
        
        $token = TokenAuth::make('client', $claims);
        $data['clubUrl'] = URL::to('/');
        Mail::send('mail.clientReg',$data,function($message) use($data){
            $message->to($data['email'])->subject('תודה שנרשמת למועדונופש!');
        }); 
        return Response::json(compact('token', 'claims', 'client'), 200);
	}
	
    protected function bindCart($client)
	{
		$this->cart->clients_id = $client;
        $this->cart->save();
	}

	public function login()
	{
        $club = $this->club;

		$json =	Request::getContent();
	  	$data	=	json_decode($json,true);
	  	$rules = array( 
            'email'  => 'required|email',
            'password'  => 'required'
        );

        $validator = Validator::make($data, $rules);
        if($validator->fails()) 
            return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים"),501);
	  	
        $client = $club->clients()->where('email','=',$data['email'])
                                  ->where('password','=',$data['password'])
                                  ->select('id', 'firstName', 'lastName')->first();
 

		if(!$client)
			return Response::json(array('error' => 'לקוח זה לא נמצא במערכת.'),403);
        
        $this->bindCart($client->id);

        $claims = array(
            'user'          => $client->id,
            'loginType'     => 'client'
        );

        $token = TokenAuth::make('client', $claims);

        return Response::json(compact('token', 'claims', 'client'), 200);
	}

    public function updateInfo()
    {
    	Config::set('auth.model','Client');
    	$json =	Request::getContent();
	  	$data	=	json_decode($json,true);
    	$client = Auth::user();
    	$restrict = array('clubs_id','remember_token');
    	foreach ($data as $key => $value) {
    		if(in_array($key,$restrict))
    			unset($data[$key]);
    	}
    	$client->fill($data);
    	$client->save();
    	return Response::json($client,200);
    }


    public function passReminder()
    {
        $club = $this->club;
        $json = Request::getContent();
        $data   =   json_decode($json,true);
        $rules = array( 
            'email'  => 'required|email',
        );
        $validator = Validator::make($data, $rules);
        if($validator->fails()) 
            return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים"),501);
        
        $client = $club->clients()->where('email','=',$data['email'])->select('id', 'firstName', 'lastName','password','email')->first();
        if(!$client)
            return Response::json(array('error' => 'לקוח זה לא נמצא במערכת.'),403);
        $client = $client->toArray();
        $client['clubUrl'] = URL::to('/');
        Mail::send('mail.passReminder',$client,function($message) use($client){
            $message->to($client['email'])->subject("מועדונופש- תזכורת סיסמא");
        }); 
        return Response::json('הסיסמא נשלחה לדו"אל שלך.',200);
    }
}