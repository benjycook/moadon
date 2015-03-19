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
	}

});