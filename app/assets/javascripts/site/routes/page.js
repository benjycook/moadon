App.PageRoute = App.ProtectedRoute.extend({

	model: function(params)
	{
		return Em.$.getJSON('page/' + params.page_id);
	},

	renderTemplate: function()
	{
		this.render('page', {into: 'application'});
		this.render('search/sidebar', {
			into: 'application', 
			outlet: 'search',
			controller: this.controllerFor('search')
		});
	},

});