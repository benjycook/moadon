<?php 
class AccountingController extends BaseController
{
	protected function address($info)
	{
		if($info['entrance']&&$info['apartment'])
			$address = $info['city']." ".$info['street']." ".$info['house']."/".$info['apartment']." כניסה ".$info['entrance'];
		if(!$info['entrance']&&$info['apartment'])
			$address = $info['city']." ".$info['street']." ".$info['house']."/".$info['apartment'];
		if($info['entrance']&&!$info['apartment'])
			$address = $info['city']." ".$info['street']." ".$info['house']." כניסה ".$info['entrance'];
		if(!$info['entrance']&&!$info['apartment'])
			$address = $info['city']." ".$info['street']." ".$info['house'];
		return $address;
	}

	public function orders()
	{
		$key = Input::get('key',0);
		$start = Input::get('from',0);
		$end   = Input::get('to',0);
		if(!$key)
			return Response::json("אנא ספק מזהה.",401);
		$dealsKey = Config::get('dealsApi.dealsKey',0);
		if($dealsKey!=$key)
			return Response::json("אנא ספק מזהה.",401);
		$start 	= date("Y-m-d",strtotime($start));
		$end 	= date("Y-m-d",strtotime($end));
		$orders = Order::with(['items'=>function($q){$q->with('sitedetails');}],'payment')->whereRaw('DATE(createdOn) >= ? AND DATE(createdOn) <= ?',[$start,$end])->orderBy('createdOn','ASC')->get();
		$xml = simplexml_load_file(public_path()."/base.xml");
		foreach ($orders as $order) {
			$doc = $xml->addChild('doc');
			$docInfo = $doc->addChild('docinfo');
			$docInfo->addChild('type',320);
			$docInfo->addChild('number',$order->id+50000);
			$client = $docInfo->addChild('client');
			$client->addChild('firstname',$order->firstName);
			$client->addChild('lastname',$order->lastName);
			$client->addChild('mobile',$order->mobile);
			$client->addChild('address',$this->address($order));
			$date = str_replace('-','',date('Y-m-d',strtotime($order->createdOn)));
			$docInfo->addChild('date',$date);
			$total = 0;
			$docItems = $doc->addChild('items');
			foreach ($order->items as $item) {
				$subject = $docItems->addChild('item');
			 	$total+=$item->qty*$item->priceSingle;
			 	$subject->addChild('code',$item->sku);
			 	$subject->addChild('quantity',$item->qty);
			 	$subject->addChild('price',$item->priceSingle);
			 	$subject->addChild('total',$item->qty*$item->priceSingle);
			 	$subject->addChild('description',$item->sitedetails->supplierName." - ".$item->name);
			} 
			$docPayments = $doc->addChild('payments');
		    $temp = $order->payment;
		 	$subject = $docPayments->addChild('payment');
		 	$subject->addChild('type',1);//$temp['creditDealType']);
		 	$subject->addChild('cardtype',$temp['creditCardType']);
		 	$subject->addChild('cardnumber',substr($temp['cardmask'],-4));
		 	$subject->addChild('voucher',$temp['auth']);

		 	$expDate = date('m',strtotime($temp['date']))."/".date('y',strtotime($temp['date']));
		 	$subject->addChild('cardexp',$expDate);

		 	$numberOfPayments = $temp['payments']==0 ? 1: $temp['payments'];
		 	$subject->addChild('payments',$numberOfPayments);

		 	$subject->addChild('firstpaymentsum',$temp['firstpayment']);
		 	$subject->addChild('additionalpaymentsum',0);
		 	$subject->addChild('total',$temp['amount']);
		 	$subject->addChild('ownerid',$temp['holderid']);
			$docInfo->addChild('orderid',$order->id);
		}			
		return $xml->asXML();
	}
}
