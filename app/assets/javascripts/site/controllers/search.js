App.SearchController = Em.ArrayController.extend({

  needs: ['application'],

  regions: Ember.computed.alias("controllers.application.regions"),
  categories: Ember.computed.alias("controllers.application.categories"),

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

  clearSubRegions: function()
  {
    this.set('subregions', []);
  }.observes('region'),

  clearSubCategories: function()
  {
    this.set('subcategories', []);
  }.observes('category'),

  region: null,
  subregions: [],
  category: null,
  subcategories: [],

});