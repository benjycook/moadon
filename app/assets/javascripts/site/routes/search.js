App.SearchRoute = App.ProtectedRoute.extend({

	 // beforeModel: function(){
	 // 	this.set('page', 1);
	 // },

	 queryParams: {

    region: {
      refreshModel: true
    },
    
    subregions: {
      refreshModel: true
    },

    category: {
      refreshModel: true
    },

    subcategories: {
      refreshModel: true
    }
  },

  query: function(params)
  {
  	var page = params.page;
  	var items = params.items;
		var region = params.region;
		var category = params.category;
		var subregions = params.subregions;
		var subcategories = params.subcategories;

		var terms = [];

		terms.push('page='+page);
		terms.push('items='+items);

		if(region && region+'' != 'undefined')
			terms.push('region='+region);
		
		if(subregions && subregions.length > 0)
			terms.push('subregions='+subregions);
		
		if(category && category+'' != 'undefined')
			terms.push('category='+category);

		if(subcategories && subcategories.length > 0)
			terms.push('subcategories='+subcategories);

		return 'search?'+terms.join('&');
  },

	fetchMoreItems: function()
	{
		var params = this.paramsFor('search');
		params.page = this.controller.get('page');
		params.items = 9;
		return $.getJSON(this.query(params));
	},

	model: function(params)
	{
		params.page = 1;
		params.items = 9;
		return $.getJSON(this.query(params));
	},

	renderTemplate: function()
	{
		this.render('search', {into: 'application'});
		this.render('search/sidebar', {
			into: 'application', 
			outlet: 'search',
			controller: this.controllerFor('search')
		});
	},

	setupController: function(ctrl, model)
	{
		ctrl.set('page', 1);
		ctrl.set('meta', model.meta);
		ctrl.set('model', model.data);
	},

	actions: {
		'fetchMore': function(callback) {
			var ctrl = this.controller;
			ctrl.incrementProperty('page');
		  var promise = this.fetchMoreItems();
		  callback(promise);
		},
	}

});