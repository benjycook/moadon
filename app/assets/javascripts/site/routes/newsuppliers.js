App.NewsuppliersRoute = App.ProtectedRoute.extend({

	model: function()
	{
		return Em.$.getJSON('newsuppliers');
	},

	renderTemplate: function()
	{
		this._super();
		this.render('search/sidebar', {into: 'application', outlet: 'search'});
	}

});