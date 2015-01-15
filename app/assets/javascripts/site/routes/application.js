App.ApplicationRoute = Em.Route.extend({
	
	model: function(){
		return $.getJSON('options');
	},

	setupController: function(ctrl, model){
		
		model.categories = {children: model.categories};
		
		model.regions = {
			children: [
				{name: "חיפוש לפי אזור", children: model.regions}
		]};

		ctrl.set('model', model);
	},

	actions: {
		'addItem': function()
		{
			var cart = this.controllerFor('cart.index');
			cart.pushObject({id: 1});
		}
	}

});