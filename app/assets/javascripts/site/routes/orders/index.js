App.OrdersIndexRoute = App.UserProtectedRoute.extend({
	queryParams: {
		page: {
			refreshModel: true
		},
	},

	model: function(params){
		var query = [];
		for(var i in params)
		{
			query.push(i+'='+params[i]);
		}
		return $.getJSON('orders?'+query.join('&'));
	},
	setupController:function(ctrl,model)
	{
		ctrl.set('model',model);
		ctrl.set('meta',model.meta);
		window.scrollTo(0,0);
	}
})