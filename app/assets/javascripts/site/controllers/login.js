App.LoginController = Em.ObjectController.extend(SimpleAuth.LoginControllerMixin, {
	
	authenticator: 'simple-auth-authenticator:jwt',

	needs: ['application'],
	
	club: Ember.computed.alias("controllers.application.options.club"),

	actions: {
		authenticate: function(type) {
			this.set('errorclub', false);
			this.set('erroruser', false);
			var _this = this;
			var model = this.get('model');
			model.type = type;

			var auth =  this._super(model).then(null, function(data){
				_this.set('error'+type, true);
				_this.set('error', data.error);
			});
			return auth;
		},
	}

});