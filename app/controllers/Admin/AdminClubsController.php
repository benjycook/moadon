<?php

class AdminClubsController extends BaseController 
{
	protected function clubValidation($data,$club)
	{
		if(empty($data))
    		return false;
    	$keys 	= $club->getFillable();
		$rules 	= array();
		$nonrequired = array('logo');
		foreach ($keys as $key => $value) 
		{
			if(array_search("$value",$nonrequired,true)===false)
				$rules["$value"] ='required';
			if($value=="urlName")
				$rules["$value"] = $rules["$value"].'|alphanum';

		}
		$validator = Validator::make($data, $rules);
		if($validator->fails()) 
			return false;
		return true;
	}
	public function index()
	{
		$items = Input::get('items',10);
		$page  = Input::get('page',1);
		$query = Input::get('query',0);
		$sql = $query ? "name LIKE CONCAT('%',?,'%')" :'? = 0';
		$count = Club::whereRaw($sql,array($query))->count();
		$pages = ceil($count/$items);
		$club = Club::whereRaw($sql,array($query))->skip($page*$items-$items)->take($items)->get();
		$club = $club->toArray();
		$meta = array(
			'pages' => $pages,
			'count' => $count,
			'page'	=> $page
			);
		$data = array('collection'=>$club,'meta'=>$meta);
		return Response::json($data,200);
	}

	public function store()
	{
		$json   = Request::getContent();
    	$data   = json_decode($json,true);
    	$club 	= new Club;
    	$res 	= $this->clubValidation($data,$club);
    	if($res==false)
    		return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים הדרושים"),501);
    	if(!IdentificationType::where('id','=',$data['identificationtypes_id'])->count())
			return Response::json(array('error'=>'צורת הזדהות לא נמצא במערכת'),501);
		if(Club::where('name','=',$data['name'])->count())
			return Response::json(array('error'=>'שם מועדון זה כבר נמצא במערכת'),501);
		if(Club::where('urlName','=',$data['urlName'])->count())
			return Response::json(array('error'=>'תת דומיין זה קיים במערכת'),501);
		 
    	$logo = $data['logo'];
    	unset($data['logo']);
    	$path = public_path()."/galleries/tempimages/";
    	$club = $club->create($data);
    	if($logo!=""&&File::exists($path."".$logo))
    	{
    		$new = "logo".str_replace($data['linkId'],$club->id,$logo);
    		File::move($path."".$logo,$path."../".$new);
    		$club->logo = $new;
    		$club->save();
    	}
    	return Response::json($club,201);
	}

	public function show($id)
	{
		$club = Club::find($id);
		if(!$club)
			return Response::json(array('error'=>'מועדון זה לא נמצא במערכת'),501);
		$club->linkId  = "logo".$club->id;
		$club->logoUrl = URL::to('/')."/galleries/".$club->logo;
		return Response::json($club->toArray(),200);
	}

	public function update($id)
	{
		$json=Request::getContent();
	    $data=json_decode($json,true);
		$club = Club::find($id);
		if(!$club)
			return Response::json(array('error'=>'מועדון זה לא נמצא במערכת'),501);
		$res 	= $this->clubValidation($data,$club);
		if($res==false)
    		return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים הדרושים"),501);
    	if(!IdentificationType::where('id','=',$data['identificationtypes_id'])->count())
			return Response::json(array('error'=>'צורת הזדהות לא נמצא במערכת'),501);
		if(Club::where('id','!=',$data['id'])->where('name','=',$data['name'])->count())
			return Response::json(array('error'=>'שם מועדון זה כבר נמצא במערכת'),501);
		if(Club::where('id','!=',$data['id'])->where('urlName','=',$data['urlName'])->count())
			return Response::json(array('error'=>'תת דומיין זה קיים במערכת'),501);
    	$path = public_path()."/galleries/tempimages/";
    	if($data['logo']!=""&&File::exists($path."".$data['logo']))
    	{
    		File::move($path."".$data['logo'],$path."../".$data['logo']);
    		$data['logo'] = $data['logo'];
    	}
    	$club->fill($data);
    	$club->save();
    	return Response::json($club,201);
	}

}