App.ItemsRoute = App.ProtectedRoute.extend({

	actions: {
		'save': function(model,view,controller)
		{
			var self = this;
			var form = view.$('form');
			var valid = form.parsley().validate();
			if(!valid)
				return;
			var url = "items";
			var type = "POST";
			if(model.id)
			{
				url  = "items/"+model.id;
				type = "PUT";
			}
				
			$.ajax({
				type: type,
				url: url,
				data: JSON.stringify(model)
			}).then(function(){
				self.transitionTo('items');
			}).fail(function(data){
				if(data.status == 500)
					var error = "אנא נסה שנית או פנה לתמיכה טכנית";
				else
					var error = data.responseJSON.error;
					controller.set('error',error);
			});
		},

		'close':function(controller)
		{
			controller.set('error',null);
		},

		'confirm':function(controller,id)
		{
			controller.set('deleteID',id);
			this.render('delete/confirm', {
				controller:controller,
			  into: 'application',
			  outlet: 'modal',
			});
		},
		'delete':function(controller)
		{
			var self = this;
			$.ajax({
				type: "DELETE",
				url: 'items/'+controller.get('deleteID'),
			}).then(function(data){
				var object = controller.get('rows').findBy('id',controller.get('deleteID'));
				controller.get('rows').removeObject(object);
				controller.set('deleteID',null);
				self.render('empty', {into: 'application',outlet: 'modal'});
			}).fail(function(data){
				if(data.status == 500)
					var error = "אנא נסה שנית או פנה לתמיכה טכנית";
				else
					var error = data.responseJSON.error;
					controller.set('error',error);
			});
		},
		'cancel':function(controller)
		{
			controller.set('deleteID',null);
			this.render('empty', {into: 'application',outlet: 'modal'});
		},
	}
});
App.ItemsIndexRoute = App.ProtectedRoute.extend({

	setupController: function(ctrl)
	{
		ctrl.refresh();
	},
});