App.AccountRegisterController = Em.ObjectController.extend({

	actions: {
		register: function(){
			var model = this.get('model');
			$.ajax({
				type: 'POST',
				url: 'register',
				data: JSON.stringify(model)
			}).then(function(data){
				//_this.set('cart_id', data.cart_id);
				console.log(data);
			});
		}
	}
});