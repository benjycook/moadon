App.ApplicationRoute = Em.Route.extend(SimpleAuth.ApplicationRouteMixin, {
	
	model:function()
	{
		return $.getJSON('home');
	},

	setupController: function(ctrl,model){
		var options = this.get('options');
		model.categories = {children: options.categories};
		
		model.regions = {
			children: [
				{name: "חיפוש לפי אזור", children: options.regions, id: 0}
		]};

		if(model.cart)
		{
			var cartCtrl = this.controllerFor('cart');
			cartCtrl.set('suspendUpdate',true);
			cartCtrl.set('model',[]);
			model.cart.forEach(function(item){
				 cartCtrl.pushObject(item,true);
			});
			cartCtrl.set('suspendUpdate',false);
			delete model.cart;
		}
		
		ctrl.set('model',model);
	},

	actions: {
		'closeModal': function(){
				this.disconnectOutlet({
		      outlet: 'lightbox',
		      parentView: 'application'
		    });
		},
		'close':function(ctrl)
		{
			ctrl.set('model.success',false);
			ctrl.set('model.error',false);
		},
		'addItem': function(item, supplierName)
		{
			var cartCtrl = this.controllerFor('cart');
			var found = cartCtrl.findBy('id', item.get('id'));
			if(!found)
			{
				var newItem = Em.copy(item.get('model'));
				newItem.supplierName = supplierName;
				console.log(newItem);
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

		'openLogin': function(transitionTo)
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
			ctrl.set('showLogin', true);
			this.render('account/modal', {
				into: 'application',
				outlet: 'lightbox',
				controller: ctrl
			});

		},

		'openPassReminder': function()
		{
			var ctrl = this.controllerFor('account.register');

			ctrl.set('model', {
				email: '',
			});

			this.render('account/restore', {
				into: 'application',
				outlet: 'lightbox',
				controller: ctrl
			});
		},

		'checkout': function()
		{
			if(this.get('session.claims.loginType') != 'client')
			{
				this.send('openLogin', ['checkout']);
				return;
			}
			var self = this;
			$.getJSON('checkout').then(function(data){
				var ctrl = self.controllerFor('checkout');
			
				ctrl.set('model', data);

				self.render('account/checkout', {
					into: 'application',
					outlet: 'lightbox',
					controller: ctrl
				});
			});

		},
		'success':function(info)
		{
			console.log(info);
			var ctrl = this.controllerFor('checkout');
			var cartCtrl = this.controllerFor('cart');
			cartCtrl.set('suspendUpdate',true);
			cartCtrl.set('model',[]);
			info.cart.forEach(function(item){
				 cartCtrl.pushObject(item,true);
			});
			cartCtrl.set('suspendUpdate',false);
			ctrl.set('model',info.order);
		},
		'creditGuardError': function(data)
		{
			console.log(data);
			var ctrl = this.controllerFor('checkout');
			var error = data.ErrorText+" מספר: "+data.ErrorCode;
			ctrl.set('model',{error:error,success:1});
		},
		'cancelCheckOut':function()
		{
			this.send('openCart');
		},
		'closeMsg':function(controller)
		{
			controller.set('error',null);
			controller.set('success',null);
		}
	}

});