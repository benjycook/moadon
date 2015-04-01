App.AccountRoute = App.UserProtectedRoute.extend({
	renderTemplate:function()
	{
		this.render('account/sidebar',{into:'application',outlet:'search'});
	},
});