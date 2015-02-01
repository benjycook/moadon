App.CitiesEditController = Em.ObjectController.extend({});

App.CitiesCreateRoute = App.CitiesEditRoute = App.ProtectedRoute.extend({
	controllerName:'citiesEdit',

	model: function(params)
	{
		if(params.cities_id)
			return $.getJSON('cities/'+params.cities_id);
		return $.getJSON('cities/create');
	},

	setupController: function(ctrl, model)
	{
		model.allRegions = {
			name: 'root element',
			children: model.allRegions
		};
		ctrl.set('model', model);
	},


	renderTemplate: function()
	{		
		this.render('cities/index');
		this.render('cities/modal', {into: 'application', outlet: 'modal'});
	}
});
