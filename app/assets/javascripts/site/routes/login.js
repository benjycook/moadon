App.LoginRoute = Em.Route.extend({

	model: function(){
		return {
				username: '',
				password: '',
				clubident: '',
		};
	},

	renderTemplate: function()
	{
		var appCtrl = this.controllerFor('application');

		this.render('index', {
			into: 'application', 
			controller: appCtrl
		});

		this.render('search/sidebar', {
			into: 'application', 
			outlet: 'search',
			controller: appCtrl
		});

		this.render('login', {
			into: 'application', 
			outlet: 'lightbox'
		});
	},

	actions: {
		'login': function(){
			var self = this;
			var model = this.modelFor('login');
			var appCtrl = this.controllerFor('application');

			$.ajax({
				type: 'POST',
				url: 'login',
				data: JSON.stringify(model)
			}).then(function(data){
				appCtrl.set('session', data);
				//localStorage.setItem('token', data.token);
				self.transitionTo('index');
			}).fail(function(data){
				if(data.status == 500)
					var error = "אנא נסה שנית או פנה לתמיכה טכנית";
				else
					var error = data.responseJSON.error;
				self.controller.set('error',error);
			});
		},
	}

});