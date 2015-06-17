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
		Mail::send($template,$info, function($message) use($subject,$email)
        {       
            $message->to($email)->subject($subject);
        });
	}
}