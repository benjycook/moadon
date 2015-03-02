<?php

class SiteBaseController extends BaseController 
{

	public function callAction($method, $parameters)
	{
		$subdomain = array_shift($parameters);

		$club = Club::where('urlName','=',$subdomain)->first();

		if(!$club)
			return "מועדון זה לא קיים";

		$this->club = $club;
		
		return parent::callAction($method, $parameters);
	}


	public function __construct(Tymon\JWTAuth\JWTAuth $auth)
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
		$token = $this->auth->getToken();
		if(!$token)
		{
			return false;
		}
		$token = $token->get();
		$this->payload = $this->auth->getPayload($token)->toArray();
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