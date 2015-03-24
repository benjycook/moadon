<?php

class AdminReportsController extends BaseController
{
	public function suppliersReport()
	{
		$startDate 	= Input::get('startDate',0);
		$endDate   	= Input::get('endDate',0);
		$query 		= Order::join('orders_items','orders.id','=','orders_id')->leftjoin('items_realizations','orders_items.id','=','orders_items_id')
						->join('suppliers','suppliers.id','=','orders_items.suppliers_id')
						->select(DB::raw('suppliers.name AS supplierName,count(orders.id) AS ordersNum,sum(priceSingle*qty) AS ordersPayedTotal,sum(netPrice*qty) AS ordersNetTotal,
								sum(if((select count(*) from orders_items where orders_id = orders.id AND fullyRealized = 0)= 0,1,0) ) AS realizedNum,
								sum(if(fullyRealized = 1,priceSingle*qty,0)) AS realizedPayedTotal,
								sum(if(fullyRealized = 1,netPrice*qty,0)) AS realizedNetTotal'));
		if($startDate)
			$query->whereRaw('DATE(createdOn) >= ?',[date('Y-m-d',strtotime(str_replace('/','-',$startDate)))]);
		if($endDate)
			$query->whereRaw('DATE(createdOn) <= ?',[date('Y-m-d',strtotime(str_replace('/','-',$endDate)))]);
		$data = [];
		$data['reports'] = $query->groupBy('suppliers_id')->get()->toArray();
		$data['queries'] = DB::getQueryLog();
		return Response::json($data,201);
	}
}