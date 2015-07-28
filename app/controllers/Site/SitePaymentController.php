<?php 
class SitePaymentController extends SiteBaseController  {

		public function success()
		{
			$client = [];
			$mac 							= Input::get('responseMac');
			if($this->club->creditDiscount>0)
				$terminal = 'creditguard1';
			else
				$terminal = 'creditguard2';
			$password						= Config::get($terminal.'.password');
			$txid 							= Input::get('txId');
			$errorCode  				= Input::get('errorCode', '000');
			$cardtoken 					= Input::get('cardToken', '');
			$exp 								= Input::get('cardExp');
			$holderid						= Input::get('personalId', '');
			$uniqueid						= Input::get('uniqueID', '');
			$cardmask   				= Input::get('cardMask', '');
			$auth 							= Input::get('authNumber', '');
			$payments						= Input::get('numberOfPayments', 1);
			$firstpayment				= Input::get('firstPayment', 0);
			$otherpayment				= Input::get('periodicalPayment', 0);
			$parts = "$password$txid$errorCode$cardtoken$exp$holderid$uniqueid";
			$str = hash('sha256', $parts);

			if($str == $mac)
			{

				$log = GatewayLog::where('uniqueid', '=', $uniqueid)->first();
				$items = GatewayItem::where('gateway_id','=',$log->id)->get();
				$client = Client::where('id','=',$log->clients_id)->first()->toArray();
				
				$log->success 				= 1;
				//extended data on payment	
				$log->cardmask				= $cardmask;
				$log->exp					= $exp;
				$log->cardtoken				= $cardtoken;
				$log->holderid				= $holderid;
				$log->holdername			= $client['firstName']." ".$client['lastName'];
				$log->auth					= $auth;
				$log->rcode 				= $errorCode;
				$log->payments				= $payments;
				$log->firstpayment    = $firstpayment;
				$log->otherpayment    = $otherpayment;

				$log->save();
				$info = OrderService::createOrder($items,$client,$log,$this->club);
				$info['cart'] =  $this->_getCart($info['carts_id']);

				
				return '
					<script>
					    function preventBack(){window.history.forward();}
					    setTimeout("preventBack()", 0);
					    window.onunload=function(){null};
							var App = window.parent.App;
							var currentRouteName = App.__container__.lookup("controller:application").get("currentRouteName");
							var currentRoute = App.__container__.lookup("route:"+currentRouteName);
							currentRoute.send("success",'.json_encode($info).');
					</script>
				';
			}
		

			return '
				<script>
						function preventBack(){window.history.forward();}
						setTimeout("preventBack()", 0);
						window.onunload=function(){null};
						var App = window.parent.App;
						var currentRouteName = App.__container__.lookup("controller:application").get("currentRouteName");
						var currentRoute = App.__container__.lookup("route:"+currentRouteName);
						currentRoute.send("error");
				</script>
			';
		}


		public function error()
		{
			$data = Input::all();

			// return View::make('site.creditguarderror', $data);
			$data = json_encode($data);
			$eval = "'$data'";

			return '
				<script>
						function preventBack(){window.history.forward();}
						setTimeout("preventBack()", 0);
						window.onunload=function(){null};
						var App = window.parent.App;
						var currentRouteName = App.__container__.lookup("controller:application").get("currentRouteName");
						var currentRoute = App.__container__.lookup("route:"+currentRouteName);
						var jsondata = '.$eval.';
						var parsedjsondata = JSON.parse(jsondata);
						currentRoute.send("creditGuardError", parsedjsondata);
				</script>
			';
		}

		public function cancel()
		{
			return '
				<script>
						function preventBack(){window.history.forward();}
						setTimeout("preventBack()", 0);
						window.onunload=function(){null};
						var App = window.parent.App;
						var currentRouteName = App.__container__.lookup("controller:application").get("currentRouteName");
						var currentRoute = App.__container__.lookup("route:"+currentRouteName);
						currentRoute.send("cancelCheckOut");
				</script>
			';
		}
	}