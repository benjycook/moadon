App.LinkRoute = Em.Route.extend({

	model: function(params){
		return params;
	},

	setupController: function(ctrl, model)
	{
		ctrl.set('model', model);
		ctrl.send('authenticate');
	}

});