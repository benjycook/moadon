App.PagesRoute = App.ProtectedRoute.extend({
	actions: {
		'save': function(model,view)
		{
			var self = this;
			var form = view.$('form');
			var valid = form.parsley().validate();
			if(!valid)
				return;
			$('.sendEnter').attr('disabled','disabled');
			url = "pages/"+model.id;
			type = "PUT";
			$.ajax({
				type: type,
				url: url,
				data: JSON.stringify(model)
			}).always(function(){
				$('.sendEnter').attr('disabled',false);
			});
		},
	}
});
App.PageController = Em.ObjectController.extend({

});
App.PagesIndexRoute = App.ProtectedRoute.extend({
	model:function()
	{	
		return $.getJSON('pages');
	},
	setupController: function(ctrl,model)
	{
		ctrl.set('model',model);
	},
});