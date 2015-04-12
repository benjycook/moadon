App.IndexRoute = App.ProtectedRoute.extend({
	
	renderTemplate: function()
	{
		this.render('index', {into: 'application'});
		this.render('search/sidebar', {into: 'application', outlet: 'search'});
	}

});