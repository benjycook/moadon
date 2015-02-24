App.LoginController = Em.ObjectController.extend(SimpleAuth.LoginControllerMixin, {
	
	authenticator: 'simple-auth-authenticator:jwt',

	needs: ['application'],
	club: Ember.computed.alias("controllers.application.club"),

	actions: {
		authenticate: function() {
			var data = this.get('model');
			console.log('data', data);
			//this.set('password', null);
			var auth =  this._super(data);
			console.log('auth', auth);
			return auth;
		}
	}

});