App.ApplicationController = Em.ObjectController.extend({

	needs: ['cart'],

	cart: Ember.computed.alias('this.controllers.cart'),

  regions: Ember.computed.alias("model.regions"),
  categories: Ember.computed.alias("model.categories"),

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

});