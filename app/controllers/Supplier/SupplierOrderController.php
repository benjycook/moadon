<?php

class SupplierOrderController extends BaseController 
{
	
	public function order($id)
	{
		$orderItems = OrderItem::where('orders_id','=',$id)->where('suppliers_id','=',Auth::id())->where('fullyRealized','=',0)
						->leftjoin('items_realizations','items_realizations.orders_items_id','=','orders_items.id')
						->select(DB::raw('sum(realizedQty) AS realized,name,qty,orders_items_id AS id,orders_id'))->groupBy('orders_items_id')->get();
		if(count($orderItems))
		{
			$data = array('items'=>$orderItems);
		}
		else
		{
			if(!Order::where('id','=',$id)->count())
				$data = array('msg'=>'הזמנה זו לא נמצאה במערכת');
			else
				$data = array('msg'=>'כל הפריטים מומשו.');
		}
		return Response::json($data,200);
	}

	public function realize()
	{
		$json=Request::getContent();
	    $data=json_decode($json,true);
	    $rules = array(
    		'item' => 'required',
    		'realizedQty'=>'required|min:1'
    		);
    	$validator = Validator::make($data,$rules);
    	if($validator->fails())
    		return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים הדרושים"),501);
    	$rules = array(
    		'id' => 'required',
    		'orders_id'=>'required'
    		);
    	$validator = Validator::make($data['item'],$rules);
    	if($validator->fails())
    		return Response::json(array('error'=>"אנא וודא שסיפקתה את כל הנתונים הדרושים"),501);
		$orderItem = OrderItem::where('orders_id','=',$data['item']['orders_id'])->where('suppliers_id','=',Auth::id())
								->where('orders_items.id','=',$data['item']['id'])->where('fullyRealized','=',0)
								->join('items_realizations','items_realizations.orders_items_id','=','orders_items.id')
								->select(DB::raw('sum(realizedQty) AS realized,name,qty,orders_items.id,orders_id'))->first();
		if(!$orderItem)
			return Response::json(array('error'=>"אנא וודא שמוצר זה לא מומש במלואו."),501);
		if($orderItem->realized+$data['realizedQty']>$orderItem->qty)
			return Response::json(array('error'=>"אנא וודא שמוצר זה לא מומש במלואו."),501);
		Realized::create(array('realizedQty'=>$data['realizedQty'],'orders_items_id'=>$orderItem->id,'realizedOn'=>date('Y-m-d H:i:s')));
		if($data['realizedQty']+$orderItem->realized==$orderItem->qty)
		{
			$orderItem->fullyRealized = 1;
			$orderItem->save();
		}
		return Response::json('success',201);
	}

}