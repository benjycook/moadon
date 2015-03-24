App.CheckoutController = Em.ObjectController.extend({

	needs: ['cart'],

	cart: Ember.computed.alias('this.controllers.cart'),
	years: [
		{id: '2015', name: '2015'},
		{id: '2016', name: '2016'},
		{id: '2017', name: '2017'},
		{id: '2018', name: '2018'},
		{id: '2019', name: '2019'},
		{id: '2020', name: '2020'},
	],

	months: [
		{id: '1', name: '1'},
		{id: '2', name: '2'},
		{id: '3', name: '3'},
		{id: '4', name: '4'},
		{id: '5', name: '5'},
		{id: '6', name: '6'},
		{id: '7', name: '7'},
		{id: '8', name: '8'},
		{id: '9', name: '9'},
		{id: '10', name: '10'},
		{id: '11', name: '11'},
		{id: '12', name: '12'},
	],

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