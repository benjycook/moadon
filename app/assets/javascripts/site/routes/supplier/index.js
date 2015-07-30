App.SupplierRoute = App.ProtectedRoute.extend({


	model: function(params)
	{
		return $.getJSON('supplier/'+params.id);
	},

	setupController: function(ctrl, model){
		for(var i = 0; i < model.items.length; i++)
		{
			model.items[i]['count'] = 1;
		}
		ctrl.set('model', model);
	},

	renderTemplate: function()
	{
		this._super();
		var appCtrl = this.controllerFor('application');

		this.render('search/sidebar', {
			into: 'application', 
			outlet: 'search',
			controller: this.controllerFor('search')
		});
	},


});