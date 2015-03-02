App.CartController = Em.ArrayController.extend({

	itemController: 'item',

	hasItems: function(){
		if(this.get('length') > 0)
			return true;
		return false;
	}.property('length'),

	updateItems: function(){
		var data = [];
		var items = this.get('model');
		for(var i = 0; i < items.length; i++)
		{
			data.push({
				id: items[i].id,
				qty: items[i].count
			});
		}

		var cart = {
			cart_id: this.get('session.cart_id'),
			items: data
		};
		
		var _this = this;

		$.ajax({
			type: 'POST',
			url: 'cart',
			data: JSON.stringify(cart)
		}).then(function(data){
			_this.set('cart_id', data.cart_id);
		});

	}.observes('model.@each.count'),

	total: function(){
		var items = this.get('model');
		var total = 0;

		for(var i = 0; i < items.length; i++)
		{
			total += (items[i]['clubPrice'] * items[i]['count']);
		}

		return total;

	}.property(
		'model.@each.count', 
		'model.@each.clubPrice', 
		'model.@each.listPrice'),

	totalDiscount: function(){
		var items = this.get('model');
		var total = 0;

		for(var i = 0; i < items.length; i++)
		{
			total += ((items[i]['listPrice'] - items[i]['clubPrice']) * items[i]['count']);
		}

		return total;

	}.property(
		'model.@each.count', 
		'model.@each.clubPrice', 
		'model.@each.listPrice')
});