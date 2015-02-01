App.IndexRoute = Em.Route.extend({

	renderTemplate: function()
	{
		this.render('index', {into: 'application'});
		this.render('search/sidebar', {into: 'application', outlet: 'search'});
	}
	
});