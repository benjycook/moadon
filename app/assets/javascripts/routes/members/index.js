App.MembersRoute = App.ProtectedRoute.extend({

	actions: {
		save: function(model,view,type)
		{
			var self = this;
			var form = view.$('form');
			var valid = form.parsley().validate();
			if(!valid)
				return;
			if(model.id)
				var url = "members/"+model.id;
			else
				var url = "members";
			$.ajax({
				type: type,
				url: url,
				data: JSON.stringify(model)
			}).then(function(){
				self.transitionTo('members');
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
		}
	}
});
App.MembersIndexRoute = App.ProtectedRoute.extend({

	setupController: function(ctrl)
	{
		ctrl.refresh();
	},
});