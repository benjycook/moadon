<?php

class AdminReportsController extends BaseController
{
	public function suppliersReport()
	{
		$startDate 	= Input::get('startDate',0);
		$endDate   	= Input::get('endDate',0);
		if($startDate)
			$startDate = date('Y-m-d',strtotime(str_replace('/','-',$startDate)));
		if($endDate)
			$endDate = date('Y-m-d',strtotime(str_replace('/','-',$endDate)));
		if(!$startDate&&!$endDate)
			return Response::json(['reports'=>[]],201);
		$query1 = "(SELECT  suppliers.name            AS supplierName,
					        suppliers.id              AS supplierId,
					        count(DISTINCT orders.id) AS ordersNum,
					        sum(pricesingle * qty)    AS ordersPayedTotal,
					        sum(netprice * qty)       AS ordersNetTotal
					 FROM   orders
					        INNER JOIN orders_items
					                ON orders.id = orders_id
					        INNER JOIN suppliers
					                ON suppliers.id = orders_items.suppliers_id
					 WHERE  date(createdOn) >= ?
					        AND date(createdOn) <= ? group by supplierId)";
		$query2 = "(SELECT   suppliers.name            AS supplierName,
								sum(realizedQty) as realizedNum,
					           suppliers.id       AS supplierId,
					           sum(pricesingle*qty) AS realizedPayedTotal,
					           sum(netprice*qty) AS realizedNetTotal 
					           FROM items_realizations
					           INNER JOIN orders_items
					           ON         orders_items.id = orders_items_id
					           INNER JOIN suppliers
					           ON         suppliers.id = orders_items.suppliers_id WHERE date(realizedOn) >= ?
					           AND        date(realizedOn) <= ? group by supplierId)";
		$bindings = [$startDate,$endDate];
		$data = [];
		$temp = [];
		$orders = DB::select($query1,$bindings);
		$realized = DB::select($query2,$bindings);
		foreach ($orders as $order) {
			$order = get_object_vars($order);
			$temp[$order['supplierId']] = $order;
		}
		foreach ($realized as $realizedItem) {
			$realizedItem = get_object_vars($realizedItem);
			if(isset($temp[$realizedItem['supplierId']]))
				$temp[$realizedItem['supplierId']] = array_merge($temp[$realizedItem['supplierId']],$realizedItem);
			else
				$temp[$realizedItem['supplierId']] = $realizedItem;
		}
		$new = [];
		foreach ($temp as $line) {
			$new[] = array_merge(['ordersNum'=>0,'ordersPayedTotal'=>0,'ordersNetTotal'=>0,'realizedNum'=>0,'realizedPayedTotal'=>0,'realizedNetTotal'=>0,'supplierName'=>""],$line);
		}
		$data['reports'] = $new;
		$data['queries'] = DB::getQueryLog();
		$data['realized'] = $realized;
		$data['orders'] = $orders;
		return Response::json($data,201);
	}
}