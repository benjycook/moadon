<?php

class AdminOptionsController extends BaseController {

		public function index()
		{
			$data = array();
			$data['identificationTypes'] = IdentificationType::orderBy('name', 'ASC')->get()->toArray();
			$data['states']       = State::orderBy('name', 'ASC')->get()->toArray();
			$data['clubs']       = Club::orderBy('name', 'ASC')->get()->toArray();
			$data['regions']     = Region::orderBy('name', 'ASC')->get()->toArray();
			$data['categories']  = Category::orderBy('name', 'ASC')->get()->toArray();
			$data['itemTypes']  = ItemType::orderBy('name', 'ASC')->get()->toArray();		
			if($user = Auth::user())
				$data['logedin'] = true;
			else
				$data['logedin'] = false;
			return Response::json($data,200);
		}
}