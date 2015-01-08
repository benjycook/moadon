App.SuppliersRoute = App.ProtectedRoute.extend({

	actions: {
		'save': function(model,view,controller)
		{
			var self = this;
			var form = view.$('form');
			var valid = form.parsley().validate();
			if(!valid)
			{
				var tabName = form.find('.parsley-error').first().closest('.tab-pane').attr('id');
	            Em.$('[href=#'+tabName+']').click();
	            return;
			}
			
			var url = "suppliers";
			var type = "POST";
			if(model.id)
			{
				url  = "suppliers/"+model.id;
				type = "PUT"; 
			}
			$.ajax({
				type: type,
				url: url,
				data: JSON.stringify(model)
			}).then(function(){
				self.transitionTo('suppliers');
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
			controller.set('success',null);
		}
	}
});
App.SuppliersIndexRoute = App.ProtectedRoute.extend({

	setupController: function(ctrl)
	{
		ctrl.refresh();
	},

});