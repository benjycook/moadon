<?php

class SiteOrdersController extends SiteBaseController 
{

	protected function generateKey($length)
    {
        $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($charset), 0, $length);
    }

	public function index()
	{
		$orders = $this->client->orders()->with('items')->get()->toArray();
    return Response::json($orders, 200);
	}

	public function show($id)
	{
		$order = $this->client->order()->with('items')->where('id', '=', $id);
		return Response::json($order, 200);
	}

	public function store()
	{
		$data = json_decode(Request::getContent(),true);
		$items = $this->cart->items()->get();
		$data['cardExp'] = date('m',strtotime($data['cardYear']."-".$data['cardMonth']."-01"))."/".date('y',strtotime($data['cardYear']."-01-01"));
		if(!preg_match("/(0[1-9]|1[0-2])\/[0-9]{2}/",$data['cardExp']))
	     	$data['cardExp'] = date("Y-m-t",strtotime('+1 years'));
	    else
	    {
	      $date = explode('/',$data['cardExp']);
	      $year = date('Y',strtotime($date[1]."-01-01"));
	      $date = date('Y-m-t',strtotime($year."-".$date[0]."-01"));
	      $data['date'] = $date;
	    }
		$info = [];
		if(count($items) < 1)
			return Response::json('לא נמצאו פריטים בסל קניות זה.',501);

		$client = $this->client->toArray();
		$client['clients_id'] = $this->client->id;
		$client['createdOn'] = date("Y-m-d H:i:s");
		$key = $this->generateKey(5);
    	while(Order::where('key','=',$key)->count()) 
    	{
    	 	$key = $this->generateKey(5);
    	} 
    	$client['key'] = $key;
		$order = Order::create($client);
		$total = 0;

		foreach ($items as $item) {
			$orderItem = Item::find($item->items_id);
			$orderItem->items_id = $item->items_id;
			$orderItem->qty = $item->qty;
			$orderItem->orders_id = $order->id;
			$total += $orderItem->priceSingle*$item->qty;
			$orderItem->supplierName = $orderItem->supplier->name;
			$info['items'][] = $orderItem;
			OrderItem::create($orderItem->toArray());
		}
		//card date here!!!!!!!!!!


	    // if($data['numberOfPayments'] > 1)
	    // 	$data['creditDealType'] = 2;
	    // else
	    	$data['creditDealType'] = 1;

	    $data['firstPayment'] = $total;//$total/$data['numberOfPayments'];
	    $data['total'] = $total;
	    $data['creditCardType'] = 1;
		$data['orders_id'] = $order->id;

		Payment::create($data);
		
    	$url = URL::to("v".$key);
		$this->cart->items()->delete();
		$info['orderNum'] = $order->id;
		$info['client'] = $client;
		$msg[]	= "שלום ".$client['firstName'].",".PHP_EOL;
		$msg[]	= "תודה שרכשת בקופונופש - מועדון חברים!".PHP_EOL;
		$msg[]	= "מספר הזמנתך היא: ".$order->id."".PHP_EOL;
		$msg[]	= "לפרטי ההזמנה:".PHP_EOL;
		$msg[]	= "$url".PHP_EOL;
		// $msg[]  = "קופונופש, מועדון חברים";
		$msg = implode('',$msg);
		$postUrl = Config::get('smsapi.url');
	    $projectKey = Config::get('smsapi.key');
	    $sms = new stdClass;
	    $sms->msg = $msg;
	    $sms->key = $projectKey;
	    $sms->senderNumber  = "0525001920";//1700700400
		$sms->resiverNumber = $client['mobile'];
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $postUrl);
		curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($sms));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = json_decode(curl_exec($ch),true);
		curl_close($ch);
		Mail::send('mail.order',$info,function($message) use($info){
            $message->to($info['client']['email'])->subject("קופונופש - מועדון חברים: הזמנה מס' ".$info['orderNum']);
        }); 

		return Response::json([
			'success' => $order->id
			],201);
  		
	}

}