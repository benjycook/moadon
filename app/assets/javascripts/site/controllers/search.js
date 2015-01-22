App.SearchController = Em.ArrayController.extend({

	needs: ['application'],

	queryParams: {
    qregions: {
      replace: true
    },

    qcategories: {
      replace: true
    }
  },

  qregions: [],
  qcategories: [],

	//app: Ember.computed.alias("controllers.application"),

	// updateSearch: function(){
	// 	console.log('update search');
	// }.observes('app.qcategories', 'app.qregions')

});