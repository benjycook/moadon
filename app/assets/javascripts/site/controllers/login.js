App.LoginController = Em.ObjectController.extend(SimpleAuth.LoginControllerMixin, {
	
	authenticator: 'simple-auth-authenticator:jwt',

	needs: ['application'],
	
	club: Ember.computed.alias("controllers.application.club"),

	actions: {
		authenticate: function() {
			var _this = this;
			var model = this.get('model');

			var auth =  this._super(model).then(null, function(data){
				_this.set('error', data.error)
			});
			return auth;
		},
	}

});