<?php

class SupplierOrderController extends BaseController 
{
	
	public function order($id)
	{
		$orderItems = OrderItem::where('code','=',$id)->where('suppliers_id','=',Auth::id())->where('fullyRealized','=',0)
						->leftjoin('items_realizations','items_realizations.orders_items_id','=','orders_items.id')
						->leftjoin('orders','orders.id','=','orders_items.orders_id')
						->select(DB::raw('sum(realizedQty) AS realized,name,qty,orders_items.id AS id,orders_id,CONCAT(orders.firstName," ",orders.lastName) AS clientName'))->groupBy('orders_items.id')->get();
		//return Response::json(DB::getQueryLog(),501);
		if(count($orderItems))
		{
			$client = array(
				'name' => $orderItems[0]->clientName
			);
			$data = array('client', 'items'=>$orderItems,'orderId'=>$id);
		}
		else
		{
			if(!Order::where('code','=',$id)->whereHas('items',function($q){$q->where('suppliers_id','=',Auth::id());})->count())
				return Response::json(array('msg'=>'הזמנה זו לא נמצאה במערכת'),501);
			else
				return Response::json(array('msg'=>'כל הפריטים מומשו.'),501);
		}
		return Response::json($data,200);
	}

	public function realize()
	{
		$json=Request::getContent();
	    $data=json_decode($json,true);
	    $rules = array(
    		'orderId'=> 'required',
    		);
    	$validator = Validator::make($data,$rules);
    	if($validator->fails())
    		return Response::json(array('error'=>"אנא וודא שסיפקת את כל הנתונים הדרושים"),501);
    	if(!$order = Order::with(array('items'=>function($q){$q->where('suppliers_id','=',Auth::id());}))->where("code",'=',$data['orderId'])->first())
    		return Response::json(array('error'=>"הזמנה זו לא נמצאה במערכת."),501);
    	foreach ($order->items as $item) {
    		$item->fullyRealized = 1;
    		$item->save();
    		Realized::create(array('realizedQty'=>$item->qty,'orders_items_id'=>$item->id,'realizedOn'=>date('Y-m-d H:i:s')));
    	}
		return Response::json('success',201);
	}

	//realize item
	// public function realize()
	// {
	// 	$json=Request::getContent();
	//     $data=json_decode($json,true);
	//     $rules = array(
 //    		'item' => 'required',
 //    		'realizedQty'=>'required|min:1'
 //    		);
 //    	$validator = Validator::make($data,$rules);
 //    	if($validator->fails())
 //    		return Response::json(array('error'=>"אנא וודא שסיפקת את כל הנתונים הדרושים"),501);
 //    	$rules = array(
 //    		'id' => 'required',
 //    		'orders_id'=>'required'
 //    		);
 //    	$validator = Validator::make($data['item'],$rules);
 //    	if($validator->fails())
 //    		return Response::json(array('error'=>"אנא וודא שסיפקת את כל הנתונים הדרושים"),501);
	// 	$orderItem = OrderItem::where('orders_id','=',$data['item']['orders_id'])->where('suppliers_id','=',Auth::id())
	// 							->where('orders_items.id','=',$data['item']['id'])->where('fullyRealized','=',0)
	// 							->join('items_realizations','items_realizations.orders_items_id','=','orders_items.id')
	// 							->select(DB::raw('sum(realizedQty) AS realized,name,qty,orders_items.id,orders_id'))->first();
	// 	if(!$orderItem)
	// 		return Response::json(array('error'=>"אנא וודא שמוצר זה לא מומש במלואו."),501);
	// 	if($orderItem->realized+$data['realizedQty']>$orderItem->qty)
	// 		return Response::json(array('error'=>"אנא וודא שמוצר זה לא מומש במלואו."),501);
	// 	Realized::create(array('realizedQty'=>$data['realizedQty'],'orders_items_id'=>$orderItem->id,'realizedOn'=>date('Y-m-d H:i:s')));
	// 	if($data['realizedQty']+$orderItem->realized==$orderItem->qty)
	// 	{
	// 		$orderItem->fullyRealized = 1;
	// 		$orderItem->save();
	// 	}
	// 	return Response::json('success',201);
	// }

}