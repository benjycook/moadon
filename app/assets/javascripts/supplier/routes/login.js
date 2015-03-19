App.LogoutRoute = Em.Route.extend({

	beforeModel: function(){
		$.getJSON('logout');
		this.transitionTo('login');
	}

});
App.LoginRoute  = Em.Route.extend({

	renderTemplate: function()
	{		
		this.render('application/login', {
		  into: 'application',
		  outlet: 'modal'
		});
	},

	model: function(){
		return Ember.Object.create();
	},

	setupController: function(ctrl, model)
	{
		ctrl.set('content',model);
		App.set('logedin',false);
		App.set('user',{});
	},
	actions: {
		'login': function(model, view){
			var self = this;
			var controller = this.controllerFor('login');
			var form = view.$('form');
			var valid = form.parsley().validate();
			if(!valid)
				return;
			$('.sendEnter').attr('disabled','disabled');
			$.ajax({
				type: 'POST',
				url: 'login',
				data: JSON.stringify(model)
			}).then(function(data){
				App.set('logedin',true);
				App.set('user',data.user);
				self.render('empty', {
					  into: 'application',
					  outlet: 'modal'
					});
				self.transitionTo('order');
			}).fail(function(data){
				if(data.status == 500)
					var error = "אנא נסה שנית או פנה לתמיכה טכנית";
				else
					var error = data.responseJSON.error;
				controller.set('error',error);
			}).always(function(){
					$('.sendEnter').attr('disabled',false);
				});
		},

		'sendRestore': function(model, view){
			var self = this;
			var controller = this.controllerFor('login');
			var form = view.$('form');
			var valid = form.parsley().validate();
			if(!valid)
				return;
			else
			{
				$('.sendEnter').attr('disabled','disabled');
				$.ajax({
					type: 'POST',
					url: 'restore',
					data: JSON.stringify(model)
				}).then(function(data){
					(self.controllerFor('login')).set('email',null);
					self.render('application/sendNotification', {
					  into: 'application',
					  outlet: 'modal'
					});
				}).fail(function(data){
					var error = data.responseJSON.error;
					controller.set('error',error);
				}).always(function(){
					$('.sendEnter').attr('disabled',false);
				});
			}
		},
		'restore':function(controller)
		{
			controller.set('error',null);
			this.render('application/restore', {
				  into: 'application',
				  outlet: 'modal'
			});
		},

		'back':function(controller)
		{
			controller.set('error',null);
			this.render('application/login', {
				  into: 'application',
				  outlet: 'modal'
			});
		},

		'close':function(controller)
		{
			controller.set('error',null);
		}

	}

});

