App.HotdealsRoute = App.ProtectedRoute.extend({

	model: function()
	{
		return Em.$.getJSON('hotdeals');
	},

	renderTemplate: function()
	{
		this._super();
		this.render('search/sidebar', {into: 'application', outlet: 'search'});
	}

});