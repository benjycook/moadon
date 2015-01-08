App.ClientsIndexRoute = App.ProtectedRoute.extend({

	setupController: function(ctrl)
	{
		ctrl.refresh();
	},

});