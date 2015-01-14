App.ApplicationController = Em.ObjectController.extend({

	needs: ['cartIndex'],

	cart: Ember.computed.alias("controllers.cartIndex"),

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

	}.property('regions')

});