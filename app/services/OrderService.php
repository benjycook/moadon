<?php 
class OrderService
{
	protected static function generateKey($length)
    {
        $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($charset), 0, $length);
    }
	public static function createOrder($items,$client,$log)
	{
		$info = [];
		$client['clients_id'] = $client['id'];
		$client['createdOn'] = date("Y-m-d H:i:s");
		$key = static::generateKey(5);
    	while(Order::where('key','=',$key)->count()) 
    	{
    	 	$key = static::generateKey(5);
    	} 
    	$client['key'] = $key;
		$order = Order::create($client);
		$log->orders_id = $order->id;
		$log->save();
		CartItem::join('carts',function($q) use($log){
			$q->on('carts.id','=','carts_items.carts_id');
			$q->where('clients_id','=',$log->clients_id);
		})->delete();
		$total = 0;
		$info['suppliers'] = [];
		$docItems = [];
		$settings = Settings::find(1);
		foreach ($items as $item) {
			$orderItem = Item::find($item->items_id);
			$orderItem->items_id = $item->items_id;
			$orderItem->qty = $item->qty;
			$orderItem->orders_id = $order->id;
			$total += $orderItem->priceSingle*$item->qty;
			$supplier = SiteDetails::where('suppliers_id','=',$orderItem->supplier->id)->first();
			$orderItem->supplierName = $supplier->supplierName;
			$info['items'][] = $orderItem;
			$price = $orderItem->priceSingle/((floatval($settings->vat)/100)+1);
			$docItems[] = [
				'name'=>$supplier->supplierName."-".$orderItem->name,'price'=>$price,'qty'=>$orderItem->qty,
				'sku'=>$orderItem->sku,'measurementunits_id'=>1,'itemtypes_id'=>1,'stock'=>1,'taxable'=>1,'t6111_id'=>1010,
				'discount'=>0,
			];
			if(!isset($info['suppliers'][$supplier->suppliers_id]))
			{
				$city = City::find($supplier->cities_id);
				$supplier->city = $city->name;
				$info['suppliers'][$supplier->suppliers_id] = $supplier;
			}
			OrderItem::create($orderItem->toArray());
		}	
		
    	$url = URL::to("v".$key);
		$info['orderNum'] = $order->id;
		$info['client'] = $client;
		$msg[]	= "שלום ".$client['firstName'].",".PHP_EOL;
		$msg[]	= "תודה שרכשת בקופונופש - מועדון חברים!".PHP_EOL;
		$msg[]	= "מספר הזמנתך היא: ".$order->id."".PHP_EOL;
		$msg[]	= "לפרטי ההזמנה:".PHP_EOL;
		$msg[]	= "$url".PHP_EOL;
		$msg[]  = "קופונופש, מועדון חברים";
		$msg = implode('',$msg);
		$postUrl = Config::get('smsapi.url');
	    $projectKey = Config::get('smsapi.key');
	    $sms = new stdClass;
	    $sms->msg = $msg;
	    $sms->key = $projectKey;
	    $sms->senderNumber  = "0525001920";//"1700700400";
		$sms->resiverNumber = $client['mobile'];
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $postUrl);
		curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($sms));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = json_decode(curl_exec($ch),true);
		curl_close($ch);
		$invoiceUrl = Config::get('invoice.url','');
		$client['name'] = $client['firstName']." ".$client['lastName'];
		if(!isset($client['zipcode'])||!$client['zipcode']||$client['zipcode']=="")
			unset($client['zipcode']);
		$year = substr("0318",2);
		$month = substr("0318",0,2);
		$expDate = "$month/$year";
		$doc = new stdClass;
		$doc->token 			= Config::get('invoice.key','');
    	$doc->type    			= 320;
	    $doc->docs_id 			= 0;
	    $doc->discountType		= 1;
	    $doc->discountAmmount 	= 0;
	    $doc->roundingAmmount 	= 0;
	    $doc->dueDate 			= date('d/m/Y');
	    $doc->createdDate 		= date('d/m/Y');
	    $doc->dateOfVal 		= date('d/m/Y');
	    $doc->vatmodes_id		= 1;
	    $doc->languages_id		= "he";
	    $doc->currencies_id		= "ILS";
	    $doc->notes 			= "";
	    $doc->discount 			= 0;
	    $doc->client 			= $client;
	    $doc->items 			= $docItems;
	    $doc->payments          = [
	    	[
	    		"paymenttypes_id"=>3,"creditcardtypes_id"=>1,"creditdealtypes_id"=>1,
	    		"date"=> $expDate,"ammount"=>$log->amount,"bank"=>0,"branch"=>0,"payments"=>1,
	    		 "firstPayment"=>$log->amount,"account"=>"","number"=> substr($log->cardmask,-4)
	    	],
	    ];
	    $ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $invoiceUrl);
		curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($doc));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = json_decode(curl_exec($ch),true);
		curl_close($ch);

		Mail::send('mail.order',$info,function($message) use($info){
            $message->to($info['client']['email'])->subject("קופונופש - מועדון חברים: הזמנה מס' ".$info['orderNum']);
        }); 
        $cartItems = CartItem::join('carts',function($q) use($log){
			$q->on('carts.id','=','carts_items.carts_id');
			$q->where('clients_id','=',$log->clients_id);
		})->get();
        return [
			'order'	=> ['success'=>$order->id],
			'cart'	=> $cartItems,
			'result'=> $result,
		];
	}
}