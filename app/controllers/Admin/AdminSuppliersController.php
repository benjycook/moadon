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
			);
		return $rules;
	}
	
	public function create()
	{
		
		$temp = new stdClass;
		$temp->sitedetails = new stdClass;
		$base = URL::to('/')."/galleries/";
		$temp->sitedetails->linkId = md5(rand(1,10000).'templink'.rand(1,10000));
		$temp->sitedetails->uploadUrl = '/uploadImage';
		$temp->sitedetails->galleries['main'] = array('id'=>0,'type'=>'ראשי','images'=>array(),'base'=>$base);
		$temp->sitedetails->categories = array();
		$temp->items = array();
		$temp->supplier = new stdClass;
		$temp->categories = Category::with('children')->where('parent_id','=',0)->get();
		$temp->contacts = [];
		$temp->contacts[] = array('firsName'=>"",'lastName'=>"",'email'=>"",'mobile'=>"",'removable'=>false);
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
		$suppliers = Supplier::whereRaw($sql,array($query))->forPage($page,$items)->get();
		foreach ($suppliers as $supplier) {
			if($supplier->orders()->count())
				$supplier->removable = false;
			else
				$supplier->removable = true;
			$supplier->contact = $supplier->contacts()->first();
		}
		$suppliers = $suppliers->toArray();
		$meta = array(
			'pages' => $pages,
			'count' => $count,
			'page'	=> $page
			);
		$data = array('collection'=>$suppliers,'meta'=>$meta);
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
    	if(!isset($data['contacts'])||!count($data['contacts']))
    		return Response::json(array('error'=>"יש לציין לפחות איש קשר אחד."),501);
    	if(Supplier::where('username','=',$data['username'])->count())
    		return Response::json(array('error'=>"שם משתמש זה כבר קיים במערכת אנא בחר אחר"),501);
    	// if(Supplier::whereRaw('idNumber = ?',array($data['idNumber']))->count())
    	// 	return Response::json(array('error'=>"ע.מ/ח.פ זה כבר קיים במערכת אנא בחר אחר"),501);

    	$supplier = $supplier->create($data);
    	foreach ($data['contacts'] as $contact) {
    		$contact['suppliers_id'] = $supplier->id;
    		SupplierContact::create($contact);
    	}
    	$siteDetails = SiteDetails::create(array('suppliers_id'=>$supplier->id,'states_id'=>2));
    	$gallery = Gallery::create(array('type'=>'ראשית'));
		$siteDetails->galleries()->attach($gallery->id);
		$base = URL::to('/')."/galleries/";
		$newSite = new stdClass;
		$newSite->linkId = $newSite->id = $siteDetails->id;
		$newSite->uploadUrl = '/uploadImage';
		$newSite->states_id = 2;
		$newSite->galleries['main'] = array('id'=>$gallery->id,'type'=>'ראשי','images'=>array(),'base'=>$base);
		$newSite->categories = array();
    	return Response::json(array('supplier'=>$supplier,'siteDetails'=>$newSite,'contacts'=>$supplier->contacts()->get()),201);
	}

	public function show($id)
	{
		$supplier = Supplier::with('sitedetails','items.orders','categories','contacts')->find($id);
		if(!$supplier)
			return Response::json(array('error'=>'ספק זה לא נמצא במערכת'),501);
		$supplier = $supplier->toArray();
		$categories = Collection::make($supplier['categories'])->lists('id');
		$contacts = $supplier['contacts'];
		$sitedetails = $supplier['sitedetails'];
		$sitedetails['categories'] = $categories;
		$galleries = $supplier['sitedetails']['galleries'];
		$items = $supplier['items'];
		unset($supplier['categories']);
		unset($supplier['sitedetails']);
		unset($supplier['items']);
		$sitedetails['linkId'] = $supplier['id'];
		$temp = array();
		$temp['main'] = isset($galleries[0]) ? $galleries[0]:array('images'=>array());
		$temp['main']['base'] = URL::to('/')."/galleries/";
		foreach ($temp['main']['images'] as &$image) {
			$image['pos'] = intval($image['pos']);
		}
		$sitedetails['galleries'] = $temp;
		$sitedetails['uploadUrl'] = '/uploadImage';
		foreach ($items as &$item) {
			if(count($item['orders']))
				$item['removable'] = false;
			else
				$item['removable'] = true;
			$galleries = $item['galleries'];
			$item['linkId'] = $item['id'];
			$temp = array();
			$temp['main'] = isset($galleries[0]) ? $galleries[0]:array('images'=>array());
			$temp['main']['base'] = URL::to('/')."/galleries/";
			$item['galleries'] = $temp;
			$item['uploadUrl'] = '/uploadImage';
			$item['expirationDate'] = implode('/',array_reverse(explode('-',$item['expirationDate'])));	
		}
		if(count($contacts)>1)
		{
			foreach ($contacts as $contact) {
				$contact['removable'] = true;
			}
		}
		else
			$contacts[0]['removable'] = false;
		//refactor code
		$data = array(
			'categories'			=> 	Category::with('children')->where('parent_id','=',0)->get(),
			'supplier'				=>	$supplier,
			'items'					=>	$items,
			'sitedetails'			=>	$sitedetails,
			'contacts'				=>  $contacts,
		);

		return Response::json($data,200);
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
    	if(!isset($data['contacts'])||!count($data['contacts']))
    		return Response::json(array('error'=>"יש לציין לפחות איש קשר אחד."),501);
    	if(Supplier::whereRaw('username = ? AND id != ?',array($data['username'],$supplier->id))->count())
    		return Response::json(array('error'=>"שם משתמש זה כבר קיים במערכת אנא בחר אחר"),501);
    	$ids = array(-1);
    	foreach ($data['contacts'] as $contact) {
    		
    		if(isset($contact['id'])&&$contact['id']&&$con = SupplierContact::where('id','=',$contact['id'])->where('suppliers_id','=',$id)->first())
    		{
    			
    			unset($contact['suppliers_id']);
    			$con->fill($contact);
    			$con->save();
    		}
    		else
    		{	
    			
    			$contact['suppliers_id'] = $id;
    			$con = SupplierContact::create($contact);
    		}
    		$ids[] = $con->id;
    	}
    	SupplierContact::where('suppliers_id','=',$supplier->id)->whereNotIn('id',$ids)->delete();
    	// if(Supplier::whereRaw('idNumber = ? AND id != ?',array($data['idNumber'],$supplier->id))->count())
    	// 	return Response::json(array('error'=>"ע.מ/ח.פ זה כבר קיים במערכת אנא בחר אחר"),501);
    	// $res = $this->validateCaregoriesAndRegions($data);
    	// if(isset($res['error']))
    	// 	return Response::json(array('error'=>$res['error']),501);
    	// $supplier->categories()->sync($data['categories']);
    	// $supplier->regions()->sync($data['regions']);
    	$supplier->fill($data);
    	$supplier->save();
    	return Response::json(array('supplier'=>$supplier,'contacts'=>$supplier->contacts()->get()),201);
	}

	public function destroy($id)
	{
		$supplier = Supplier::with('items','sitedetails.galleries')->find($id);
		if(!$supplier)
			return Response::json(array('error'=>'ספק זה לא נמצא במערכת.'),501);
		if($supplier->orders()->count())
			return Response::json(array('error'=>'לא ניתן למחוק ספק אשר בוצעו אליו הזמנות.'),501);
		
		$supplier->contacts()->delete();
		DB::table('categories_suppliers')->where('suppliers_id','=',$id)->delete();
		DB::table('galleries_sitedetails')->where('sitedetails_id','=',$supplier->sitedetails->id)->delete();
		foreach ($supplier->sitedetails->galleries as $gallery) {
			foreach ($gallery->images as $image) {
				if(File::exists(public_path()."/galleries/".$image->src))
					File::delete(public_path()."/galleries/".$image->src);
				$image->delete();
			}
			$gallery->delete();
		}
		$supplier->sitedetails()->delete();
		
		foreach ($supplier->items as $item) {
			ItemGallery::where('items_id','=',$item->id)->delete();
			foreach ($item->galleries as $gallery) {
				foreach ($gallery->images as $image) {
					if(File::exists(public_path()."/galleries/".$image->src))
						File::delete(public_path()."/galleries/".$image->src);
					$image->delete();
				}
				$gallery->delete();
			}
			$item->delete();
		}
		$supplier->delete();
		return Response::json('הספק נמחק בהצלחה.',200);
	}
}