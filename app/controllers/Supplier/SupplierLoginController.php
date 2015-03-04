<?php
class SupplierLoginController extends BaseController {
	
    public function restore()
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
            $result = Client::whereRaw('email = ?',array($data['email']))->first();
            if(!$result)
                return Response::json(array('error'=>'דוא"ל זה לא נמצא במערכת'), 401);
            elseif($result->states_id == 1)
                    return Response::json(array('error'=>'המשתמש אינו פעיל אנא פנה למנהל מערכת'), 403);
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
    public function login()
    {
        $data = json_decode(Request::getContent(),true);
        $rules = array( 
            'username'  => 'required',
            'password'  => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) 
        {
            return Response::json(array('error'=>$validator->messages()->first()), 501);
        }
        else 
        {
            Config::set('auth.model','Supplier');
            $data = array('username'=>$data['username'],'password'=>$data['password'],'states_id'=>2);
            if($test = Auth::attempt($data,true))
                return Response::json(array("logged"=>true), 200);
            return Response::json(array('error'=>'שם משתמש או סיסמא אינם נכונים'), 401);
        }
    }
    public function logout()
    {
        Auth::logout();
    }
}