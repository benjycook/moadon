App.ApplicationController = Em.ObjectController.extend({

	needs: ['cart'],

	cart: Ember.computed.alias('this.controllers.cart'),

  regions: Ember.computed.alias("model.regions"),
  categories: Ember.computed.alias("model.categories"),
	club: Ember.computed.alias("options.club"),

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


	flatRegions: function(){
		var regions = this.get('regions.children');
		var flatRegions = [];

		var visitor = function(arr, func)
		{
			for(var i = 0; i < arr.length; i++)
			{
				func(arr[i]);
				visitor(arr[i]['children'], func);
			}
		};

		var flatten = function(item)
		{
			flatRegions.push({
				id: item.id,
				name: item.name
			});
		};

		visitor(regions, flatten);

		return flatRegions;

	}.property('model.regions'),

	actions: {
		search: function(){
			this.transitionToRoute('search', {queryParams: {
				q: this.get('search'),
			  region: null,
			  subregions: [],
			  category: null,
			  subcategories: []
			}});
		}
	}

});