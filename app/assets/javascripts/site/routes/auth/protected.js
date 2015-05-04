App.ProtectedRoute = Em.Route.extend(SimpleAuth.AuthenticatedRouteMixin, {});

App.UserProtectedRoute = App.ProtectedRoute.extend({
	beforeModel: function(transition, queryParams){
		this._super(transition, queryParams);
  	var session = this.get('session');
		if(this.get('session.claims.loginType') != 'client')
		{          
			transition.abort();
	        this.get('session').set('attemptedTransition', transition);
	        this.send('openLogin');
		}
  }
});