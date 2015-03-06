App.ProtectedRoute = Em.Route.extend({

	beforeModel: function(){
		if(App.get('logedin')!=true)
			this.transitionTo('login');	
	},

	actions: {
    error: function(reason, transition) {
      if (reason.status === 401||reason.status === 405) 
      	this.transitionTo("login");
    }
  }

});


App.ApplicationRoute = Em.Route.extend({
	actions: {
		'openResponsiveMenu': function(){
			$('.xs-header').removeClass('hidden');
		},
		
		'closeResponsiveMenu': function(){
			$('.xs-header').addClass('hidden');
		}
	}

});