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
		'closeModal': function(){
			this.disconnectOutlet({
	      outlet: 'lightbox',
	      parentView: 'application'
	    });
		},

		'addItem': function(item, supplierName)
		{
			var cartCtrl = this.controllerFor('cart');
			var found = cartCtrl.findBy('id', item.get('id'));
			if(!found)
			{
				var newItem = Em.copy(item.get('model'));
				newItem.supplierName = supplierName;
				cartCtrl.pushObject(newItem, true);
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

		'openRegister': function(transitionTo)
		{
			var ctrl = this.controllerFor('account.register');

			ctrl.set('transitionTo', transitionTo);

			ctrl.set('model', {
				firstName: '',
				lastName: '',
				email: '',
				mobile: '',
				password: '',
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
			if(this.get('session.claims.loginType') != 'client')
			{
				this.send('openRegister', ['checkout']);
				return;
			}

			var ctrl = this.controllerFor('checkout');
			
				ctrl.set('model', {});

				this.render('account/checkout', {
					into: 'application',
					outlet: 'lightbox',
					controller: ctrl
				});

		}
	}

});