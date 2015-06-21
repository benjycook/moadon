<?php

class SiteCartController extends SiteBaseController 
{

	protected function validateItem($item,$club,$qty,$cart)
	{
		if(!$cartItem = CartItem::where('items_id','=',$item->id)->where('carts_id','=',$cart->id)->first())
		{
			$cartItem =CartItem::create(array(
				'carts_id'=>$cart->id,
				'items_id'=>$item->id,
				'qty'=>$qty,
				'price'=>$item->priceSingle,
				));
		}
		else
		{
			$cartItem->qty = $qty;
			$cartItem->save();
		}

		return $cartItem->toArray();
	}

	public function cart()
	{
		$data = json_decode(Request::getContent(),true);

		$cart = $this->cart;

		$info = [	
			'cart_id' => $cart->id,	
			'items' => []
		];

		$ids = array(-1);

		foreach ($data['items'] as $item) 
		{
			$origin = Item::find($item['id']);
			if(!$origin||in_array($origin->id,$ids))
				continue;
			$ids[]  = $origin->id;
			$res = $this->validateItem($origin,$this->club,$item['qty'],$cart);
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

	public function getCart()
	{
		return Response::json($this->_getCart($this->cart->id),201);
	}
}