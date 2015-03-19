App.PagesRoute = App.ProtectedRoute.extend({
	actions: {
		'save': function(model,view,controller)
		{
			var self = this;
			var form = view.$('form');
			var valid = form.parsley().validate();
			if(!valid)
				return;
			$(document.body).trigger('click');
			$('.sendEnter').attr('disabled','disabled');
			url = "pages/"+model.id;
			type = "PUT";
			$.ajax({
				type: type,
				url: url,
				data: JSON.stringify(model)
			}).then(function(){
				controller.set('error',null);
				controller.set('success',"נשמר בהצלחה.");
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
		},

		'close':function(controller)
		{
			controller.set('error',null);
			controller.set('success',null);
		}
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