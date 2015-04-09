<?php

class SiteClubsController extends SiteBaseController 
{

	protected function mainCategory($suppliers)
	{
		foreach ($suppliers as &$supplier) 
		{
			$categories = Category::join('categories_suppliers','categories_suppliers.categories_id','=','categories.id')
										->whereRaw('categories_suppliers.suppliers_id = ?',[$supplier['id']])->get();
			foreach ($categories as $category) {
				if($category->parent_id == 0||Category::where('parent_id','=',0)->where('id','=',$category->categories_id)->count())
				{
					$supplier['mainCategory'] = $category->categories_id;
					break;
				}
			}
		}
		return $suppliers;
	}
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

	public function images($suppliers)
	{
		foreach ($suppliers as &$supplier) {
			$rawImages = array();
			$images = $supplier['galleries'][0]['images'];
			foreach ($images as $image) {
				$rawImages[] = URL::to('/')."/galleries/{$image['src']}";
			}
			unset($supplier['galleries']);
			$supplier['images'] = $rawImages;
		}

		return $suppliers;
	}


	public function options()
	{

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
		
		return Response::json($data,200);
	}
	public function suppliers()
	{

		$data = array();
		$suppliers = SiteDetails::homePage('visibleOnSite')->get()->toArray();
		$data['suppliers'] = $this->images($suppliers);
		$data['suppliers'] = $this->mainCategory($data['suppliers']);

		$newsuppliers = SiteDetails::homePage('newBusiness', true)->forPage(1, 1)->get()->toArray();
		$data['newsuppliers'] = $this->images($newsuppliers);
		
		$mostviewed = SiteDetails::homePage('mostViewed', true)->forPage(1, 1)->get()->toArray();
		$data['mostviewed'] = $this->images($mostviewed);
		
		$hotdeals = SiteDetails::homePage('hotDeal', true)->forPage(1, 1)->get()->toArray();
		$data['hotdeal'] = $this->images($hotdeals);
		try
		{
			$token = TokenAuth::getToken();
			if($token)
			{
				$token = $token->get();
				$payload = TokenAuth::getPayload($token);
				$payloadArray = $payload->toArray();
				$data['cart'] =  $this->_getCart($payloadArray['cart_id']);
			}
		}
		catch(Exception $e)
		{}
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

	public function newsuppliers()
	{
		$newsuppliers = SiteDetails::homePage('newBusiness')->get()->toArray();
		$data['newsuppliers'] = $this->images($newsuppliers);		
	
		return Response::json($data, 200);
	}

	public function mostviewed()
	{
		$mostviewed = SiteDetails::homePage('mostViewed')->get()->toArray();
		$data['mostviewed'] = $this->images($mostviewed);		
	
		return Response::json($data, 200);
	}

	public function hotdeals()
	{
		$hotdeals = SiteDetails::homePage('hotDeal')->get()->toArray();
		$data['hotdeals'] = $this->images($hotdeals);		
	
		return Response::json($data, 200);
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
		$query = Input::get('q', 0);
		//$item = Input::get('item',0);
		$supplier  = SiteDetails::join('items', 'sitedetails.suppliers_id', '=', 'items.suppliers_id')
														->where('sitedetails.states_id', '=', '2')
														//->whereRaw('(100 - FLOOR(items.priceSingle / items.listPrice * 100)) > 1')
														->select(DB::raw(
															'sitedetails.*, MAX(100 - FLOOR(items.priceSingle / items.listPrice * 100)) AS discount'
														))
														->orderBy(DB::raw('MAX(100 - FLOOR(items.priceSingle / items.listPrice * 100))'), 'DESC')
														->groupBy('sitedetails.suppliers_id')
														->with('galleries');
														//->get()->toArray();
		if($query)
		{
			$supplier->where('sitedetails.supplierName', 'LIKE', "%$query%");
		}

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

		$count = DB::select(DB::raw("
			SELECT COUNT(*) AS aggregate FROM ({$supplier->toSql()}) AS t1
		"), $supplier->getBindings())[0]->aggregate;


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
		];
		
		return Response::json($data,200);
	}
}