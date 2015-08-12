App.AccountIndexRoute = App.UserProtectedRoute.extend({
	model:function()
	{
		return $.getJSON('info');
	},
	actions: {
		'update': function()
		{
			var model = this.get('controller.model');
			var _this = this;
			var msg = "";
			$.ajax({
				type: 'POST',
				url: 'update/info',
				data: JSON.stringify(model)
			}).then(function(data){
				msg = data;
				var session = _this.get('session');
				session.set('client',Em.copy(model));
				_this.set('controller.model.password',null);
			}).fail(function(data){
				if(data.status == 500)
					msg = "אנא נסה שנית או פנה לתמיכה טכנית";
				else
					msg = data.responseJSON.error;
			}).always(function(){
				var ctrl = Em.Controller.create({model:{msg:msg,header:"פרטי חשבון"}});
				ctrl.set('target',_this);
				_this.render('account/notice', {
					into: 'application',
					outlet: 'lightbox',
					controller: ctrl
				});
			});

			return false;
		},

		'closeModal': function()
		{
				this.disconnectOutlet({
		      outlet: 'lightbox',
		      parentView: 'application'
		    });
		}
	}
})