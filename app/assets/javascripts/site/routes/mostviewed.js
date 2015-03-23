App.MostviewedRoute = App.ProtectedRoute.extend({

	model: function()
	{
		return Em.$.getJSON('mostviewed');
	},

	renderTemplate: function()
	{
		this._super();
		this.render('search/sidebar', {into: 'application', outlet: 'search'});
	}

});