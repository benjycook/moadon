App.SupplierRoute = Em.Route.extend({

	model: function(params)
	{
		return $.getJSON('supplier/'+params.id);
	},

	// setupController: function(ctrl, model)
	// {
	// 	console.log(ctrl, model);
	// }

});