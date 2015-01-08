App.MembersEditController 	= Em.ObjectController.extend({
	isDisabled:function()
	{
		return true;
	}.property('content')
});

App.MembersCreateRoute = App.MembersEditRoute = App.ProtectedRoute.extend({
	controllerName:'membersEdit',

	model: function(params)
	{
		if(params.members_id)
			return $.getJSON('members/'+params.members_id);
		return $.getJSON("members/create");
	},

	setupController: function(ctrl, model)
	{
		ctrl.set('model', model);
		console.log(ctrl);
	},


	renderTemplate: function()
	{		
		this.render('members/index');
		this.render('members/modal', {into: 'application',outlet: 'modal'});
	}
});
