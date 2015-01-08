<?php 
class AdminOrdersController extends BaseController 
{
	public function index()
	{
		$items = Input::get('items',10);
		$page  = Input::get('page',1);
		$query = Input::get('query',0);
		if($query=="")
			$query = 0;
		$sql = $query ? "name LIKE CONCAT('%',?,'%')" :'? = 0';
		$count = Order::whereRaw($sql,array($query))->count();
		$pages = ceil($count/$items);
		$orders = Order::whereRaw($sql,array($query))->forPage($page,$items)->get();
		$orders = $orders->toArray();
		$meta = array(
			'pages' => $pages,
			'count' => $count,
			'page'	=> $page
			);
		$data = array('collection'=>$orders,'meta'=>$meta,'query'=>DB::getQueryLog());
		return Response::json($data,200);
	}

	public function store()
	{
		$json   = Request::getContent();
    	$data   = json_decode($json,true);
    	$rules = array(
    		'clients_id' => 'required',
    		);
    	$validator = Validator::make($data,$rules);
    	if($validator->fails())
    		return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים הדרושים"),501);
    	if(!$client = Client::where('id','=',$data['clients_id'])->first())
    		return Response::json(array('error'=>"לקוח זה לא קיים במערכת"),501);
    	if(!count($data['items']))
    		return Response::json(array('error'=>"יש לבחור לפחות מוצר אחד"),501);
    	foreach ($data['items'] as &$item) {
    		if(!isset($item['qty'])||intval($item['qty'])<1)
    			return Response::json(array('error'=>"הכמות של מוצר לא יכולה להיות קטנה מ-1"),501);
    		$qty = $item['qty'];
    		if(!isset($item['id'])||!$item = Item::where('id','=',$item['id'])->first())
    			return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים הדרושים"),501);
    		$item = $item->toArray();
    		$item['qty'] = $qty;
    		$item['items_id'] = $item['id'];
    	}
    	
    	$client = $client->toArray();
    	$client['createdOn'] = date('Y-m-d H:i:s');
    	$client['clients_id'] = $client['id'];
    	$client['invoiceFor'] = isset($data['invoiceFor']) ? $data['invoiceFor']:"";
    	$order = new Order;
    	$order = $order->create($client);
    	foreach ($data['items'] as $item) {
    		$orderItem = new OrderItem;
    		$item['orders_id'] = $order->id;
    		$orderItem->create($item);
    	}
	}

	public function show($id)
	{
		$order = Order::with('items')->where('id','=',$id)->first();
		if(!$order)
			return Response::json(array('error'=>"הזמנה זו לא נמצאה במערכת"),501);
		return Response::json($order->toArray(),200);
	}
}	
