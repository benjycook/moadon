<?php

class SiteIndexController extends SiteBaseController {

	public function index()
	{
		return View::make('site.index', ['club' => $this->club]);
	}

	
}