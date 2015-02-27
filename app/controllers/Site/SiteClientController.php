<?php

class SiteClientController extends BaseController 
{

	public function register($slug)
	{
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
            'cart_id'		=> 'required'
        );
        $validator = Validator::make($data, $rules);
        if($validator->fails()) 
            return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים"),501);
    	if(!$club = Club::where('urlName','=',$slug)->first())
    		return Response::json(array('error'=>'מועדון זה לא נמצא במערכת'),501);
    	if(isset($data['taxId'])&&Client::where('taxId','=',$data['taxId'])->where('id','=',$club->id)->count())
    		return Response::json(array('error'=>'ת"ז זו כבר קיימת במערכת'),501);
    	if($count = Client::where('email','=',$data['email'])->where('clubs_id','=',$club->id)->count())
    		return Response::json(array('error'=>'דוא"ל זה כבר קיימת במערכת'),501);
    	$data['clubs_id'] = $club->id;
    	$client = Client::create($data);
    	Config::set('auth.model','Client');
		Auth::login($client,true);
		$this->bindCart($data['cart_id'],$client->id);
		return Response::json($client,201);

	}
	protected function bindCart($cart_id,$client)
	{
		$cart = Cart::where('id','=',$cart_id)->where('clients_id','=',0)->first();
		if($cart)
		{
			$cart->clients_id = $client;
			$cart->save();
		}
	}
	public function login($slug)
	{
		$json =	Request::getContent();
	  	$data	=	json_decode($json,true);
	  	$rules = array( 
            'email'  => 'required|email',
            'password'  => 'required'
        );
        $validator = Validator::make($data, $rules);
        if($validator->fails()) 
            return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים"),501);
	  	$client = CLient::whereHas("club",function($q) use($slug){
	  							$q->where('urlName', '=', $slug);
	  						})->where('email','=',$data['email'])->where('password','=',$data['password'])->first();
		if(!$client)
			return Response::json(array('error' => 'לקוח זה לא נמצא במערכת.'),401);
		Config::set('auth.model','Client');
		Auth::login($client,true);
		if(isset($data['cart_id']))
			$this->bindCart($data['cart_id'],$client->id);
		return Response::json($client,200);
	}

	public function logout()
    {
    	Config::set('auth.model','Client');
        Auth::logout();
    }

    public function orders()
    {
    	Config::set('auth.model','Client');
    	$client = Auth::user();
    	$orders = Order::where('clients_id','=',$client->id)->get();
    	return Response::json($orders,200);
    }

    public function order($id)
    {
    	Config::set('auth.model','Client');
    	$client = Auth::user();
    	$order = Order::with("items")->where('clients_id','=',$client->id)->where('id','=',$id)->first();
    	return Response::json($order,200);
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

    public function purchase()
    {
    	Config::set('auth.model','Client');
    	$json =	Request::getContent();
	  	$data = json_decode($json,true);
	  	$client = Auth::user();
	  	$cart = Cart::where('clients_id','=',$client->id);
	  	$items = CartItem::where('carts_id','=',$cart->id)->get();
	  	if(count($items))
	  	{
	  		$client->clients_id = $client->id;
	  		$client->createdOn = date("Y-m-d H:i:s");
	  		$order = Order::create($client->toArray());
	  		$total = 0;
	  		foreach ($items as $item) {
	  			$orderItem = Item::find($item->items_id);
	  			$orderItem->qty = $item->qty;
	  			$orderItem->orders_id = $order->id;
	  			$total = $orderItem->netPrice*$item->qty;
	  			OrderItem::create($orderItem->toArray());
	  		}
	  		if(!preg_match("/(0[1-9]|1[0-2])\/[0-9]{2}/",$data['cardExp']))
                $data['cardExp'] = date("Y-m-t",strtotime('+1 years'));
            else
            {
                $date = explode('/',$data['cardExp']);
                $year = date('Y',strtotime($date[1]."-01-01"));
                $date = date('Y-m-t',strtotime($year."-".$date[0]."-01"));
                $data['date'] = $date;
            }
            if($data['numberOfPayments']>1)
            	$data['creditDealType'] = 2;
            else
            	$data['creditDealType'] = 1;
            $data['firstPayment'] = $total/$data['numberOfPayments'];
            $data['total'] = $total;
            $data['creditCardType'] = 1;
	  		$data['orders_id'] = $order->id;
	  		Payment::create($data);
	  		CartItem::where('carts_id','=',$data['cart_id'])->delete();
	  		Cart::where('id','=',$data['cart_id'])->delete();
	  		return Response::json("ההזמנה בוצע בהצלחה.",201);
	  	}
	  	else
	  		return Response::json('לא נמצאו פריטים בסל קניות זה.',501);
    }
}