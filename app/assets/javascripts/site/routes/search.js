App.SearchRoute = Em.Route.extend({

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

	model: function(params)
	{
		//console.log(params);
		var region = params.region;
		var category = params.category;
		var subregions = params.subregions;
		var subcategories = params.subcategories;
		// var regions = [];//params.qregions.join(',');
		// var categories = [];//params.qcategories.join(',');
		var terms = [];
		if(region && region+'' != 'undefined')
			terms.push('region='+region);
		
		if(subregions && subregions.length > 0)
			terms.push('subregions='+subregions);
		
		if(category && category+'' != 'undefined')
			terms.push('category='+category);

		if(subcategories && subcategories.length > 0)
			terms.push('subcategories='+subcategories);
		// terms.push('categories='+categories);
		// terms.push('regions='+regions);
		terms = terms.join('&');
		return $.getJSON('search?'+terms);
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
	}

});