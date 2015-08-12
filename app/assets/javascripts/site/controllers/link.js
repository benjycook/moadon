App.LinkController = Em.ObjectController.extend(SimpleAuth.LoginControllerMixin, {
	
	authenticator: 'simple-auth-authenticator:jwt',

	needs: ['application'],
	
	club: Ember.computed.alias("controllers.application.options.club"),

	actions: {
		authenticate: function() {
			var _this = this;
			var model = this.get('model');
			var auth =  this._super(model).then(function(data){
				_this.transitionToRoute('index');
			}, function(data){
				_this.transitionToRoute('index');
				_this.set('error', data.error)
			});
			return auth;
		},
	}

});