<?php

class AdminUsersController extends BaseController 
{
	protected function rules()
	{
		$rules = array(
			"username" 		=> "required",
			"email" 		=> "required|email",
			"firstName" 	=> "required",
			"lastName" 		=> "required",
			"phone" 		=> "required",
			"password"		=> "required",
			'states_id'		=> "required",
			);
		return $rules;
	}
	public function index()
	{
		$items = Input::get('items',10);
		$page  = Input::get('page',1);
		$query = Input::get('query',0);
		$sql = $query ? "CONCAT(firstName,' ',lastName) LIKE CONCAT('%',?,'%')" :'? = 0';
		$count = User::whereRaw($sql,array($query))->count();
		$pages = ceil($count/$items);
		$user = User::whereRaw($sql,array($query))->skip($page*$items-$items)->take($items)->get();
		$user = $user->toArray();
		$meta = array(
			'pages' => $pages,
			'count' => $count,
			'page'	=> $page
			);
		$data = array('collection'=>$user,'meta'=>$meta);
		return Response::json($data,200);
	}

	public function store()
	{
		$json   = Request::getContent();
    	$data   = json_decode($json,true);
    	$user 	= new User;
		$validator = Validator::make($data,$this->rules());
		if($validator->fails()) 
			return Response::json(array('error'=>"אנא וודא שסיפקת את כל הנתונים הדרושים"),501);
    	if(User::whereRaw('username = ?',array($data['username']))->count())
    		return Response::json(array('error'=>"שם משתמש זה קיים במערכת אנא בחר אחר"),501);
    	if(User::whereRaw('email = ?',array($data['email']))->count())
    		return Response::json(array('error'=>'דוא"ל זה קיים במערכת אנא בחר אחר'),501);
    	if(!State::whereRaw('id = ?',array($data['states_id']))->count())
    		return Response::json(array('error'=>'סטאטוס זה לא קיים במערכת אנא בחר אחר'),501);
    	$user->create($data);
    	return Response::json(array('sucsess'=>"המשתמש נוצר בהצלחה"),201);
	}

	public function show($id)
	{
		$user = User::find($id);
		if(!$user)
			return Response::json(array('error'=>'משתמש זה לא נמצא במערכת'),501);
		return Response::json($user->toArray(),200);
	}

	public function update($id)
	{
		$json=Request::getContent();
	    $data=json_decode($json,true);
		$user = User::find($id);
		if(!$user)
			return Response::json(array('error'=>'משתמש זה לא נמצא במערכת'),501);
		$validator = Validator::make($data,$this->rules());
		if($validator->fails()) 
			return Response::json(array('error'=>"אנא וודא שסיפקת את כל הנתונים הדרושים"),501);
    	if(User::whereRaw('username = ? AND id != ?',array($data['username'],$user->id))->count())
    		return Response::json(array('error'=>"שם משתמש זה קיים במערכת אנא בחר אחר"),501);
    	if(User::whereRaw('email = ? AND id != ?',array($data['email'],$user->id))->count())
    		return Response::json(array('error'=>'דוא"ל זה קיים במערכת אנא בחר אחר'),501);
    	if(!State::whereRaw('id = ?',array($data['states_id']))->count())
    		return Response::json(array('error'=>'סטאטוס זה לא קיים במערכת אנא בחר אחר'),501);
    	$user->fill($data);
    	$user->save();
    	return Response::json(array('sucsess'=>"המשתמש עודכן בהצלחה"),201);
	}

}