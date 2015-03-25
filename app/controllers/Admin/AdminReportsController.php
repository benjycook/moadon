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
		$query = "select * from
					(SELECT suppliers.name            AS supplierName,
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
					        AND date(createdOn) <= ? group by supplierId) AS t1

					left join

					 (SELECT   sum(IF(fullyrealized = 1,1,0)) as realizedNum,
					           suppliers.id       AS supplierId,
					           sum(IF(fullyrealized = 1,pricesingle*qty,0)) AS realizedPayedTotal,
					           sum(IF(fullyrealized = 1,netprice*qty,0)) AS realizedNetTotal 
					           FROM items_realizations
					           INNER JOIN orders_items
					           ON         orders_items.id = orders_items_id
					           INNER JOIN suppliers
					           ON         suppliers.id = orders_items.suppliers_id WHERE date(realizedOn) >= ?
					           AND        date(realizedOn) <= ? group by supplierId) AS t2
					on t1.supplierId = t2.supplierId";
		$data = [];
		$data['reports'] = DB::select($query,[$startDate,$endDate,$startDate,$endDate]);
		$data['queries'] = DB::getQueryLog();
		return Response::json($data,201);
	}
}