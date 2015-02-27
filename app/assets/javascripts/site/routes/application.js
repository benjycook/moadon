App.ApplicationRoute = Em.Route.extend(SimpleAuth.ApplicationRouteMixin, {

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
		'closeModel': function(){
			this.disconnectOutlet({
	      outlet: 'lightbox',
	      parentView: 'application'
	    });
		},

		'addItem': function(item)
		{
			var cartCtrl = this.controllerFor('cart');
			var found = cartCtrl.findBy('id', item.get('id'));
			if(!found)
			{
				cartCtrl.pushObject(Em.copy(item.get('model'), true));
			}	
			else
			{
				found.incrementProperty('count', item.get('count'));
			}

			this.send('openCart');
		},

		'removeItem': function(item)
		{
			var cartCtrl = this.controllerFor('cart');
			var found = cartCtrl.findBy('id', item.id);
			if(found)
				cartCtrl.removeObject(found);
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

		'openRegister': function()
		{
			var ctrl = this.controllerFor('account.register');
			
			ctrl.set('model', {
				firstName: 'איגור',
				lastName: 'גורושיט',
				email: 'igor@webt.co.il',
				mobile: '0525001920',
				password: '123456',
				cart_id: this.get('session.cart_id')
			});

			this.render('account/modal', {
				into: 'application',
				outlet: 'lightbox',
				controller: ctrl
			});
		},

		'checkout': function()
		{
			var ctrl = this.controllerFor('checkout');
			
				ctrl.set('model', {
					firstName: 'איגור',
					lastName: 'גורושיט',
					email: 'igor@webt.co.il',
					mobile: '0525001920',
					password: '123456',
					cart_id: this.get('session.cart_id')
				});

				this.render('account/checkout', {
					into: 'application',
					outlet: 'lightbox',
					controller: ctrl
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