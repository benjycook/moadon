App.UsersRoute = App.ProtectedRoute.extend({

	actions: {
		'save': function(model,view,controller)
		{
			var self = this;
			var form = view.$('form');
			var valid = form.parsley().validate();
			if(!valid)
				return;
			var url = "users";
			var type = "POST";
			if(model.id)
			{
				url = "users/"+model.id;
				type = "PUT";
			}
			$.ajax({
				type: type,
				url: url,
				data: JSON.stringify(model)
			}).then(function(){
				self.transitionTo('users');
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
		}
	}
});
App.UsersIndexRoute = App.ProtectedRoute.extend({

	setupController: function(ctrl)
	{
		ctrl.refresh();
	},

});