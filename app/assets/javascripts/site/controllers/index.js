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

  mostviewed: function(){
  	var mostviewed =  this.get('model.mostviewed').map(function(item){
  		return App.SupplierController.create({
  			model: item,
  			container: App.__container__
  		});
  	});

  	return mostviewed[0];
  }.property('model.mostviewed'),
  
	hotdeal: function(){
		var hotdeal =  this.get('model.hotdeal').map(function(item){
		    return App.SupplierController.create({
        model: item,
        container: App.__container__
      });
    });
		return hotdeal[0];
	}.property('model.hotdeal'),

});
