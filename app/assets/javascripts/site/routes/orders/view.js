App.OrdersViewRoute = App.UserProtectedRoute.extend({
	model:function(params)
	{
		if(params.orders_id)
			return $.getJSON('orders/'+params.orders_id);
		return {};
	},
})