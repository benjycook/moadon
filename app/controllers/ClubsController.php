<?php

class ClubsController extends BaseController 
{
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

	public function options()
	{
		$clubName = Input::get('clubName',0);
		if(!$clubName)
			return Response::json('מועדון זה לא נמצאה במערכת',404);
		$club = Club::site()->where('urlName','=',$clubName)->first();
		if(!$club)
			return Response::json('מועדון זה לא נמצאה במערכת',404);
		$data = array();
		$data['club'] = $club->toArray();
		$data['club']['logo']   = $data['club']['logo']=="" ?  $data['club']['logo']:URL::to('/')."/".$data['club']['logo'];
		$data['regions'] = Region::with('children')->get();
		$data['categories'] = Category::with('children')->get();
		$data['mostViewed'] = SiteDetails::site()->whereHas('supplier',function($q){
			$q->where('views','>',0);
			$q->orderBy('views','DESC');

		})->take(10)->get();
		$ids = Collection::make($data['mostViewed'])->lists('id');
		if(!count($ids))
			array_push($ids,0);
		$data['newSuppliers'] = SiteDetails::site()->whereNotIn('id',$ids)->whereHas('supplier',function($q){
			$q->orderBy('created_at','DESC');
		})->take(10)->get();
		$ids = array_merge($ids,Collection::make($data['newSuppliers'])->lists('id'));
		$suppliers = SiteDetails::site()->whereNotIn('id',$ids)->get();
		$data['suppliers'] = $this->setUp($suppliers,$data['regions']);
		$data['mostViewed'] = $this->setUp($data['mostViewed'],$data['regions']);
		$data['newSuppliers'] = $this->setUp($data['newSuppliers'],$data['regions']);
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
		$supplier = SiteDetails::whereHas('supplier',function($q) use($id){
			$q->where('id','=',$id);
		})->mini()->first();
		if(!$supplier)
			return Response::json('ספק זה לא נמצאה במערכת',404);
		$regions = Region::with('children')->get();
		$supplier = $supplier->toArray();
		$supplier['region'] = $supplier['regions_id'] ? $regions[$supplier['regions_id']]['name']:"";
		$gallery = count($supplier['galleries']) ? $supplier['galleries'][0]:array('images'=>array());
		$supplier['galleries'] = $this->gallerySetUp($gallery);
		foreach ($supplier['items'] as &$item) {
			$gallery = count($item['galleries']) ? $item['galleries'][0]:array('images'=>array());
			$item['galleries'] = $this->gallerySetUp($gallery);
		}
		unset($supplier['regions_id']);
		unset($supplier['suppliers_id']);
		return Response::json($supplier,200);
	}

	public function search()
	{
		$regions = Input::get('regions',0);
		$categories = Input::get('categories',0);
		$name = Input::get('supplier',0);
		//$item = Input::get('item',0);
		$supplier = SiteDetails::mini();
		if($regions)
		{
			$regions = explode(',',$regions); 
			$supplier->whereHas('supplier',function($q1) use($regions){
				$q1->whereHas('regions',function($q2) use($regions){
					$q2->whereIn('regions_id',$regions);
				}); 
			});
		}
		if($categories)
		{
			$categories = explode(',',$categories); 
			$supplier->whereHas('supplier',function($q1) use($categories){
				$q1->whereHas('categories',function($q2) use($categories){
					$q2->whereIn('categories_id',$categories);
				}); 
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
			$supplier['region'] = $supplier['regions_id'] ? $regions[$supplier['regions_id']]['name']:"";
			$gallery = count($supplier['galleries']) ? $supplier['galleries'][0]:array('images'=>array());
			$supplier['galleries'] = $this->gallerySetUp($gallery);
			foreach ($supplier['items'] as &$item) {
				$gallery = count($item['galleries']) ? $item['galleries'][0]:array('images'=>array());
				$item['galleries'] = $this->gallerySetUp($gallery);
			}
			unset($supplier['regions_id']);
			unset($supplier['suppliers_id']);
		}
		return Response::json($suppliers,200);
	}
}