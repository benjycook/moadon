<?php 
class OrderService
{
	protected static function calculateLuhn($subject)
	 {
	 	$subject = (string)$subject;
	    $sum = 0;
	    for ($i=0; $i<strlen($subject);$i++)
		{

			$sum += intval($subject[$i]);
		}
		$delta = [0,1,2,3,4,-4,-3,-2,-1,0];
		for ($i=(strlen($subject)-1); $i>=0;$i-=2)
	    {		
			$deltaIndex = intval($subject[$i]);
			$deltaValue = $delta[$deltaIndex];	
			$sum += $deltaValue;
		}	

		$mod10 = $sum%10;
		$mod10 = 10-$mod10;	
		if($mod10==10)	
			$mod10=0;
		return $mod10;
	 }
	protected static function generateKey($length)
    {
        $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($charset), 0, $length);
    }

	public static function createOrder($items,$client,$log,$club)
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
    	$client['orders_statuses_id'] = 1;
		$order = Order::create($client);
    	$code = $orgCode =(string)(1000000+$order->id);
    	$code = $code."".rand(1000,9999);
    	for ($i=0; $i < 3; $i++) { 
    		$temp = static::calculateLuhn($code);
    		$orgCode = $orgCode."".$temp;
    		$code = $code."".$temp;
    	}
    	$order->code = $info['code'] = $orgCode;
    	$order->save();
		$log->orders_id = $order->id;
		$log->save();
		$total = 0;
		$info['suppliers'] = [];
		$docItems = [];
		
		$settings = Settings::find(1);
		$vatPercent = floatval($settings->vat) / 100 + 1;

		foreach ($items as $item) {
			$cart = $item->carts_id;

			$orderItem = Item::find($item->items_id);

			$orderItem->items_id = $item->items_id;
			$orderItem->orders_id = $order->id;

			
			$supplier = SiteDetails::where('suppliers_id','=',$orderItem->supplier->id)->first();
			$orderItem->supplierName = $supplier->supplierName;
			
			$orderItem->qty = $item->qty;
			
			$info['items'][] = $orderItem;

			$totalDiscount 	= 1 - (($club->creditDiscount + $club->regularDiscount) / 100);
			$creditDiscount = 1 - ($club->creditDiscount / 100);

			$orderItem->noCreditDiscountPrice = $orderItem->priceSingle;
			$orderItem->noDiscountPrice 			= $orderItem->priceSingle / $totalDiscount;

			if($club->creditDiscount > 0)
			{
				$orderItem->noCreditDiscountPrice = $orderItem->priceSingle / $creditDiscount;
			}

			$total = $orderItem->noCreditDiscountPrice * $orderItem->qty / $vatPercent;
			$price = $total / $orderItem->qty;

			$docItems[] = [
				'name'								=>	$supplier->supplierName." - ".$orderItem->name,
				'price'								=>	$price,
				'qty'								=>	$orderItem->qty,
				'itemtypes_id'						=>	1,
				'total'								=>	$total,
				'sku'								=>	$orderItem->items_id,
				'measurementunits_id'				=>	1,
				'stock'								=>	1,
				'taxable'							=>	1,
				't6111_id'							=>	1010,
				'discount'							=>	0,
			];

			if(!isset($info['suppliers'][$supplier->suppliers_id]))
			{
				$city = City::find($supplier->cities_id);
				$supplier->city = $city->name;
				$info['suppliers'][$supplier->suppliers_id] = $supplier;
			}
			
			OrderItem::create($orderItem->toArray());

			$originItem = CartItem::where('carts_id','=',$item->carts_id)->where('items_id','=',$item->items_id)->first();
			if($originItem)
			{
				$qty = $originItem->qty-$item->qty;
				if($qty>0)
				{
					$originItem->qty = $qty;
					$originItem->save();
				}
				else
					$originItem->delete();
			}
		}	
    	$url = URL::to("v".$key);
		$info['orderNum'] = $order->id;
		$info['client'] = $client;
		$msg[]	= "שלום ".$client['firstName'].",".PHP_EOL;
		$msg[]	= "תודה שרכשת בקופונופש - מועדון חברים!".PHP_EOL;
		$msg[]	= "קוד הזמנתך הוא: ".$order->code."".PHP_EOL;
		$msg[]	= "לפרטי ההזמנה:".PHP_EOL;
		$msg[]	= "$url".PHP_EOL;
		$msg[]  = "קופונופש, מועדון חברים";
		$msg = implode('',$msg);
		$postUrl = Config::get('smsapi.url');
    $projectKey = Config::get('smsapi.key');
    
    $sms = new stdClass;
    $sms->msg = $msg;
    $sms->key = $projectKey;
    $sms->senderNumber  = "0509995449";

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
		$year = substr($log->exp,2);
		$month = substr($log->exp,0,2);
		$expDate = "$month/$year";
		$doc = new stdClass;
		$doc->token 							= Config::get('invoice.key','');
    	$doc->type    					= 320;
	    $doc->docs_id 					= 0;
	    $doc->discountType				= 1;
	    $doc->discountAmmount 			= 0;
	    $doc->roundingType 				= 1;
	    $doc->dueDate 					= date('d/m/Y');
	    $doc->createdDate 				= date('d/m/Y');
	    $doc->dateOfVal 				= date('d/m/Y');
	    $doc->vatmodes_id				= 1;
	    $doc->languages_id				= "he";
	    $doc->currencies_id				= "ILS";
	    $doc->rounding          		= true;
	    $doc->notes 						= "";
	    $doc->discount 					= 0;
	    $doc->client 						= $client;
	    $doc->items 						= $docItems;
	    $doc->payments          = [
	    	[
					"paymenttypes_id"=>3,
					"creditcardtypes_id"=>1,
					"creditdealtypes_id"=>1,
					"date"=> $expDate,
					"ammount"=>$log->amount,
					"bank"=>0,
					"branch"=>0,
					"payments"=>1,
					"firstPayment"=>$log->amount,
					"account"=> $log->auth,
					"number"=> substr($log->cardmask,-4)
	    	],
	    ];

	  if(empty($client['email']))
	  {
	  	$clientEmail = $_ENV['DEFUALT_EMAIL'];
	  }else{
	  	$clientEmail = $client['email'];
	  }

    $doc->sendInvoice = new stdClass;
    $doc->sendInvoice->subject  = "חשבונית מס קבלה - קופונופש - מועדון חברים";
    $doc->sendInvoice->email    = $clientEmail;
    $doc->sendInvoice->content  = "שלום ".$client['name'].",<br>תודה על רכישתך באתר קופונופש-מועדון חברים!<br>מצורף בקובץ חשבונית מס קבלה.<br><br>יום טוב";
    
    $ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $invoiceUrl);
		curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($doc));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = json_decode(curl_exec($ch),true);
		curl_close($ch);
		if(!isset($result['error']))
		{
			$order->docNumber = $result['number'];
			$order->save();
		}



		Mail::send('mail.order',$info,function($message) use($info, $clientEmail){
            $message->to($clientEmail)->subject("קופונופש - מועדון חברים: הזמנה מס' ".$info['orderNum']);
    }); 

    return [
			'order'	=> ['success'=>$order->code],
			'carts_id'	=> $cart,
			'result'=> $result,
		];
	}
}