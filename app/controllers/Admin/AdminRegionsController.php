<?php 

class AdminRegionsController extends BaseController 
{
	public $ids = array(-1);
	public $branchIds = array(-1);
	protected function idSet($region)
	{
		$this->branchIds[] = $region->id;
		if(count($region->children))
		{
			foreach ($region->children as $temp) {
				$this->idSet($temp);
			}
		}
	}
	protected function region($region)
	{
		if(!isset($region['id'])||!$temp = Region::find($region['id']))
			$temp = Region::create($region);
		else
		{
			if(!$region['parent_id']&&$region['parent_id']!=$temp->parent_id)
			{
				$temp->delete();
				return;
			}
			else
			{
				$temp = $temp->fill($region);
				$temp->save();
			}
		}
		$this->ids[] = $temp->id;
		if(isset($region['children'])&&count($region['children']))
		{
			foreach($region['children'] as $reg) 
			{
				//if(!isset($reg['parent_id']))
					$reg['parent_id'] = $temp->id;
				$this->region($reg);
			}
		}
		return;
	}
	public function index()
	{
		$regions = Region::with('children')->where('parent_id','=',0)->get();
		return Response::json($regions,200);
	}
	public function store()
	{
		$json=Request::getContent();
	    $data=json_decode($json,true);
	    $regionsTree = json_decode($this->index()->getContent(),true);
	    if(!is_array($data))
	    	return Response::json(array('error'=>'אנא וודא שספקתה את כל הפרטים','tree'=>$regionsTree),501);
	    foreach ($data as $region) {
	    	$this->region($region);
	    }
	    $bindings = DB::table('suppliers_regions')->whereNotIn('regions_id',$this->ids)->lists('regions_id');
	    $supplierMainRegions = SiteDetails::where('regions_id','!=','NULL')->where('regions_id','!=',0)->lists('regions_id');
	    $bindings = array_unique(array_merge($supplierMainRegions,$bindings));
	    $regions = Region::whereNotIn('id',$this->ids)->get();
	    foreach ($regions as $region) {
	    	$this->idSet($region);
	    	$test = array_intersect($this->branchIds,$bindings);
	    	if(count($test))
	    	{
	    		$region = Region::where('id','=',current($test))->first();
	    		return Response::json(array('error'=>'לא ניתן למחוק אזור "'.$region->name.'" מכיוון שהינו משויך לאחד הספקים','tree'=>$regionsTree),501);
	    	}
	    }
	    // if(count($bindings))
	    // {
	    // 	$region = Region::where('id','=',current($bindings))->first();
	    // 	return Response::json(array('error'=>'לא נינן למחוק אזור '.$region->name.' מכיוון שהוא משויך לאחד הספקים','tree'=>$regionsTree),501);
	    // }
	   
	    foreach ($regions as $region) {
	    	$region->delete();
	    }
	    $regions = Region::orderBy('name','ASC')->get();
	    $regionsTree = json_decode($this->index()->getContent(),true);
		return Response::json(array('regions'=>$regions,'tree'=>$regionsTree),200);
	}
}	