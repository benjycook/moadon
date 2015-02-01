App.ItemController = Em.ObjectController.extend({

	count: 1,

	listPriceTotal: function(){
		return this.get('count') * this.get('listPrice');
	}.property('count', 'listPrice'),

	clubPriceTotal: function(){
		return this.get('count') * this.get('clubPrice');
	}.property('count', 'clubPrice')

});