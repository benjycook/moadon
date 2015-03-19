<?php

class SiteBaseController extends BaseController 
{

	public function callAction($method,$parameters)
	{
		$subdomain = array_shift($parameters);

		$club = Club::where('urlName','=',$subdomain)->first();
		if(!$club)
		{
			$key = str_replace(URL::to('/')."/v","",Request::url());
			$order = Order::with('items')->where('key','=',$key)->first();
			if(!$order)
				return "מועדון זה לא קיים";
			$data = [];
			foreach ($order->items as &$item) {
				$item->supplierName = $item->supplier->name;
				$data['items'][] = $item;
			}
			$data['orderNum'] = $order->id;
			$data['client']['firstName'] = $order->firstName;
			$data['client']['lastName']  = $order->lastName;
			return View::make('mail.order',$data);
		}
		$this->club = $club;
		
		return parent::callAction($method, $parameters);
	}


	public function __construct(Igorgoroshit\TokenAuth\TokenAuth $auth)
	{
		$this->auth = $auth;
		if($this->_setPayload())
		{
				$this->_setClient();
			$this->_setCart();
		}

	}

	protected function _setPayload()
	{
		try{
			$token = $this->auth->getToken();
			if(!$token)
			{
				return false;
			}
			$token = $token->get();
			$this->payload = $this->auth->getPayload($token)->toArray();
		}catch(Exception $e)
		{
			return false;
		}

		return true;
	}

	protected function _setClient()
	{
		$client = Client::find($this->payload['user']);
		if($client)
			$this->client = $client;
		else
			$this->client = null;
	}

	protected function _setCart()
	{
		
		$cart = Cart::find($this->payload['cart_id']);
		//print_r($this->payload); die($cart->id);
		if($cart)
			$this->cart = $cart;
		else
			$this->cart = null;
	}
}