App.IndexRoute = Ember.Route.extend({

	beforeModel: function(){
		console.log('indexroute before login');
		this.transitionTo('login');
	}

});