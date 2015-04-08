Ember.Application.initializer({
  name: "options",
  after: 'simple-auth-token',
  initialize: function(container, application) {
    console.log('options');
  	application.deferReadiness();
  	
  	var Options = Em.Object.extend();

    container.register('options:main', Options);

    Ember.A(['controller', 'route', 'component']).forEach(function(component) {
      container.injection(component, 'options', 'options:main');
    });

  	Em.$.getJSON('site/options').then(function(data){
      var Options = container.lookup('options:main');

  		for(var i in data.options)
  		{
  			Options.set(i, data.options[i]);
  		}
      if(data.cart)
      {
        var cart = this.controllerFor('cart');
        cart.set('model',data.cart);
      }
  		application.advanceReadiness();

  	});
  },

});