App.ProtectedRoute = Em.Route.extend({

	beforeModel: function()
	{
		console.log('session', this.controllerFor('application').get('session'));
		var session = this.controllerFor('application').get('session');
		console.log('session', session);
		if(session && session.club)
			return null;
		
		this.transitionTo('login');
	},

	actions: {
    error: function(reason, transition) {
      if (reason.status === 401||reason.status === 405) 
      	this.transitionTo("login");
    }
  }

});