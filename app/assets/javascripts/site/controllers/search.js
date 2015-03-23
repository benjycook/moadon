App.SearchController = Em.Controller.extend({

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

  hesMore: function()
  {
    console.log(this.get('page'), this.get('meta'));
    if(this.get('page') < this.get('meta.pages'))
      return true;
    return false;
  }.property('meta.pages', 'page'),

  page: 1,
  items: 9,
  region: null,
  subregions: [],
  category: null,
  subcategories: [],

});