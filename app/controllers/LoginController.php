<?php
class LoginController extends BaseController {
	
    public function restorePassword()
    {
        $data = json_decode(Request::getContent(),true);
        $rules = array( 
            'email'    => 'required|email'
        );

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) 
        {
            return Response::json(array('error'=>$validator->messages()->first()), 501);
        }
        else 
        {
            $result = Member::whereRaw('email = ?',array($data['email']))->first();
            if(!$result)
            {
                return Response::json(array('error'=>'דוא"ל זה לא נמצא במערכת'), 401);
            }
            else
            {
                Mail::send('mail.restore', array(
                 'password'  =>  $result->password,
                 ), function($message) use($result) 
                {
                    $message->to($result->email,$result->name)->subject('שחזור סיסמא');
                });
                return Response::json('הסיסמא נשלחה לדוא"ל שלך', 200);
            }
        }
    }
    public function dologin()
    {
        $data = json_decode(Request::getContent(),true);
        $rules = array( 
            'clubs_id'  => 'required',
            'password'  => 'required'
        );
        $validator = Validator::make($data, $rules);
        if($validator->fails()) 
            return Response::json(array('error'=>"אנא וודא שסיפקת את כל הנתונים"),501);
        else 
        {
            $attempt = array('email'=>$data['email'],'password'=>$data['password']);
            $club = Club::find($clubs_id);
            if($club->identificationtypes_id==1)
            {

            }
            else
            {
                Config::set('auth.model', 'Client');
                if(Auth::attempt($attempt,true))
                    return Response::json(array('error'=>"שם משתמש או סיסמא אינם נכונים"),501);
            }
            
        }
    }
    public function logout()
    {
        Auth::logout();
    }
}