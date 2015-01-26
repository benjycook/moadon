<?php

class AdminCitiesController extends BaseController 
{
	protected function validateRegions($data)
	{
		if(!isset($data['regions'])||!count($data['regions']))
			return array('error'=>'יש לבחור לפחות אזור אחת');
		if(Region::whereIn('id',$data['regions'])->count()!=count($data['regions']))
			return array('error'=>'אחד האזורים לא נמצא במערכת');
		return false;
	}

	public function create()
	{
		return Response::json(array(
			'allRegions'=>Region::with('children')->where('parent_id','=',0)->get(),
			'regions'=>array(),
			),201);
	}

	public function index()
	{
		$items = Input::get('items',10);
		$page  = Input::get('page',1);
		$query = Input::get('query',0);
		if($query=="")
			$query = 0;
		$sql = $query ? "name LIKE CONCAT('%',?,'%')" :'? = 0';
		$count = City::whereRaw($sql,array($query))->count();
		$pages = ceil($count/$items);
		$cities = City::with('suppliers')->whereRaw($sql,array($query))->forPage($page,$items)->get();
		foreach ($cities as $city) {
			if(count($city->suppliers))
				$city->removable = false;
			else
				$city->removable = true;
		}
		$meta = array(
			'pages' => $pages,
			'count' => $count,
			'page'	=> $page
			);
		$data = array('collection'=>$cities,'meta'=>$meta);
		return Response::json($data,200);
	}
	public function show($id)
	{
		$city = City::find($id);
		if(!$city)
			return Response::json(array('error'=>'ישוב זה לא נמצא במערכת'),501);
		$city->regions = $city->regions()->lists('regions_id'); 
		$city->allRegions = Region::with('children')->where('parent_id','=',0)->get();
		return Response::json($city,200);
	}
	public function store()
	{
		$json   = Request::getContent();
    	$data   = json_decode($json,true);
    	$city   = new City;
    	$rules = array('name'=>'required'); 
    	$validator = Validator::make($data,$rules);
    	if($validator->fails())
    		return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים הדרושים"),501);
    	if(City::where('name','=',$data['name'])->count())
    		return Response::json(array('error'=>"שם ישוב זה קיים במערכת."),501);
    	$res = $this->validateRegions($data);
    	if(isset($res['error']))
    		return Response::json(array('error'=>$res['error']),501);
    	$city = $city->create($data);
    	$city->regions()->sync($data['regions']);
    	return Response::json($city,201);
	}

	public function update($id)
	{
		$json=Request::getContent();
	    $data=json_decode($json,true);
		$city = City::find($id);
		if(!$city)
			return Response::json(array('error'=>'ישוב זה לא נמצא במערכת'),501);
    	$rules = array('name'=>'required'); 
    	$validator = Validator::make($data,$rules);
    	if($validator->fails())
    		return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים הדרושים"),501);
    	if(City::where('name','=',$data['name'])->where('id','!=',$id)->count())
    		return Response::json(array('error'=>"שם ישוב זה קיים במערכת."),501);
    	$res = $this->validateRegions($data);
    	if(isset($res['error']))
    		return Response::json(array('error'=>$res['error']),501);
    	$city->regions()->sync($data['regions']);
    	$city->fill($data);
    	$city->save();
    	return Response::json($city,201);
	}

	public function destroy($id)
	{
		$city = City::with('suppliers')->with('regions')->find($id);
		if(!$city)
			return Response::json(array('error'=>'ישוב זה לא נמצא במערכת'),501);
		if(count($city->suppliers))
		{
			$names = $city->suppliers->lists('name');
			$names = implode(',',$names);
			return Response::json(array('error'=>'אין באפשרותך למחוק ישוב זה מכיוון שהוא משויך ל: '.$names),501);
		}
		//$regions = $city->regions->lists('regions_id');
		$city->regions()->detach();
		$city->delete();
		return Response::json(array('success'=>'הישוב נמחק בהצלחה'),200);	
	}
}