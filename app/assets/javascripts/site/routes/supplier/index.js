App.SupplierRoute = Em.Route.extend({


	model: function(params)
	{
		return $.getJSON('supplier/'+params.id);
	},

	renderTemplate: function()
	{
		this._super();
		var appCtrl = this.controllerFor('application');

		this.render('search/sidebar', {
			into: 'application', 
			outlet: 'search',
			controller: appCtrl
		});
	},


});