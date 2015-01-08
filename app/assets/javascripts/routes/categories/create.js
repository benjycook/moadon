App.CategoriesEditController = Em.ObjectController.extend({
	displayCategories:function()
	{
		var id = this.get('id');
		var categoriesAll = this.get('categoriesAll');
		if(id&&categoriesAll)
		{
			this.set('categoriesAll',null);
			return categoriesAll;
		}
		return App.get('categories');
	}.property('content'),
});


App.CategoriesCreateRoute = App.CategoriesEditRoute = App.ProtectedRoute.extend({
	controllerName:'categoriesEdit',

	model: function(params)
	{
		if(params.categories_id)
			return $.getJSON('categories/'+params.categories_id);
		return {};
	},

	setupController: function(ctrl, model)
	{
		ctrl.set('model', model);
	},


	renderTemplate: function()
	{		
		this.render('categories/index');
		this.render('categories/modal', {into: 'application', outlet: 'modal'});
	}
});
