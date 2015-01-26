App.SearchController = Em.ArrayController.extend({

  queryParams: {
    region: {
      replace: true
    },

    subregions: {
      replace: true
    },

    category: {
      replace: true
    },

    subcategories: {
      replace: true
    }
  },

  region: null,
  subregions: [],
  category: null,
  subcategories: [],

  // clearSubRegions: function(){
  //   var self = this;
  //   Em.run('afterRender', function(){
  //     self.set('subregions', []);
  //   });
  // }.observes('region')

});