<?php

class SiteOrdersController extends SiteBaseController 
{

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

		if(count($items) < 1)
			return Response::json('לא נמצאו פריטים בסל קניות זה.',501);

		$client = $this->client->toArray();
		$client['clients_id'] = $this->client->id;
		$client['createdOn'] = date("Y-m-d H:i:s");
		$order = Order::create($client);
		$total = 0;

		foreach ($items as $item) {
			$orderItem = Item::find($item->items_id);
			$orderItem->qty = $item->qty;
			$orderItem->orders_id = $order->id;
			$total += $orderItem->netPrice*$item->qty;
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

    if($data['numberOfPayments'] > 1)
    	$data['creditDealType'] = 2;
    else
    	$data['creditDealType'] = 1;

    $data['firstPayment'] = $total/$data['numberOfPayments'];
    $data['total'] = $total;
    $data['creditCardType'] = 1;
		$data['orders_id'] = $order->id;

		Payment::create($data);

		$this->cart->items()->delete();
		return Response::json([
			'success' => "הזמנתך בוצע בהצלחה.<br />מספר ההזמנה שלך הוא: $order->id"
			],201);
  		
	}


}