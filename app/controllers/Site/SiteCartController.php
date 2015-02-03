<?php

class SiteCartController extends BaseController 
{
	protected function validateItem($item,$club,$qty,$cart)
	{
		if(!$item)
			return false;
		if(!$cartItem = CartItem::where('items_id','=',$item->id)->where('carts_id','=',$cart->id)->first())
		{
			$cartItem =CartItem::create(array(
				'carts_id'=>$cart->id,
				'items_id'=>$res->id,
				'qty'=>$res->qty,
				'price'=>$res->price,
				));
		}
		else
		{
			$cartItem->qty = $qty;
			$cartItem->save();
		}
		return = array(
			'id'=>$item->id,
			'qty'=>$qty,
			'price'=>$item->netPrice,
			);
	}
	public function cart($slug)
	{
		$club = Club::where('urlName','=',$slug)->first();
		if(!$club)
			return Response::json('מועדון זה לא נמצאה במערכת',404);
		$data = json_decode(Request::getContent(),true);
		if(!isset($data['cart_id'])||$cart = Cart::find($data['id']))
			$cart = Cart::create(array());
		$info = array('cart_id'=>$cart->id,'items'=>array());
		foreach ($data['items'] as $item) {
			$origin = Item::find($item['id']);
			$res = $this->validateItem($origin,$club,$item['qty'],$cart);
			if($res===false)
				continue;
			$info['items'][] = array(
				'qty'=>$res->qty,
				'id'=>$res->id,
				'total'=>$res->qty*$res->price,
				);
			
		}
		return Response::json($info,201);
	}
}