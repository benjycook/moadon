App.ApplicationRoute = Em.Route.extend(
	SimpleAuth.ApplicationRouteMixin,
	{

	model: function(){
		return $.getJSON('options');
	},

	setupController: function(ctrl, model){
		
		model.categories = {children: model.categories};
		
		model.regions = {
			children: [
				{name: "חיפוש לפי אזור", children: model.regions, id: 0}
		]};

		ctrl.set('model', model);
	},

	actions: {
		'addItem': function(item)
		{
			var cartCtrl = this.controllerFor('cart');
			var found = cartCtrl.findBy('id', item.get('id'));
			if(!found)
				cartCtrl.pushObject(Em.copy(item.get('model'), true));
			
		},

		'openCart': function()
		{
			var cartCtrl = this.controllerFor('cart');
			this.render('cart/index', {
				into: 'application',
				outlet: 'lightbox',
				controller: this.controller
			});
		},

		'closeCart': function()
		{
	    this.disconnectOutlet({
	      outlet: 'lightbox',
	      parentView: 'application'
	    });
		}
	}

});