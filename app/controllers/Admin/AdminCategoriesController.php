<?php 

class AdminCategoriesController extends BaseController 
{
	public $ids = array(-1);
	public $branchIds = array(-1);
	protected function idSet($category)
	{
		$this->branchIds[] = $category->id;
		if(count($category->children))
		{
			foreach ($category->children as $temp) {
				$this->idSet($temp);
			}
		}
	}
	protected function category($category)
	{
		if(!isset($category['id'])||!$temp = Category::find($category['id']))
			$temp = Category::create($category);
		else
		{
			if(!$category['parent_id']&&$category['parent_id']!=$temp->parent_id)
			{
				$temp->delete();
				return;
			}
			else
			{
				$temp = $temp->fill($category);
				$temp->save();
			}
		}
		$this->ids[] = $temp->id;
		if(isset($category['children'])&&count($category['children']))
		{
			foreach($category['children'] as $cat) 
			{
				//if(!isset($cat['parent_id']))
					$cat['parent_id'] = $temp->id;
				$this->category($cat);
			}
		}
		return;
	}
	
	public function index()
	{
		$categories = Category::with('children')->where('parent_id','=',0)->get();
		return Response::json($categories,200);
	}

	public function store()
	{
		$json=Request::getContent();
	    $data=json_decode($json,true);
	    $categoriesTree = json_decode($this->index()->getContent(),true);
	    if(!is_array($data))
	    	return Response::json(array('error'=>'אנא וודא שספקתה את כל הפרטים','tree'=>$categoriesTree),501);
	    foreach ($data as $category) {
	    	$this->category($category);
	    }

	    $bindings = DB::table('categories_suppliers')->whereNotIn('categories_id',$this->ids)->lists('categories_id');
	    $categories = Category::whereNotIn('id',$this->ids)->get();
	    foreach ($categories as $category) {
	    	$this->idSet($category);
	    	$test = array_intersect($this->branchIds,$bindings);
	    	if(count($test))
	    	{
	    		$category = Category::where('id','=',current($bindings))->first();
	     		return Response::json(array('error'=>'לא ניתן למחוק קטגוריה "'.$category->name.'" מכיוון שהינה משויכת לאחד הספקים','tree'=>$categoriesTree),501);
	    	}
	    }
	    // if(count($bindings))
	    // {
	    // 	$category = Category::where('id','=',current($bindings))->first();
	    // 	return Response::json(array('error'=>'לא נינן למחוק קטגוריה '.$category->name.' מכיוון שהיא משויכת לאחד הספקים','tree'=>$categoriesTree),501);
	    // }
	    // 
	    foreach ($categories as $category) {
	    		$category->delete();
	    }
	    $categories = Category::orderBy('name','ASC')->get();
	    $categoriesTree = json_decode($this->index()->getContent(),true);
		return Response::json(array('categories'=>$categories,'tree'=>$categoriesTree),200);
	}
}
