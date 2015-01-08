App.ClientsEditController = Em.ObjectController.extend({
});

App.ClientsCreateRoute  = App.ClientsEditRoute = App.ProtectedRoute.extend({
	controllerName: 'clientsEdit',

	model: function(params)
	{
		if(params.clients_id)
			return $.getJSON('clients/'+params.clients_id);
		return {contacts:[]};
	},

	setupController: function(ctrl,model)
	{
		ctrl.set('model', model);
	},

	renderTemplate: function()
	{		
		this.render('clients/index');
		this.render('clients/modal',{into: 'application',outlet: 'modal'});
	},

	actions:
	{
		save:function(model,view,controller)
		{
			var form = view.$('form');
			var valid = form.parsley().validate();
			if(!valid)
				return;
			var url = "clients";
			var type = "POST";
			if(model.id)
			{
				url = "clients/"+model.id;
				type = "PUT";
			}
			var self = this;
			$.ajax({
				type:  type,
				url:  url,
				data: JSON.stringify(model)
			}).then(function(data){
				self.transitionTo('clients.index');
			}).fail(function(data){
				if(data.status == 500)
					var error = "אנא נסה שנית או פנה לתמיכה טכנית";
				else
					var error = data.responseJSON.error;
					controller.set('error',error);
			});
		},
		

		close:function(controller)
		{
			controller.set('error',null);
		},

		closeWindow:function()
		{
			this.render('empty', {into: 'application',outlet: 'modal'});
		}
	}
});
