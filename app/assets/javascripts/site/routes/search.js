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
		console.log(params);
		var region = this.get('region');
		var category = params.category;
		// var regions = [];//params.qregions.join(',');
		// var categories = [];//params.qcategories.join(',');
		var terms = [];
		terms.push('region='+region);
		terms.push('category='+category);
		// terms.push('categories='+categories);
		// terms.push('regions='+regions);
		terms = terms.join('&');
		return $.getJSON('search?'+terms);
	},

	setupController: function(ctrl, model)
	{
		var obj = {
			suppliers: model
		};

		console.log(model);
		ctrl.set('model', model);
	}

});