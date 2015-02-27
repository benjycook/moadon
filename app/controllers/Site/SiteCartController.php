<?php

class SiteCartController extends BaseController 
{
	protected function validateItem($item,$club,$qty,$cart)
	{
		if(!$cartItem = CartItem::where('items_id','=',$item->id)->where('carts_id','=',$cart->id)->first())
		{
			$cartItem =CartItem::create(array(
				'carts_id'=>$cart->id,
				'items_id'=>$item->id,
				'qty'=>$qty,
				'price'=>$item->netPrice,
				));
		}
		else
		{
			$cartItem->qty = $qty;
			$cartItem->save();
		}

		return $cartItem->toArray();
	}
	public function cart($slug)
	{
		Config::set('auth.model','Client');
		$client = Auth::user();
		if($client)
			$clients_id = $client->id;
		else
			$clients_id = 0;
		$club = Club::where('urlName','=',$slug)->first();
		if(!$club)
			return Response::json('מועדון זה לא נמצאה במערכת',404);
		$data = json_decode(Request::getContent(),true);
		if(!isset($data['cart_id'])||!$cart = Cart::find($data['cart_id']))
			$cart = Cart::create(array('clients_id'=>$clients_id));
		$info = array('cart_id'=>$cart->id,'items'=>array());
		$ids = array(-1);
		foreach ($data['items'] as $item) {
			$origin = Item::find($item['id']);
			if(!$origin||in_array($origin->id,$ids))
				continue;
			$ids[]  = $origin->id;
			$res = $this->validateItem($origin,$club,$item['qty'],$cart);
			if($res===false)
				continue;
			$info['items'][] = array(
				'qty'=>$res['qty'],
				'id'=>$res['items_id'],
				'total'=>$res['qty']*$res['price'],
				);
			
		}
		CartItem::where('carts_id','=',$cart->id)->whereNotIn('items_id',$ids)->delete();
		return Response::json($info,201);
	}
}