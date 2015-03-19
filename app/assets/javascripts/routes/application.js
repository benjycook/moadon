App.LogoutRoute = Em.Route.extend({

	beforeModel: function(){
		$.getJSON('logout');
		this.transitionTo('login');
	}

});
App.LoginRoute  = Em.Route.extend({

	renderTemplate: function()
	{		
		var data = this.render('application/login', {
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
	},
	
	actions: {
		'login': function(model, view){
			var self = this;
			var controller = this.controllerFor('login');
			var form = view.$('form');
			var valid = form.parsley().validate();
			if(!valid)
				return;

			$.ajax({
				type: 'POST',
				url: 'login',
				data: JSON.stringify(model)
			}).then(function(data){
				localStorage.setItem('logged',true);
				self.transitionTo('users');
			}).fail(function(data){
				if(data.status == 500)
					var error = "אנא נסה שנית או פנה לתמיכה טכנית";
				else
					var error = data.responseJSON.error;
				controller.set('error',error);
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
				});
			}
		},
		'restore':function()
		{
			this.render('application/restore', {
				  into: 'application',
				  outlet: 'modal'
			});
		},

		'back':function()
		{
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

App.LoginController = Em.ObjectController.extend({
});

App.ProtectedRoute = Em.Route.extend({

	beforeModel: function(){
		var logged = localStorage.getItem('logged');
		if(logged)
		{
			App.set('logedin',true);
		}
		else
		{
			App.set('logedin',false);
			this.transitionTo('login');
		}	
	},

	actions: {
    error: function(reason, transition) {
      if (reason.status === 401||reason.status === 405) 
      	this.transitionTo("login");
    }
  }

});


App.ApplicationRoute = Em.Route.extend({
	beforeModel:function()
	{
		var self = this;
		 $.ajaxSetup({
		 	complete: function(xhr, stat) {
		 		var states = [400,500,501];
		 		if(xhr.responseJSON!=undefined&&xhr.responseJSON.fileUpload&&states.indexOf(xhr.status)==-1)
		 			return;
       			$('.modal').scrollTop(0);
       				window.scrollTo(0,0);
   			},
	        error: function (x, status, error) {
	            if (x.status == 401) {
	                self.transitionTo('login');
	            }
	        }
	    });
	}
});