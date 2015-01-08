<?php 
class AdminClientsController extends BaseController 
{
	public function index()
	{
		$items = Input::get('items',10);
		$page  = Input::get('page',1);
		$query = Input::get('query',0);
		if($query=="")
			$query = 0;
		$sql = $query ? "CONCAT(firstName,' ',lastName) LIKE CONCAT('%',?,'%')" :'? = 0';
		$count = Client::whereRaw($sql,array($query))->count();
		$pages = ceil($count/$items);
		$clients = Client::whereRaw($sql,array($query))->forPage($page,$items)->get();
		$clients = $clients->toArray();
		$meta = array(
			'pages' => $pages,
			'count' => $count,
			'page'	=> $page
			);
		$data = array('collection'=>$clients,'meta'=>$meta);
		return Response::json($data,200);
	}

	public function store()
	{
		$json   = Request::getContent();
    	$data   = json_decode($json,true);
    	if(Client::where('taxId','=',$data['taxId'])->count())
    		return Response::json(array('error'=>'ת"ז זו כבר קיימת במערכת'),501);
    	if(!Club::where('id','=',$data['clubs_id'])->count())
    		return Response::json(array('error'=>'מועדון זה לא נמצא במערכת'),501);
    	$client = Client::create($data);
		return Response::json($client,201);
	}
	public function show($id)
	{
		$client = Client::find($id);
		if($client)
			return Response::json($client,200);
		return Response::json("לקוח זה אינו נמצא במערכת",501);
	}

	public function update($id)
	{
		$json=Request::getContent();
	    $data=json_decode($json,true);
		if($client=Client::find($id))
		{
			if(Client::whereRaw('taxId = ? AND id != ?',array($id,$data['taxId']))->count())
    			return Response::json(array('error'=>'ת"ז זו כבר קיימת במערכת'),501);
    		if(!Club::where('id','=',$data['clubs_id'])->count())
    			return Response::json(array('error'=>'מועדון זה לא נמצא במערכת'),501);
    		$client->fill($data);
    		$client->save();
			return Response::json($client,201);
		}
		else
			return Response::json("לקוח זה אינו נמצא במערכת",501);
	}
}	
