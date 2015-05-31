App.PageController = Em.Controller.extend({
	
	needs: ['application', 'search'],

	application: Ember.computed.alias("controllers.application"),
	options: Ember.computed.alias("application.options"),

});