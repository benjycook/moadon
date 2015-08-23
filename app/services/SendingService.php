<?php 
class SendingService
{
	public static function sendSms($senderNumber,$resiverNumber,$msg,$projectKey,$postUrl)
	{
		$sms = new stdClass;
        $sms->msg = $msg;
        $sms->key = $projectKey;
        $sms->senderNumber  = $senderNumber;
        $sms->resiverNumber = $resiverNumber;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $postUrl);
        curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($sms));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($ch),true);
        curl_close($ch);
	}

	public static function sendEmail($email,$template,$subject,$info)
	{
        if(empty($email))
        {
            $email = $_ENV['DEFUALT_EMAIL'];
        }

        //do not send emails if DEFUATL_EMAIL is null
        if($email != NULL)
        {
            Mail::send($template,$info, function($message) use($subject,$email){       
                $message->to($email)->subject($subject);
            });
        }
	}
}