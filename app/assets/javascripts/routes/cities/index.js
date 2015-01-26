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
			}).then(function(){
				self.transitionTo('cities');
			}).fail(function(data){
				if(data.status == 500)
					var error = "אנא נסה שנית או פנה לתמיכה טכנית";
				else
					var error = data.responseJSON.error;
					controller.set('error',error);
			});
		},

		'closeMsg':function(controller)
		{
			controller.set('error',null);
			controller.set('success',null);
		},

		'closeWindow':function(controller)
		{
			this.send('closeMsg',controller);
			this.render('empty',{into:'application',outlet:'modal'});
			controller.refresh();
		},

		'delete':function(controller,id)
		{
			var self = this;
			this.send('closeMsg',controller);
			$.ajax({
				type: "DELETE",
				url: "cities/"+id,
			}).then(function(data){
				var cities = App.get('cities');
				var city = cities.findBy('id',id);
				cities.removeObject(city);
				controller.set('success',data.success);
			}).fail(function(data){
				if(data.status == 500)
					var error = "אנא נסה שנית או פנה לתמיכה טכנית";
				else
					var error = data.responseJSON.error;
					controller.set('error',error);
			}).always(function(){
				self.render('notice',{into:'application',outlet:'modal',controller:controller});
			});
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