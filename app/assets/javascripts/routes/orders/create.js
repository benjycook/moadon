App.OrdersEditController = Em.ObjectController.extend({

});

App.OrdersCreateRoute = App.OrdersEditRoute = App.ProtectedRoute.extend({
	controllerName:'ordersEdit',

	model: function(params)
	{
		if(params.orders_id)
			return $.getJSON('orders/'+params.orders_id);
		return {};
	},

	setupController: function(ctrl, model)
	{
		ctrl.set('model', model);
	},


	renderTemplate: function()
	{		
		this.render('orders/index');
		this.render('orders/modal', {into: 'application', outlet: 'modal'});
	},

	actions:
	{
		'realized':function(item)
		{
			item.realized.forEach(function(item){
				var dateTime = item.realizedOn.split(' ');
				item.realizedDate = dateTime[0].split('-').reverse().join('/');
				item.realizedTime = dateTime[1];
			});
			var ctrl = App.ItemController.create({model:item});
			ctrl.set('target',this);//.controllerFor('ordersEdit')
			this.render('orders/realized', {into: 'application', outlet: 'modal',controller:ctrl});
		},

		'back':function()
		{
			this.render('orders/modal', {into: 'application', outlet: 'modal'});
		},
	}
});
