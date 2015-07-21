App.OrderController = Em.ObjectController.extend({
	displayStatus:function()
	{
		var status = this.get('status');
	
		return App.get('orderStatuses').findBy('id',parseInt(status)).name;
	}.property('model'),
});