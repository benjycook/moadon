App.AccountRegisterController = Em.ObjectController.extend({

	actions: {
		'register': function(){

			var model = this.get('model');
			
			var _this = this;

			$.ajax({
				type: 'POST',
				url: 'account/register',
				data: JSON.stringify(model)
			}).then(function(data){
				//_this.set('cart_id', data.cart_id);
				console.log(data);
			}).fail(function(data){
				var data = data.responseJSON;
				console.log(data);
				_this.set('error', data.error);
			});
		},

		'login': function(){
			var model = this.get('model');
			
			var _this = this;

			$.ajax({
				type: 'POST',
				url: 'account/login',
				data: JSON.stringify(model)
			}).then(function(data){
				//_this.set('cart_id', data.cart_id);
				console.log(data);
			}).fail(function(data){
				var data = data.responseJSON;
				console.log(data);
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