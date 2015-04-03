App.IndexController = Em.Controller.extend({

	needs: ['application'],

  regions: Ember.computed.alias("controllers.application.regions"),
  categories: Ember.computed.alias("controllers.application.categories"),

  newsupplier: function(){
  	var newsuppliers =  this.get('model.newsuppliers').map(function(item){
  		return App.SupplierController.create({
  			model: item,
  			container: App.__container__
  		});
  	});

  	return newsuppliers[0];
  }.property('model.newsupplier'),

  newsupplier: function(){
  	var newsuppliers =  this.get('model.newsuppliers').map(function(item){
  		return App.SupplierController.create({
  			model: item,
  			container: App.__container__
  		});
  	});

  	return newsuppliers[0];
  }.property('model.newsupplier'),
  
	hotdeals: function(){
		var data =  this.get('model.hotdeals').map(function(item){
			return item.images[0];
		});
		return data;
	}.property('model.hotdeals'),

});
