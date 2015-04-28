App.AccountRegisterController = Em.ObjectController.extend(SimpleAuth.LoginControllerMixin, {
	
	authenticator: 'simple-auth-authenticator:jwt',

	actions: {
		'register': function(){
			var _this = this;
			var authenticator = this.get('authenticator');
			var model = this.get('model');
			var data  = {};

			$.ajax({
				type: 'POST',
				url: 'account/register',
				data: JSON.stringify(model)
			}).then(function(data){
				var session = _this.get('session');
				session.setup(authenticator, data, false);
				session.set('client', data.client);
				if(_this.get('transitionTo') == 'checkout')
				{
					_this.send('checkout');
				}else{
					_this.send('closeModal');
					_this.transitionToRoute('account.index');
				}		
			}).fail(function(data){
				var data = data.responseJSON;
				_this.set('error', data.error);
			});
		},

		'login': function(data){
			var _this = this;
			var authenticator = this.get('authenticator');
			var model = this.get('model');
			var data  = {};

			data['email'] = model.email;
			data['password'] = model.password;

			$.ajax({
				type: 'POST',
				url: 'account/login',
				data: JSON.stringify(model)
			}).then(function(data){
				var session = _this.get('session');
				session.setup(authenticator, data, false);
				session.set('client', data.client);
				if(_this.get('transitionTo') == 'checkout')
				{
					_this.send('checkout');
				}else{
					_this.send('closeModal');
					_this.transitionToRoute('account.index');
				}		
				var cartCtrl = _this.controllerFor('cart');
				cartCtrl.set('suspendUpdate',true);
				cartCtrl.set('model',[]);
				data.cart.forEach(function(item){
				 	cartCtrl.pushObject(item,true);
				});
				cartCtrl.set('suspendUpdate',false);
			}).fail(function(data){
				var data = data.responseJSON;
				_this.set('error', data.error);
			});
		},

		'restore': function(){
			var _this = this;
			var model = this.get('model');
			$.ajax({
				type: 'POST',
				url: 'account/restore',
				data: JSON.stringify(model)
			}).then(function(data){
				_this.set('model',{success:data});
			}).fail(function(data){
				var data = data.responseJSON;
				if(data==undefined)
					_this.set('error', 'במהל הפעולה אירע שגיאה אנא פנה לתמיכה טכנית.');
				else
					_this.set('error', data.error);
			});
			return false;
		},

		'openLogin': function(){
			this.set('showLogin', true);
			this.send('closeMsg',this);
		},

		'openRegister': function(){
			this.send('closeMsg',this);
			this.set('showLogin', false);
		}
	}
});