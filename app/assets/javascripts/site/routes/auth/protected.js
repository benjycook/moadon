App.ProtectedRoute = Em.Route.extend(SimpleAuth.AuthenticatedRouteMixin);
App.UserProtectedRoute = App.ProtectedRoute.extend({
	beforeModel: function(transition, queryParams){
		this._super(transition, queryParams);
  	var session = this.get('session');
		console.log('UserProtectedRoute', session.get('loginType'));
  }
});