App.IndexController = Em.Controller.extend({

	needs: ['application'],

  regions: Ember.computed.alias("controllers.application.regions"),
  categories: Ember.computed.alias("controllers.application.categories"),

	newsuppliers: function(){
		var data =  this.get('model.newsuppliers').map(function(item){
			return item.images[0];
		});
		return data;
	}.property('model.newsuppliers'),

	mostviewed: function(){
		var data =  this.get('model.mostviewed').map(function(item){
			return item.images[0];
		});
		return data;
	}.property('model.mostviewed'),

	hotdeals: function(){
		var data =  this.get('model.hotdeals').map(function(item){
			return item.images[0];
		});
		return data;
	}.property('model.hotdeals'),

});
