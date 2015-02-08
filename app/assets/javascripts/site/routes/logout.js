App.LogoutRoute = Em.Route.extend({

	beforeModel: function()
	{
		var self = this;
		var appCtrl = this.controllerFor('application');

		return $.getJSON('logout').then(function(){
			appCtrl.set('session', false);
			self.transitionTo('login');
		});
	}

});