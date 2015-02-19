App.SearchRoute = App.ProtectedRoute.extend({

	 queryParams: {

	 	page: {
	 		refreshModel: false
	 	},

	 	items: {
	 		refreshModel: false
	 	},

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
		terms.push('items'+items);

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
		// console.log(params, this.controller.get('page'));
		// //this.controller.set('page', params.page+1);
		params.page = this.controller.get('page');
		return $.getJSON(this.query(params));
	},

	model: function(params)
	{
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
		var obj = {
			suppliers: model
		};

		ctrl.set('model', model);
	},

	actions: {
		'fetchMore': function(callback) {
			var ctrl = this.controller;
			//console.log(ctrl.get('page'));
			//Em.run('next')
			ctrl.set('page', ctrl.get('page') + 1);
			//console.log(ctrl.get('page'));
		  var promise = this.fetchMoreItems();
		  callback(promise);
		},
	}

});