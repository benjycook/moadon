<?php

class AdminSuppliersController extends BaseController 
{
	protected function rules()
	{
		$rules = array(
			"name" 			=> "required",
			"idNumber" 	=> "required",
			"username" 		=> "required",
			"password"		=> "required",
			'contactFirstName'	=> "required",
			"contactEmail" 		=> "required|email",
			'contactLastName'=> "required",
			'contactPhone'=> "required",
			);
		return $rules;
	}
	protected function validateCaregoriesAndRegions($data)
	{
		if(!count($data['categories']))
			return array('error'=>'יש לבחור לפחות קטגוריה אחת');
		if(!count($data['regions']))
			return array('error'=>'יש לבחור לפחות אזור אחת');
		if(Category::whereIn('id',$data['categories'])->count()!=count($data['categories']))
			return array('error'=>'אחת הקטגוריות לא נמצא במערכת');
		if(Region::whereIn('id',$data['regions'])->count()!=count($data['regions']))
			return array('error'=>'אחת האזורים לא נמצא במערכת');
		return false;
	}
	public function create()
	{
		
		$temp = new stdClass;
		$temp->sitedetails = new stdClass;
		$base = URL::to('/')."/galleries/";
		$temp->sitedetails->linkId = md5(rand(1,10000).'templink'.rand(1,10000));
		$temp->sitedetails->uploadUrl = '/uploadImage';
		$temp->sitedetails->galleries['main'] = array('id'=>0,'type'=>'ראשי','images'=>array(),'base'=>$base);
		$temp->items = array();
		$temp->supplier = new stdClass;
		return Response::json($temp,201);
	}

	public function index()
	{
		$items = Input::get('items',10);
		$page  = Input::get('page',1);
		$query = Input::get('query',0);
		$sql = $query ? "name LIKE CONCAT('%',?,'%')" :'? = 0';
		$count = Supplier::whereRaw($sql,array($query))->count();
		$pages = ceil($count/$items);
		$supplier = Supplier::whereRaw($sql,array($query))->skip($page*$items-$items)->take($items)->get();
		$supplier = $supplier->toArray();
		$meta = array(
			'pages' => $pages,
			'count' => $count,
			'page'	=> $page
			);
		$data = array('collection'=>$supplier,'meta'=>$meta);
		return Response::json($data,200);
	}

	public function store()
	{
		$json   = Request::getContent();
    	$data   = json_decode($json,true);
    	$supplier 	= new Supplier;
    	$validator = Validator::make($data,$this->rules());
    	if($validator->fails())
    		return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים הדרושים"),501);
    	if(Supplier::where('username','=',$data['username'])->count())
    		return Response::json(array('error'=>"שם משתמש זה כבר קיים במערכת אנא בחר אחר"),501);
    	if(Supplier::whereRaw('idNumber = ?',array($data['idNumber']))->count())
    		return Response::json(array('error'=>"ע.מ/ח.פ זה כבר קיים במערכת אנא בחר אחר"),501);
    	$res = $this->validateCaregoriesAndRegions($data);
    	if(isset($res['error']))
    		return Response::json(array('error'=>$res['error']),501);
    	$supplier = $supplier->create($data);
    	$supplier->categories()->attach($data['categories']);
    	$supplier->regions()->attach($data['regions']);
    	$siteDetails = SiteDetails::create(array('suppliers_id'=>$supplier->id));
    	$gallery = Gallery::create(array('type'=>'ראשית'));
		$siteDetails->galleries()->attach($gallery->id);
		$base = URL::to('/')."/galleries/";
		$newSite = new stdClass;
		$newSite->linkId = $newSite->id = $siteDetails->id;
		$newSite->uploadUrl = '/uploadImage';
		$newSite->galleries['main'] = array('id'=>$gallery->id,'type'=>'ראשי','images'=>array(),'base'=>$base);
    	return Response::json(array('supplier'=>$supplier,'siteDetails'=>$newSite),201);
	}

	public function show($id)
	{
		$supplier = Supplier::with('sitedetails')->with('items')->with('regions')->with('categories')->find($id);
		if(!$supplier)
			return Response::json(array('error'=>'ספק זה לא נמצא במערכת'),501);
		$supplier = $supplier->toArray();
		$regions = Collection::make($supplier['regions'])->lists('id');
		$categories = Collection::make($supplier['categories'])->lists('id');
		$supplier['regions'] = $regions;
		$supplier['categories'] = $categories;
		$sitedetails = $supplier['sitedetails'];
		$galleries = $supplier['sitedetails']['galleries'];
		$items = $supplier['items'];
		unset($supplier['sitedetails']);
		unset($supplier['items']);
		$supplier    = $supplier;
		$sitedetails['linkId'] = $sitedetails['id'];
		$temp = array();
		$temp['main'] = isset($galleries[0]) ? $galleries[0]:array('images'=>array());
		$temp['main']['base'] = URL::to('/')."/galleries/";
		$sitedetails['galleries'] = $temp;
		$sitedetails['uploadUrl'] = '/uploadImage';
		foreach ($items as &$item) {
			$galleries = $item['galleries'];
			$item['linkId'] = $item['id'];
			$temp = array();
			$temp['main'] = isset($galleries[0]) ? $galleries[0]:array('images'=>array());
			$temp['main']['base'] = URL::to('/')."/galleries/";
			$item['galleries'] = $temp;
			$item['uploadUrl'] = '/uploadImage';
			$item['expirationDate'] = implode('/',array_reverse(explode('-',$item['expirationDate'])));	
		}
		return Response::json(array('supplier'=>$supplier,'items'=>$items,'sitedetails'=>$sitedetails),200);
	}
	public function update($id)
	{
		$json=Request::getContent();
	    $data=json_decode($json,true);
		$supplier = Supplier::find($id);
		if(!$supplier)
			return Response::json(array('error'=>'ספק זה לא נמצא במערכת'),501);
    	$validator = Validator::make($data,$this->rules());
    	if($validator->fails())
    		return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים הדרושים"),501);
    	if(Supplier::whereRaw('username = ? AND id != ?',array($data['username'],$supplier->id))->count())
    		return Response::json(array('error'=>"שם משתמש זה כבר קיים במערכת אנא בחר אחר"),501);
    	if(Supplier::whereRaw('idNumber = ? AND id != ?',array($data['idNumber'],$supplier->id))->count())
    		return Response::json(array('error'=>"ע.מ/ח.פ זה כבר קיים במערכת אנא בחר אחר"),501);
    	$res = $this->validateCaregoriesAndRegions($data);
    	if(isset($res['error']))
    		return Response::json(array('error'=>$res['error']),501);
    	$supplier->categories()->sync($data['categories']);
    	$supplier->regions()->sync($data['regions']);
    	$supplier->fill($data);
    	$supplier->save();
    	return Response::json(array('supplier'=>$supplier),201);
	}

}