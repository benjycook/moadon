App.CitiesRoute = App.ProtectedRoute.extend({

	actions: {
		'save': function(model,view,controller)
		{
			var self = this;
			var form = view.$('form');
			var valid = form.parsley().validate();
			if(!valid)
				return;
			var url = "cities";
			var type = "POST";
			if(model.id)
			{
				url = "cities/"+model.id;
				type = "PUT";
			}
			$.ajax({
				type: type,
				url: url,
				data: JSON.stringify(model)
			}).then(function(data){
				var cities 	= App.get('cities');
				var city 	= cities.findBy('id',data.id);
				if(city)
					cities.removeObject(city);
				cities.pushObject(data);
				self.transitionTo('cities');
			}).fail(function(data){
				if(data.status == 500)
					var error = "אנא נסה שנית או פנה לתמיכה טכנית";
				else
					var error = data.responseJSON.error;
					controller.set('error',error);
			});
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
				url: 'cities/'+controller.get('deleteID'),
			}).then(function(data){
				var object = controller.get('rows').findBy('id',controller.get('deleteID'));
				controller.get('rows').removeObject(object);
				var cities = App.get('cities');
				var city = cities.findBy('id',controller.get('deleteID'));
				cities.removeObject(city);
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

		'close':function(controller)
		{
			controller.set('error',null);
		}
	}
});
App.CitiesIndexRoute = App.ProtectedRoute.extend({

	setupController: function(ctrl)
	{
		ctrl.refresh();
	},

});