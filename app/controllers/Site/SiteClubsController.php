<?php

class SiteClubsController extends BaseController 
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

	public function login($slug)
	{
		$json =	Request::getContent();
	  $data	=	json_decode($json);

		$club = Club::where('urlName', '=', $slug)
								->where('clubCode', '=', $data->clubident)
								->first();

		if(!$club)
		{
			return Response::json(array('error' => 'הקוד שהזנת שגוי. נסה שנית.'), 401);
		}

		$session = array(
			'token' => '123',
			'club'	=> $club->id,
			'user'	=> null
		);

		return Response::json($session, 201);
	}

	public function logout($slug)
	{
		return Response::json(array('status' => 'ok'), 200);
	}

	public function options($slug)
	{
		$club = Club::site()->where('urlName','=',$slug)->first();

		if(!$club)
			return Response::json('מועדון זה לא נמצאה במערכת',404);

		$data = array();
		$data['club'] = $club->toArray();
		$data['club']['logo'] = URL::to('/')."/galleries/{$data['club']['logo']}";

		$data['regions'] 		= Region::where('parent_id','=',0)->with('children')->get();
		$data['categories'] = Category::where('parent_id','=',0)->with('children')->get();
		$cities = City::with('regions')->get();

		foreach ($cities as &$city) {
			$city['regions_id'] = $city['regions'][0]['id'];
			unset($city['regions']);
		}

		$data['cities'] = $cities;

		$suppliers = SiteDetails::where('visibleOnSite', '=', '1')
														->where('states_id', '=', '2')
														->has('items', '>=', '1')
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

	public function supplier($slug, $id)
	{
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
		$supplier = SiteDetails::mini();
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

			'data' => $suppliers
		];

		return Response::json($data,200);
	}
}