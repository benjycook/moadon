<?php

class SiteClientController extends BaseController 
{
	public function login($slug)
	{
		$json =	Request::getContent();
	  	$data	=	json_decode($json,true);
	  	$rules = array( 
            'username'  => 'required',
            'password'  => 'required'
        );
        $validator = Validator::make($data, $rules);
        if($validator->fails()) 
            return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים"),501);
	  	$client = CLient::whereHas("club",function($q) use($slug){
	  							$q->where('urlName', '=', $slug);
	  						})->where('username','=',$data['username'])->where('password','=',$data['password'])->first();
		if(!$client)
			return Response::json(array('error' => 'לקוח זה לא נמצא במערכת.'),401);
		Config::set('auth.model','Client');
		Auth::login($client,true);
		return Response::json('success', 201);
	}

	public function logout()
    {
        Auth::logout();
    }

    public function orders()
    {
    	$client = Auth::user();
    	$orders = Order::where('clients_id','=',$client->id)->get();
    	return Response::json($orders,200);
    }

    public function order($id)
    {
    	$client = Auth::user();
    	$order = Order::with("items")->where('clients_id','=',$client->id)->where('id','=',$id)->first();
    	return Response::json($order,200);
    }

    public function updateInfo()
    {
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
    	$json =	Request::getContent();
	  	$data = json_decode($json,true);
	  	if(!isset($data['carts_id']))
	  		return Response::json('סל קניות זה לא נמצא במערכת.',501);
	  	$client = Auth::user();
	  	$items = CartItem::where('carts_id','=',$data['carts_id'])->get();
	  	if(count($items))
	  	{
	  		$client->clients_id = $client->id;
	  		$client->createdOn = date("Y-m-d H:i:s");
	  		$order = Order::create($client->toArray());
	  		foreach ($items as $item) {
	  			$orderItem = Item::find($item->items_id);
	  			$orderItem->qty = $item->qty;
	  			$orderItem->orders_id = $order->id;
	  			OrderItem::create($orderItem->toArray());
	  		}
	  		CartItem::where('carts_id','=',$data['carts_id'])->delete();
	  		Cart::where('id','=',$data['carts_id'])->delete();
	  		return Response::json("ההזמנה בוצע בהצלחה.",201);
	  	}
	  	else
	  		return Response::json('לא נמצאו פריטים בסל קניות זה..',501);
    }
}