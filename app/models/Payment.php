<?php
class Payment extends Eloquent{
	protected $table = 'payments';
	protected $fillable = array('id','orders_id','ownerName','ownerId','creditCardType','creditDealType','paymentType','cardNumber',
								'cvv','date','numberOfPayments','firstPayment','total','tranId','voucher');
}