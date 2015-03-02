App.CheckoutController = Em.ObjectController.extend({

	needs: ['cart'],

	cart: Ember.computed.alias('this.controllers.cart'),

	actions: {
		'pay': function()
		{
			var model = this.get('model');
			
			var _this = this;

			$.ajax({
				type: 'POST',
				url: 'orders',
				data: JSON.stringify(model)
			}).then(function(data){
			//var data = data.responseJSON;
				_this.set('success', data.success);
				_this.set('error', null);
				_this.set('cart.content', []);
			}).fail(function(data){
				var data = data.responseJSON;
				_this.set('error', data.error);
			});
		}
	}

});