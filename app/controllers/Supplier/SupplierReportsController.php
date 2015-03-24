<?php

class SupplierReportsController extends BaseController
{
	public function realizations()
	{
		$startDate 	= Input::get('startDate',0);
		$endDate   	= Input::get('endDate',0);
		$query 		= ItemRealization::join('orders_items','orders_items.id','=','orders_items_id')->where('suppliers_id','=',Auth::id())
						->select(DB::raw('DATE_FORMAT(realizedOn,"%d/%m/%Y") AS realizedOn,orders_id AS orderId,name,realizedQty AS qty,netPrice*realizedQty AS total'));
		if($startDate)
			$query->whereRaw('DATE(realizedOn) >= ?',[date('Y-m-d',strtotime(str_replace('/','-',$startDate)))]);
		if($endDate)
			$query->whereRaw('DATE(realizedOn) <= ?',[date('Y-m-d',strtotime(str_replace('/','-',$endDate)))]);
		$data = [];
		$data['reports'] = $query->orderBy(DB::raw('DATE(realizedOn)'),'ASC')->get()->toArray();
		$total = ['realizedOn'=>'סיכום','total'=>0]; 
		foreach ($data['reports'] as $rep) {
			$total['total']+= $rep['total'];
			$rep['total'] = number_format($rep['total'],2);
		}
		$total['total'] = number_format($total['total'],2);
		$data['reports'][] = $total;
		$data['queries'] = DB::getQueryLog();
		return Response::json($data,201);
	}
}