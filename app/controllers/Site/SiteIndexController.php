<?php

	class SiteIndexController extends BaseController {

		public function index($slug)
		{
			$club = Club::site()->where('urlName','=',$slug)->first();

			if(!$club)
				return "מועדון זה לא קיים";
			return View::make('site.index');
		}

	}