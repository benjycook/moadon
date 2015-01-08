App.UsersEditController = Em.ObjectController.extend({});

App.UsersCreateRoute = App.UsersEditRoute = App.ProtectedRoute.extend({
	controllerName:'usersEdit',

	model: function(params)
	{
		if(params.user_id)
			return $.getJSON('users/'+params.user_id);
		return {};
	},

	setupController: function(ctrl, model)
	{
		ctrl.set('model', model);
	},


	renderTemplate: function()
	{		
		this.render('users/index');
		this.render('users/modal', {into: 'application', outlet: 'modal'});
	}
});
