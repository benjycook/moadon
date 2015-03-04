<?php

class SupplierOptionsController extends BaseController {

		public function index()
		{
			$data = array();
			Config::set('auth.model', 'Supplier');
			if($user = Auth::user())
				$data['logedin'] = true;
			else
				$data['logedin'] = false;
			return Response::json($data,200);
		}
}