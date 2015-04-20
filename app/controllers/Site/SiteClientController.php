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
            return Response::json(array('error'=>"אנא וודא שסיפקת את כל הנתונים"),501);

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
            $message->to($data['email'])->subject('תודה שנרשמת לקופונופש - מועדון חברים!');
        }); 
        return Response::json(compact('token', 'claims', 'client'), 200);
	}
	
    protected function bindCart($client)
	{
        $cart = Cart::where('clients_id','=',$client)->first();
        if($cart)
        {
            $id    = $this->cart->id;
            $items = CartItem::where('carts_id','=',$cart->id)->get();
            foreach ($items as $item) {
                if($temp = CartItem::where('carts_id','=',$id)->where('items_id','=',$item->items_id)->first())
                {
                    $temp->qty = $temp->qty+$item->qty;
                    $temp->save();
                    $item->delete();
                }
                else
                {
                    $item->carts_id = $id;
                    $item->save();
                }
            }
            Cart::where('id','=',$cart->id)->delete();
        }
        else
        {
            $this->cart->clients_id = $client;
            $this->cart->save();
        }

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
            return Response::json(array('error'=>"אנא וודא שסיפקת את כל הנתונים."),501);
	  	
        $client = $club->clients()->where('email','=',$data['email'])
                                  ->where('password','=',$data['password'])
                                  ->select('id', 'firstName', 'lastName')->first();
 

		if(!$client)
			return Response::json(array('error' => 'שם משתמש או סיסמא אינם נכונים.'),403);
        
        $this->bindCart($client->id);

        $claims = array(
            'user'          => $client->id,
            'loginType'     => 'client'
        );

        $token = TokenAuth::make('client', $claims);
        $cart  = $this->_getCart($this->cart->id);
        return Response::json(compact('token', 'claims', 'client','cart'), 200);
	}

    public function userInfo()
    {
        $client = $this->client->toArray();
        $client = Client::where('id','=',$client['id'])->select(['firstName','lastName','email','mobile'])->first();
        $client->password = "";
        return Response::json($client,200);
    }
    public function updateInfo()
    {
    	$json =	Request::getContent();
	  	$data	=	json_decode($json,true);
        $client = $this->client->toArray();
        $client = Client::find($client['id']);
    	$allowed = ['firstName','lastName','email','mobile','password'];
    	foreach ($data as $key => $value) {
    		if(!in_array($key,$allowed))
    			unset($data[$key]);
    	}
        if(is_null($data['password'])||$data['password']=="")
            unset($data['password']);
    	$client->fill($data);
    	$client->save();
    	return Response::json('הפרטים עודכנו בהצלחה.',200);
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
            return Response::json(array('error'=>"אנא וודא שסיפקת את כל הנתונים"),501);
        
        $client = $club->clients()->where('email','=',$data['email'])->select('id', 'firstName', 'lastName','password','email')->first();
        if(!$client)
            return Response::json(array('error' => 'לקוח זה לא נמצא במערכת.'),403);
        $client = $client->toArray();
        $client['clubUrl'] = URL::to('/');
        Mail::send('mail.passReminder',$client,function($message) use($client){
            $message->to($client['email'])->subject("קופונופש - מועדון חברים: תזכורת סיסמא");
        }); 
        return Response::json('הסיסמא נשלחה לדו"אל שלך.',200);
    }

    // public function restore()
    // {
    //     $data = json_decode(Request::getContent(),true);
    //     $rules = array( 
    //         'email'    => 'required|email'
    //     );

    //     $validator = Validator::make($data, $rules);
    //     if ($validator->fails()) 
    //     {
    //         return Response::json(array('error'=>$validator->messages()->first()), 501);
    //     }
    //     else 
    //     {
    //         $result = Client::whereRaw('email = ?',array($data['email']))->first();
    //         if(!$result)
    //             return Response::json(array('error'=>'כתובת דוא"ל לא נמצאה במערכת'), 501);
    //         elseif($result->states_id == 1)
    //                 return Response::json(array('error'=>'המשתמש אינו פעיל אנא פנה למנהל מערכת'), 501);
    //             else
    //             {
    //                 Mail::send('mail.passReminder', array(
    //                  'password'     =>  $result->password,
    //                  'firstName'    =>  $result->firstName,
    //                  'lastName'     =>  $result->lastName,
    //                  'clubUrl'      =>  URL::to('/'),
    //                  ), function($message) use($result) 
    //                 {
    //                     $message->to($result->email)->subject('תזכורת סיסמא-קופונופש מועדון חברים');
    //                 });
    //                 return Response::json('הסיסמא נשלחה לדוא"ל שלך', 200);
    //             }
    //     }
    // }
}