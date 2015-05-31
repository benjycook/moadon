<?php

class SitePagesController extends SiteBaseController 
{
	public function page($id)
	{
		$page = Page::find($id);

		return Response::json($page, 200);
	}
}