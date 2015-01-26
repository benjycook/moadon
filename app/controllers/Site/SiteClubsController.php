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

	public function options($slug)
	{
		$club = Club::site()->where('urlName','=',$slug)->first();

		if(!$club)
			return Response::json('מועדון זה לא נמצאה במערכת',404);

		$data = array();
		$data['club'] = $club->toArray();
		$data['club']['logo'] = URL::to('/')."/".$data['club']['logo'];

		$data['regions'] 		= Region::where('parent_id','=',0)->with('children')->get();
		$data['categories'] = Category::where('parent_id','=',0)->with('children')->get();


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
		$supplier['region'] = $supplier['regions_id'] ? $regions[$supplier['regions_id']]['name']:"";
	
		$rawImages = array();
		$images = $supplier['galleries'][0]['images'];
		foreach ($images as $image) {
			$rawImages[] = URL::to('/')."/galleries/{$image['src']}";
		}
		unset($supplier['galleries']);
		$supplier['images'] = $rawImages;
		
		unset($supplier['regions_id']);
		unset($supplier['suppliers_id']);
		return Response::json($supplier,200);
	}

	public function search()
	{
		$region = Input::get('region', 0);
		$category = Input::get('category', 0);
		$subregions = Input::get('subregions',0);
		$subcategories = Input::get('subcategories',0);
		$name = Input::get('supplier',0);
		//$item = Input::get('item',0);
		$supplier = SiteDetails::mini();
		if($category)
		{
			if($subcategories)
			{
				$subcategories = explode(',', $subcategories);
				$temp = Category::whereIn('id', $subcategories)->with('children')->get()->toArray();
			}
			else
			{
				$temp = Category::where('id', '=', $category)->with('children')->get()->toArray();
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
				$temp = Region::whereIn('id', $subregions)->with('children')->get()->toArray();
			}
			else{
				$temp = Region::where('id', '=', $region)->with('children')->get()->toArray();
			}
			
			$regions = $this->flaten($temp);

			$supplier->whereHas('regions',function($q) use($regions){
				$q->whereIn('regions_id', $regions);
			});
		}

		if($name)
		{
			if($name=="")
				$name = 0;
			$sql = $name ? "supplierName LIKE CONCAT('%',?,'%')" :'? = 0';
			$supplier->whereRaw($sql,array($name));
		}
		$suppliers = $supplier->get();

		$regions = Region::with('children')->get();
		
		foreach ($suppliers as &$supplier) {
			$rawImages = array();
			$images = $supplier['galleries'][0]['images'];
			foreach ($images as $image) {
				$rawImages[] = URL::to('/')."/galleries/{$image['src']}";
			}
			unset($supplier['galleries']);
			$supplier['images'] = $rawImages;
		}

		return Response::json($suppliers,200);
	}
}