App.SearchRoute = Em.Route.extend({

  queryParams: {
    qcategories: {
      refreshModel: true
    },
    qregions: {
      refreshModel: true
    },    
  },

	model: function(params)
	{
		var regions = params.qregions.join(',');
		var categories = params.qcategories.join(',');
		var terms = [];
		terms.push('categories='+categories);
		terms.push('regions='+regions);
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