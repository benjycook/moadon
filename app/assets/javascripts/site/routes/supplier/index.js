App.SupplierRoute = Em.Route.extend({

	model: function(params)
	{
		return $.getJSON('supplier/'+params.id);
	}

});