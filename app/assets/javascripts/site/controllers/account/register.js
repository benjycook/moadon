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
			}).fail(function(data){
				var data = data.responseJSON;
				_this.set('error', data.error);
			});
		},

		'openLogin': function(){
			this.set('showLogin', true);
		},

		'openRegister': function(){
			this.set('showLogin', false);
		}
	}
});