App.IndexRoute = App.ProtectedRoute.extend({

	renderTemplate: function()
	{
		var appCtrl = this.controllerFor('application');
		
		this.render('index', {
			into: 'application', 	
			controller: appCtrl
		});
		
		this.render('search/sidebar', {into: 'application', outlet: 'search'});
	}

});