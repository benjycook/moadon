<?php

	class CreditGuardService {


		public static function find($uniqueid)
		{
			return GatewayLog::where('uniqueid', '=', $uniqueid);
		}

		public static function startTransaction($total,$client,$maxpayments = 1)
		{
			$amount = $total;
			$cgConf['tid'] 						= Config::get('creditguard.tid');
			$cgConf['mid'] 						= Config::get('creditguard.mid');
			$cgConf['user']						= Config::get('creditguard.user');
			$cgConf['password']					= Config::get('creditguard.password');
			$cgConf['cg_gateway_url']			= Config::get('creditguard.url');
			
			$cgConf['amount'] 					= $amount * 100;

			$poststring = 'user='.$cgConf['user'];
			$poststring .= '&password='.$cgConf['password'];

			$successUrl 	= URL::to('payment/success') . '?';
			$errorUrl		= URL::to('payment/error') . '?';
			$cancelUrl		= URL::to('payment/cancel') . '?';
			

			$log = new GatewayLog;
			
			$log->status				= 	0;
			$log->maxpayments			= 	$maxpayments;		
			$log->amount				= 	$amount;
			$log->code					= 	0;
			$log->message				= 	'';		
			$log->info					= 	'';
			$log->url					= 	'';
			$log->tranid				= 	'';
			$log->txid					= 	'';
			$log->clients_id			=   $client->id;
			$log->clubs_id 				=   $client->clubs_id;
			$log->save();
			$log->uniqueid	= $uniqueid = $log->id.strtotime('now');
			$log->save();

			$poststring.='&int_in=<ashrait>
				<request>
						<version>1000</version>
						<language>HEB</language>
						<dateTime></dateTime>
						<command>doDeal</command>
						<doDeal>
							 <successUrl>' . $successUrl . '</successUrl>
							 <errorUrl>' . $errorUrl . '</errorUrl>
							  <cancelUrl>' . $cancelUrl . '</cancelUrl>
							 <terminalNumber>'.$cgConf['tid'].'</terminalNumber>
							 <mainTerminalNumber/>
							 <cardNo>CGMPI</cardNo>
							 <total>'.$cgConf['amount'].'</total>
							 <transactionType>Debit</transactionType>
							 <creditType>Payments</creditType>
							 <currency>ILS</currency>
							 <transactionCode>Phone</transactionCode>
							 <authNumber/>
							 <numberOfPayments>3</numberOfPayments>
							 <firstPayment/>
							 <periodicalPayment/>
							 <validation>TxnSetup</validation>
							 <dealerNumber/>
							 <user>'.$log->id.'</user>
							 <mid>'.$cgConf['mid'].'</mid>
							 <uniqueid>'.$uniqueid.'</uniqueid>
							 <mpiValidation>autoComm</mpiValidation>
							 <email/>
							 <clientIP/>
							 <customerData>
							  <userData1/>
							  <userData2/>
							  <userData3/>
							  <userData4/>
							  <userData5/>
							  <userData6/>
							  <userData7/>
							  <userData8/>
							  <userData9/>
							  <userData10/>
							 </customerData>
						</doDeal>
				</request>
			</ashrait>';
			//init curl connection
			if( function_exists( "curl_init" )) { 
			   $CR = curl_init();
			   curl_setopt($CR, CURLOPT_URL, $cgConf['cg_gateway_url']);
			   curl_setopt($CR, CURLOPT_POST, 1);
			   curl_setopt($CR, CURLOPT_FAILONERROR, true);
			   curl_setopt($CR, CURLOPT_POSTFIELDS, $poststring);
			   curl_setopt($CR, CURLOPT_RETURNTRANSFER, 1);
			   curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);
			   curl_setopt($CR, CURLOPT_FAILONERROR,true);


			   //actual curl execution perfom
			   $result = curl_exec( $CR );
			   $error = curl_error ( $CR );
			   
			   // on error - die with error message
			   if( !empty( $error )) {
				  die($error);
				}
					
			   curl_close( $CR );
			}

			if(strpos(strtoupper($result),'HEB'))
			{ 
				$result = iconv("utf-8", "iso-8859-8", $result); 
			}

			$xmlObj = simplexml_load_string($result);
	

			if(isset($xmlObj->response->doDeal->mpiHostedPageUrl))
			{
				$log->status  = 1;
				$log->url 		= (string)$xmlObj->response->doDeal->mpiHostedPageUrl;
				$log->tranid  = (string)$xmlObj->response->tranId;
				$log->txid 		= (string)$xmlObj->response->doDeal->token;
				$log->save();

				return $log;
			}
			else
			{
				$log->status 	= 0;
				$log->code 		= (string)$xmlObj->response->result;
				$log->message	= (string)$xmlObj->response->message;
				$log->info 		= (string)$xmlObj->response->additionalInfo;
				$log->save();
				return $log;
			}
		}

		protected static function query($token)
		{
			$cgConf['tid'] 						= Config::get('creditguard.tid');
			$cgConf['mid'] 						= Config::get('creditguard.mid');
			$cgConf['user']						= Config::get('creditguard.user');
			$cgConf['password']				= Config::get('creditguard.password');
			$cgConf['cg_gateway_url']	= Config::get('creditguard.url');

			$poststring = '';
			$poststring = 'user='.$cgConf['user'];
			$poststring .= '&password='.$cgConf['password'];

			/*Build Ashrait XML to post*/
			$poststring.='&int_in=<ashrait>
									<request>
									 <language>HEB</language>
									 <command>inquireTransactions</command>
									 <inquireTransactions>
		 							  <terminalNumber>'.$cgConf['tid'].'</terminalNumber>
									  <mainTerminalNumber/>
									  <queryName>mpiTransaction</queryName>
									  <mid>'.$cgConf['mid'].'</mid>
									  <mpiTransactionId>'.$cgConf['txId'].'</mpiTransactionId>
									  <userData1/>
									  <userData2/>
									  <userData3/>
									  <userData4/>
									  <userData5/>
									 </inquireTransactions>
									</request>
								   </ashrait>';

			//init curl connection
			if( function_exists( "curl_init" )) { 
			   $CR = curl_init();
			   curl_setopt($CR, CURLOPT_URL, $cgConf['cg_gateway_url']);
			   curl_setopt($CR, CURLOPT_POST, 1);
			   curl_setopt($CR, CURLOPT_FAILONERROR, true);
			   curl_setopt($CR, CURLOPT_POSTFIELDS, $poststring);
			   curl_setopt($CR, CURLOPT_RETURNTRANSFER, 1);
			   curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);
			   curl_setopt($CR, CURLOPT_FAILONERROR,true);


			   //actual curl execution perfom
			   $result = curl_exec( $CR );
			   $error = curl_error ( $CR );
			   
			   // on error - die with error message
			   if( !empty( $error )) {
				  die($error);
				}
					
			   curl_close( $CR );
			}

			if( function_exists( "simplexml_load_string" )) { 
				if(strpos(strtoupper($result),'HEB')){ $result = iconv("utf-8", "iso-8859-8", $result); }
				$xmlObj = simplexml_load_string($result);
				// Example to print out status text
				echo $xmlObj->response->inquireTransactions->row->statusText;
			} else {
				die("simplexml_load_string function is not support, upgrade PHP version!");
			}
		}
	}

