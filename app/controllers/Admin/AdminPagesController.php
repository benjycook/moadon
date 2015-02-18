<?php 
class AdminPagesController extends BaseController 
{
	public function index()
	{
		$pages = Page::all();
		foreach ($pages as $page) {
			$page->link = "#".$page->id;
		}
		$pages[0]['first'] = true;
		return Response::json(array('pages'=>$pages),200);
	}
	public function update($id)
	{
		$json   = Request::getContent();
    	$data   = json_decode($json);
		$page = Page::find($id);
		if(!$page)
			return Response::json(array('error'=>'דף זה לא נמצא במערכת.'),501);
		$page->text = $data->text;
		$page->save();
		return Response::json('success',201);
	}
}	
