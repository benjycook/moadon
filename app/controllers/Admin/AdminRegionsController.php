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
	    $bindings = DB::table('cities_regions')->whereNotIn('regions_id',$this->ids)->lists('regions_id');
	    $regions = Region::whereNotIn('id',$this->ids)->get();
	    $names = array();
	    $restricted = array();
	    foreach ($regions as $region) {
	    	$this->branchIds = array(-1);
	    	$this->idSet($region);
	    	$test = array_intersect($this->branchIds,$bindings);
	    	if(count($test))
	    		$restricted = array_merge($restricted,$this->branchIds);
	    }
	   	$restricted = array_unique($restricted);
	   	foreach ($restricted as $key => $value) {
	   		$reg = Region::find($value);
	   		if($reg)
	   			$names[] = $reg->name;
	   	}
	    foreach ($regions as $region) {
	    	if(!in_array($region->id,$restricted))
	    		$region->delete();
	    }
	    $regions = Region::orderBy('name','ASC')->get();
	    $regionsTree = json_decode($this->index()->getContent(),true);
	    if(count($names))
	    	return Response::json(array('error'=>'לא ניתן למחוק אזור "'.implode(',',$names).'" מכיוון שהינו משויך לאחד הישובים','tree'=>$regionsTree),501);
	    else
			return Response::json(array('regions'=>$regions,'tree'=>$regionsTree),200);
	}
}	