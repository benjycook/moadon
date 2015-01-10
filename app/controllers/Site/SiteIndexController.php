<?php

	class SiteIndexController extends BaseController {

		public function index()
		{
			return View::make('site.index');
		}

	}