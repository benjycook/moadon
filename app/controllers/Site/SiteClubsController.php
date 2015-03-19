<?php

class SiteClubsController extends SiteBaseController 
{

	protected function flaten($array)
	{
		$ids = array();
		$res  = array();
		foreach ($array as $key => $value) {
			$ids[] = $value['id'];
			$res = array_merge($res, $this->flaten($value['children']));
		}

		return array_merge($res, $ids);
	}

	protected function setUp($subjects,$regions)
	{
		foreach ($subjects as $subject) {
			$subject['region'] = $subject['regions_id'] ? $regions[$subject['regions_id']]['name']:"";
			unset($subject['regions_id']);
			if(count($subject['galleries']))
			{
				$gallery = $subject['galleries'][0];
				$subject['pic'] = count($gallery['images']) ?$gallery['images'][0]['src']:"";
				$subject['pic'] = URL::to('/')."/".$subject['pic'];
			}
			else
				$subject['pic'] = "";
			unset($subject['galleries']);	
		}
		return $subjects;
	}

	public function login()
	{
		$json =	Request::getContent();
	  $data	=	json_decode($json);

	  //print_r($this->club->clubCode);die($data->clubident);
						
		if($data->clubident != $this->club->clubCode)
			return Response::json(array('error' => 'הקוד שהזנת שגוי. נסה שנית.'), 401);
		
		$cart = Cart::create(array());

		$claims = array(
			'club_id'		=> $this->club->id,
			'user'				=> null,
			'cart_id' 		=> $cart->id,
			'loginType' 	=> 'club'
		);

		$token = TokenAuth::make('club', $claims);

		return Response::json(compact('token', 'claims'), 200);
	}


	public function options()
	{
		// ///star token
		// $header =	Request::header('authorization', null);
		// if($header)
		// {
		// 	list($nop, $token) = explode('Bearer ', $header);
		// }
	
		// $parts = explode('.', $token);
		// if(count($parts) != 3)
		// 	return Response::json(['error' => 'invalid token parts'], 401);
		
		// //verify token
		// $data = $parts[0] . $parts[1];
		// if(md5($data) != base64_decode($parts[2]))
		// 	return Response::json(['error' => 'invalid token signature'], 401);

		// $payload = json_decode(base64_decode($parts[1]));


		// $club = Club::site()->where('urlName','=',$slug)->first();

		// if(!$club)
		// 	return Response::json('מועדון זה לא נמצאה במערכת',404);


		$data = array();
	
		$data['club'] = $this->club->toArray();
		$data['club']['logo'] = URL::to('/')."/galleries/{$data['club']['logo']}";

		$data['regions'] 		= Region::where('parent_id','=',0)->with('children')->get();
		$data['categories'] = Category::where('parent_id','=',0)->with('children')->get();
		$cities = City::with('regions')->get();

		foreach ($cities as &$city) {
			$city['regions_id'] = $city['regions'][0]['id'];
			unset($city['regions']);
		}

		$data['cities'] = $cities;

		$suppliers = SiteDetails::join('items', 'sitedetails.suppliers_id', '=', 'items.suppliers_id')
														->where('visibleOnSite', '=', '1')
														->where('sitedetails.states_id', '=', '2')
														->whereRaw('100 - FLOOR(items.priceSingle / items.listPrice * 100) > 1')
														->select(DB::raw(
															'sitedetails.*, MAX(100 - FLOOR(items.priceSingle / items.listPrice * 100)) AS discount'
														))
														->orderBy(DB::raw('MAX(100 - FLOOR(items.priceSingle / items.listPrice * 100))'), 'DESC')
														->groupBy('sitedetails.suppliers_id')
														->with('galleries')
														->get()->toArray();
		
		foreach ($suppliers as &$supplier) {
			$rawImages = array();
			$images = $supplier['galleries'][0]['images'];
			foreach ($images as $image) {
				$rawImages[] = URL::to('/')."/galleries/{$image['src']}";
			}
			unset($supplier['galleries']);
			$supplier['images'] = $rawImages;
		}
		$data['suppliers'] = $suppliers;
		// $data['mostViewed'] = SiteDetails::site()->whereHas('supplier',function($q){
		// 	$q->where('views','>',0);
		// 	$q->orderBy('views','DESC');

		// })->take(10)->get();

		// $ids = Collection::make($data['mostViewed'])->lists('id');
		// if(!count($ids))
		// 	array_push($ids,0);

		// $data['newSuppliers'] = SiteDetails::site()->whereNotIn('id',$ids)->whereHas('supplier',function($q){
		// 	$q->orderBy('created_at','DESC');
		// })->take(10)->get();

		//$ids = array_merge($ids,Collection::make($data['newSuppliers'])->lists('id'));
		
		//$suppliers = SiteDetails::site()->whereNotIn('id',$ids)->get();
		
		//$data['suppliers'] = $this->setUp($suppliers,$data['regions']);
		
		//$data['mostViewed'] = $this->setUp($data['mostViewed'],$data['regions']);
		
		//$data['newSuppliers'] = $this->setUp($data['newSuppliers'],$data['regions']);

		return Response::json($data,200);
	}

	protected function gallerySetUp($gallery)
	{
		unset($gallery['id']);
		unset($gallery['pivot']);
		foreach ($gallery['images'] as &$image) {
			unset($image['id']);
			unset($image['galleries_id']);
			$image['src'] = URL::to('/')."/galleries/".$image['src'];
		}
		return $gallery;
	}

	public function supplier($id)
	{
		// $supplier = SiteDetails::whereHas('supplier',function($q) use($id){
		// 	$q->where('id','=',$id);
		// })->first();
		
		$supplier = SiteDetails::whereHas('supplier',function($q) use($id){
			$q->where('id','=',$id);
		})->mini()->first();
		
		if(!$supplier)
			return Response::json('ספק זה לא נמצאה במערכת',404);
		$regions = Region::with('children')->get();
		$supplier = $supplier->toArray();
		//$supplier['region'] = $supplier['regions_id'] ? $regions[$supplier['regions_id']]['name']:"";
	
		$rawImages = array();
		$images = $supplier['galleries'][0]['images'];
		foreach ($images as $image) {
			$rawImages[] = URL::to('/')."/galleries/{$image['src']}";
		}
		unset($supplier['galleries']);
		$supplier['images'] = $rawImages;
		
		foreach ($supplier['items'] as $key => &$item) {
			$rawImages = array();

			$images = $supplier['items'][$key]['galleries'][0]['images'];
			
			foreach ($images as $image) 
			{
				$rawImages[] = URL::to('/')."/galleries/{$image['src']}";
			}

			if(!empty($item['priceSingle']))
			{	
				$item['discount'] = 100 - floor(100 / $item['listPrice'] * $item['priceSingle']);
			}
			else
			{
				$item['discount'] = 100;
				$item['priceSingle'] = 0;
			}
			unset($supplier['items'][$key]['galleries']);
			$supplier['items'][$key]['images'] = $rawImages;
		}

		unset($supplier['suppliers_id']);
		return Response::json($supplier,200);
	}

	public function search()
	{
		//sleep(3);
		$region = Input::get('region', 0);
		$category = Input::get('category', 0);
		$subregions = Input::get('subregions',0);
		$subcategories = Input::get('subcategories',0);
		$items = Input::get('items',9);
		$page  = Input::get('page',1);
		$name = Input::get('supplier',0);
		$items = Input::get('items', 9);
		$page = Input::get('page', 1);
		//$item = Input::get('item',0);
		$supplier  = SiteDetails::join('items', 'sitedetails.suppliers_id', '=', 'items.suppliers_id')
														->where('visibleOnSite', '=', '1')
														->where('sitedetails.states_id', '=', '2')
														->whereRaw('100 - FLOOR(items.priceSingle / items.listPrice * 100) > 1')
														->select(DB::raw(
															'sitedetails.*, MAX(100 - FLOOR(items.priceSingle / items.listPrice * 100)) AS discount'
														))
														->orderBy(DB::raw('MAX(100 - FLOOR(items.priceSingle / items.listPrice * 100))'), 'DESC')
														->groupBy('sitedetails.suppliers_id')
														->with('galleries');
														//->get()->toArray();
		if($category)
		{
			if($subcategories)
			{
				$subcategories = explode(',', $subcategories);
				$temp = Category::whereIn('id', $subcategories)->with('children')->get();
			}
			else
			{
				$temp = Category::where('id', '=', $category)->with('children')->get();
			}

			$categories = $this->flaten($temp);

			$supplier->whereHas('categories', function($q) use($categories){
				$q->whereIn('categories_id', $categories);
			});
		}

		if($region > 0)
		{
			if($subregions)
			{
				$subregions = explode(',', $subregions);
				$temp = Region::whereIn('id', $subregions)->with('children')->get();
			}
			else{
				$temp = Region::where('id', '=', $region)->with('children')->get();
			}

			//get all regions
			$regions = $this->flaten($temp);

			//get all cities for region
			$cities = City::whereHas('regions', function($q) use ($regions){
				$q->whereIn('regions_id', $regions);
			})->lists('id');

			$cities[] = -1;

			$supplier->whereIn('cities_id', $cities);
		}

		if($name)
		{
			if($name=="")
				$name = 0;
			$sql = $name ? "supplierName LIKE CONCAT('%',?,'%')" :'? = 0';
			$supplier->whereRaw($sql,array($name));
		}

		$count = $supplier->count();

		$suppliers = $supplier->forPage($page, $items)->get();
		
		foreach ($suppliers as &$supplier) {
			$rawImages = array();
			$images = $supplier['galleries'][0]['images'];
			foreach ($images as $image) {
				$rawImages[] = URL::to('/')."/galleries/{$image['src']}";
			}
			unset($supplier['galleries']);
			$supplier['images'] = $rawImages;
		}

		$data = [
			'meta' => [
				'pages' => ceil($count / $items)
			],

			'data' => $suppliers,

			'query' => DB::getQueryLog()
		];

		return Response::json($data,200);
	}
}