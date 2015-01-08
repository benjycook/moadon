<?php

class AdminMembersController extends BaseController 
{

	public function create()
	{
		$member = array();
		$member['clubs'] = Club::orderBy('name', 'ASC')->get(array('name','id'))->toArray();
		return Response::json($member,200);
	}
	protected function memberValidation($data,$member)
	{
		if(empty($data))
    		return false;
    	$keys 	= $member->getFillable();
		$rules 	= array();
		$nonrequired = array();
		foreach ($keys as $key => $value) 
		{
			if(array_search("$value",$nonrequired,true)===false)
				$rules["$value"] ='required';
			if($value == "idNumber")
				$rules["$value"] = $rules["$value"]."|id_check";
			if($value == "email")
				$rules["$value"] = $rules["$value"]."|email";
		}
		$validator = Validator::make($data, $rules);
		if($validator->fails())
			return false;
		if(Club::where('id','=',$data['clubs_id'])->count())
			return true;
		return false;
	}
	public function index()
	{
		$items = Input::get('items',10);
		$page  = Input::get('page',1);
		$query = Input::get('query',0);
		$sql = $query ? "name LIKE CONCAT('%',?,'%')" :'? = 0';
		$count = Member::whereRaw($sql,array($query))->count();
		$pages = ceil($count/$items);
		$member = Member::with('club')->whereRaw($sql,array($query))->skip($page*$items-$items)->take($items)->get();
		$member = $member->toArray();
		$meta = array(
			'pages' => $pages,
			'count' => $count,
			'page'	=> $page
			);
		$data = array('collection'=>$member,'meta'=>$meta);
		return Response::json($data,200);
	}

	public function store()
	{
		$json   = Request::getContent();
    	$data   = json_decode($json,true);
    	$member = new Member;
    	$res 	= $this->memberValidation($data,$member);
    	if($res==false)
    		return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים הדרושים"),501);
    	$data['password'] = Hash::make($data['password']); 
    	$member->create($data);
    	return Response::json(array('sucsess'=>"הלקוח נוצר בהצלחה"),201);
	}

	public function show($id)
	{
		$member = Member::find($id);
		if(!$member)
			return Response::json(array('error'=>'לקוח זה לא נמצא במערכת'),501);
		$member = $member->toArray();
		$member['clubs'] = Club::orderBy('name', 'ASC')->get(array('name','id'))->toArray();
		return Response::json($member,200);
	}

	public function update($id)
	{
		$json=Request::getContent();
	    $data=json_decode($json,true);
		$member = member::find($id);
		if(!$member)
			return Response::json(array('error'=>'לקוח זה לא נמצא במערכת'),501);
		$res 	= $this->memberValidation($data,$member);
		if($res===false)
    		return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים הדרושים"),501);
    	if(isset($data['password']))
    		unset($data['password']);
    	$member->fill($data);
    	$member->save();
    	return Response::json(array('sucsess'=>"פרטי הלקוח עודכנו בהצלחה"),201);
	}

}