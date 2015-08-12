Ember.Application.initializer({
  name: 'fastclick',

  initialize: function() {
    Ember.run.schedule('afterRender', function() {
      FastClick.attach(document.body)
    });
  }
});
