App.ApplicationRoute = Em.Route.extend(SimpleAuth.ApplicationRouteMixin, {
	
	model:function()
	{
		return $.getJSON('home');
	},

	setupController: function(ctrl,model){
		$(document).ajaxStart(function( event, jqxhr, settings, thrownError ) {
			//$(".blockUi").show();
				
		});
		$(document).ajaxStop(function( event, jqxhr, settings, thrownError ) {
			console.log("completed");
			//$(".blockUi").hide();
		});
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

		checkterms: function(){
			this.get('session').set('termsBoxOpen', true);
		},

		closeTermsBox: function(){
			this.get('session').set('termsBoxOpen', false);
		},

		terms: function(){
			this.get('session').set('termsBoxOpen', false);
			this.get('session').toggleProperty('terms');
		},

		'updateCartItems':function(cart)
		{
			var cartCtrl = this.controllerFor('cart');
			cartCtrl.set('suspendUpdate',true);
			cartCtrl.set('model',[]);
			cart.forEach(function(item){
				 cartCtrl.pushObject(item,true);
			});
			cartCtrl.set('suspendUpdate',false);
		},
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

		'openCart': function(type)
		{
			var cartCtrl = this.controllerFor('cart');
			var self = this;
			if(type)
			{
				$.getJSON('cart').then(function(data){
					self.send('updateCartItems',data);
					self.render('cart/index', {
						into: 'application',
						outlet: 'lightbox',
						controller: this.controller
					});
				});
			}
			else
			{
				self.render('cart/index', {
						into: 'application',
						outlet: 'lightbox',
						controller: this.controller
					});
			}
			
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
				taxId: '',
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
			var ctrl = this.controllerFor('checkout');
			this.send("updateCartItems",info.cart);
			ctrl.set('model',info.order);
		},
		'creditGuardError': function(data)
		{
			var ctrl = this.controllerFor('checkout');
			var error = data.ErrorText+" מספר: "+data.ErrorCode;
			ctrl.set('model',{error:error,success:1});
		},
		'cancelCheckOut':function()
		{
			this.send('openCart',1);
		},
		'closeMsg':function(controller)
		{
			controller.set('error',null);
			controller.set('success',null);
		}
	}

});