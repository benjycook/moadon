App.OrdersEditController = Em.ObjectController.extend({

});

App.OrdersCreateRoute = App.OrdersEditRoute = App.ProtectedRoute.extend({
	controllerName:'ordersEdit',
	templateName:"orders/view",
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

	actions:
	{
		'close':function(controller)
		{
			controller.set('error',null);
			controller.set('success',null);
		},
		'openWarning':function()
		{
			var model = this.get('controller.model');
			var ctrl = Em.ObjectController.create({model:{id:model.id,code:null}});
			ctrl.set('target',this);
			this.render('orders/cancel', {into: 'application', outlet: 'modal',controller:ctrl});
		},

		'back':function()
		{
			this.render('empty', {into: 'application', outlet: 'modal'});
		},
		'cancelOrder':function(model,view,controller)
		{
			var self = this;
			var form = view.$('form');
			var valid = form.parsley().validate();
			if(!valid)
				return;
			$('.sendEnter').attr('disabled','disabled');
			url = "orders/"+model.id+"/cancel";
			type = "POST";
			$.ajax({
				type: type,
				url: url,
				data: JSON.stringify(model)
			}).then(function(data){
				controller.set('error',null);
				controller.set('success',data);
				self.controllerFor('ordersEdit').set('model.cancel',false);
				//self.send('back');
			}).fail(function(data){
				controller.set('success',null);
				if(data.status == 500)
					var error = "אנא נסה שנית או פנה לתמיכה טכנית";
				else
					var error = data.responseJSON.error;
					controller.set('error',error);
			}).always(function(){
				$('.sendEnter').attr('disabled',false);
			});
		}
	}
});
