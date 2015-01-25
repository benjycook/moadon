App.ApplicationController = Em.ObjectController.extend({

	needs: ['cartIndex', 'search'],

	search: Em.computed.alias("controllers.search"),

	cart: Ember.computed.alias("controllers.cartIndex"),

	queryParams: {
    eregions: {
      replace: true
    },

    ecategories: {
      replace: true
    },

    qregions: {
      replace: true
    },

    qcategories: {
      replace: true
    }
  },

  eregions: [],
  ecategories: '',
  qregions: '',
  qcategories: '',

  updateExpended: function(){
  	console.log(this.get('eregions'));
  }.observes('eregions'),

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

	}.property('regions'),



});