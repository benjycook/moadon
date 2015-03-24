App.ItemController = Em.ObjectController.extend({

	total: function(){
		return this.get('count') * this.get('priceSingle');
	}.property('count', 'priceSingle'),

	totalListPrice: function(){
		return this.get('count') * this.get('listPrice');
	}.property('count', 'listPrice'),

});