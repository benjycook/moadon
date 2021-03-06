<?php 
class AdminOrdersController extends BaseController 
{
	public function index()
	{
		$items = Input::get('items',10);
		$page  = Input::get('page',1);
		$query = Input::get('query',0);
        $startDate  = Input::get('startDate',0);
        $endDate    = Input::get('endDate',0);
        $status    = Input::get('orderStatus',0);
        $suppliers_id    = Input::get('suppliers_id',0);
        if($startDate)
            $startDate = date('Y-m-d',strtotime(str_replace('/','-',$startDate)));
        if($endDate)
            $endDate = date('Y-m-d',strtotime(str_replace('/','-',$endDate)));
		if($query=="")
			$query = 0;
		$sql = $query ? "CONCAT_WS(' ',orders.code,orders.id,firstName,lastName,taxId,auth) LIKE CONCAT('%',?,'%')" :'? = 0';
        $base = Order::whereRaw($sql,array($query))
                ->leftjoin('creditguardlog','creditguardlog.orders_id','=','orders.id')->where('success',1);
        if($startDate)
            $base->whereRaw('DATE(createdOn) >= ?',[$startDate]);
         if($endDate) 
            $base->whereRaw('DATE(createdOn) <= ?',[$endDate]);
        if(!empty($status)&&in_array($status,[1,2,3,4]))
            $base->where('orders_statuses_id',$status);
        if($suppliers_id) 
            $base->whereHas('items',function($q) use($suppliers_id){
                $q->where('suppliers_id',$suppliers_id);
            });
		$count = $base->count();
		$pages = ceil($count/$items);
		$orders = $base->with('club')->forPage($page,$items)->orderBy('orders.id','DESC')
            ->select(DB::raw('orders.*,creditguardlog.auth AS auth'))->get();
		$orders = $orders->toArray();
        $newOrders = [];

        foreach ($orders as $order) 
        {
            $item = OrderItem::where('orders_id', '=', $order['id']);
            if($suppliers_id)
                $item->where('suppliers_id',$suppliers_id);
            $item = $item->first();
            $newOrders[] = array(
                'createdAt'     =>  date('d/m/y',strtotime($order['createdOn'])),
                'id'            =>  $order['id'], 
                'fullName'      =>  $order['firstName']." ".$order['lastName'], 
                'mobile'        =>  $order['mobile'],   
                'email'         =>  $order['email'],    
                'total'         =>  number_format(OrderItem::where('orders_id','=',$order['id'])->sum(DB::raw('qty*noCreditDiscountPrice')),2),    
                'clubName'      =>  $order['club']['name'], 
                'code'          =>  $order['code'],
                'docNumber'     =>  $order['docNumber'],
                'auth'          =>  $order['auth'],
                'status'        =>  $order['orders_statuses_id'],
                'taxId'         =>  $order['taxId'],
                'supplierName'  =>  $item->supplier->name,
                'suppliers_id'  =>  $item->supplier->id,
            ); 
        }

		$meta = array(
			'pages' => $pages,
			'count' => $count,
			'page'	=> $page
			);
		$data = array('collection'=>$newOrders,'meta'=>$meta);
		return Response::json($data,200);
	}

	// public function store()
	// {
	// 	$json   = Request::getContent();
 //    	$data   = json_decode($json,true);
 //    	$rules = array(
 //    		'clients_id' => 'required',
 //    		);
 //    	$validator = Validator::make($data,$rules);
 //    	if($validator->fails())
 //    		return Response::json(array('error'=>"אנא וודא שסיפקת את כל הנתונים הדרושים"),501);
 //    	if(!$client = Client::where('id','=',$data['clients_id'])->first())
 //    		return Response::json(array('error'=>"לקוח זה לא קיים במערכת"),501);
 //    	if(!count($data['items']))
 //    		return Response::json(array('error'=>"יש לבחור לפחות מוצר אחד"),501);
 //    	foreach ($data['items'] as &$item) {
 //    		if(!isset($item['qty'])||intval($item['qty'])<1)
 //    			return Response::json(array('error'=>"הכמות של מוצר לא יכולה להיות קטנה מ-1"),501);
 //    		$qty = $item['qty'];
 //    		if(!isset($item['id'])||!$item = Item::where('id','=',$item['id'])->first())
 //    			return Response::json(array('error'=>"אנא וודא שסיפקת את כל הנתונים הדרושים"),501);
 //    		$item = $item->toArray();
 //    		$item['qty'] = $qty;
 //    		$item['items_id'] = $item['id'];
 //    	}
    	
 //    	$client = $client->toArray();
 //    	$client['createdOn'] = date('Y-m-d H:i:s');
 //    	$client['clients_id'] = $client['id'];
 //    	$client['invoiceFor'] = isset($data['invoiceFor']) ? $data['invoiceFor']:"";
 //    	$order = new Order;
 //    	$order = $order->create($client);
 //    	foreach ($data['items'] as $item) {
 //    		$orderItem = new OrderItem;
 //    		$item['orders_id'] = $order->id;
 //    		$orderItem->create($item);
 //    	}
	// }

	public function show($id)
	{
		$order = Order::with(array('items'=>function($q){$q->with('supplier');$q->with('realized');}))->with('payment')->where('id','=',$id)->first();
		if(!$order)
			return Response::json(array('error'=>"הזמנה זו לא נמצאה במערכת"),501);
        $order = $order->toArray();
        $order['createdOn'] = date('d/m/y',strtotime($order['createdOn']));
        $order['total'] = $order['payment']['amount'];
        $order['cardNumber'] = substr($order['payment']['cardmask'],-4);
        $order['ownerName'] = "";//$order['payment']['holdername'];
        $order['ownerId'] = $order['payment']['holderid'];
        $order['numberOfPayments'] = 1;
        $order['realized'] = [];
        $suppliers   = array();
        foreach ($order['items'] as &$item) {
            $item['supplierName'] = $item['supplier']['name'];
            $item['total'] = $item['qty']*$item['noCreditDiscountPrice'];
            $suppliers[$item['supplierName']] = $item['supplier']['id'];
            unset($item['supplier']);
        }
        $relizations = [];
        foreach ($suppliers as $supKey=>&$value) {
            $realized = Realized::join('orders_items','orders_items.id','=','orders_items_id')->where('suppliers_id','=',$value)->where('orders_items.orders_id','=',$id)
            ->select(DB::raw('name,realizedOn,realizedQty,qty,orders_items.id AS id'))->get();
            $temp = $realized->lists('id');
            $temp = array_unique($temp);
            $previous = [];
            foreach ($temp as $key => $value) {
               $previous[$value] = 0;
            }
            foreach ($realized as &$temp) {
                $temp['realizedOn'] = date('d/m/y H:i:s',strtotime($temp['realizedOn']));
                $temp['left'] = $temp['qty']-$temp['realizedQty']-$previous[$temp['id']];
                $previous[$temp['id']] += $temp['realizedQty'];
            }
            if(count($realized))
                $relizations[] = array('supplierName'=>$supKey,'items'=>$realized);
        }
        $order['realizations'] = $relizations;
        $order['cancel'] = $order['orders_statuses_id']==1 ? true:false;
        unset($order['payment']);
		return Response::json($order,200);
	}

    public function cancelOrder($id)
    {
        $json   = Request::getContent();
        $data   = json_decode($json,true);
        if(!isset($data['code']))
            $data['code'] = 0;
        $order = Order::where('code','=',$data['code'])->first();
        if(!$order)
            return Response::json(array('error'=>"הזמנה זו לא נמצאה במערכת"),501);
        if($order->orders_statuses_id==1)
        {
            $order->orders_statuses_id = 4;
            $order->save();
        }
        return Response::json('ההזמנה בוטלה בהצלחה.',201);
    }
}	
