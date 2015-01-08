App.ClubsRoute = App.ProtectedRoute.extend({

	actions: {
		'save': function(model,view,controller)
		{

			var self = this;
			console.log(this);
			var form = view.$('form');
			var valid = form.parsley().validate();
			if(!valid)
				return;
			var url = "clubs";
			var type = "POST";
			if(model.id)
			{
				url  = "clubs/"+model.id;
				type = "PUT";
			}
				
			$.ajax({
				type: type,
				url: url,
				data: JSON.stringify(model)
			}).then(function(data){
				var clubs = App.get('clubs');
				club = clubs.findBy('id',data.id)
				if(club)
					clubs.removeObject(club);
				clubs.pushObject(data);
				self.transitionTo('clubs');
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
App.ClubsIndexRoute = App.ProtectedRoute.extend({

	setupController: function(ctrl)
	{
		ctrl.refresh();
	},
});