App.RealizationsIndexRoute = App.ProtectedRoute.extend({
	queryParams: {
		startDate: {
			refreshModel: true
		},

		endDate: {
			refreshModel: true
		},
	},

	model: function(params){
		var query = [];
		for(var i in params)
		{
			query.push(i+'='+params[i]);
		}
		return $.getJSON('realizations?'+query.join('&'));
	},

	setupController:function(ctrl,model)
	{
		ctrl.set('model',model);
	},
	
});