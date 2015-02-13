App.ItemController = Em.ObjectController.extend({

	listPriceTotal: function(){
		return this.get('count') * this.get('listPrice');
	}.property('count', 'listPrice'),

	clubPriceTotal: function(){
		return this.get('count') * this.get('clubPrice');
	}.property('count', 'clubPrice')

});