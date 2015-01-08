App.RegionsEditController = Em.ObjectController.extend({
	displayRegions:function()
	{
		var id = this.get('id');
		var regionsAll = this.get('regionsAll');
		if(id&&regionsAll)
		{
			this.set('regionsAll',null);
			return regionsAll;
		}
		return App.get('regions');
	}.property('content'),
});


App.RegionsCreateRoute = App.RegionsEditRoute = App.ProtectedRoute.extend({
	controllerName:'regionsEdit',

	model: function(params)
	{
		if(params.regions_id)
			return $.getJSON('regions/'+params.regions_id);
		return {};
	},

	setupController: function(ctrl, model)
	{
		ctrl.set('model', model);
	},


	renderTemplate: function()
	{		
		this.render('regions/index');
		this.render('regions/modal',{into: 'application',outlet: 'modal'});
		
	}
});
