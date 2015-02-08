App.LoginController = Em.ObjectController.extend({
	
	needs: ['application'],
	club: Ember.computed.alias("controllers.application.club"),

});