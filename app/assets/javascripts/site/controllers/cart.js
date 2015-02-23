App.CartController = Em.ArrayController.extend({

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