Ember.Application.initializer({
  name: "options",
  initialize: function(container, application) {

  	application.deferReadiness();
  	
  	var Options = Em.Object.extend();

    container.register('options:main', Options);

    Ember.A(['controller', 'route', 'component']).forEach(function(component) {
      container.injection(component, 'options', 'options:main');
    });

  	Em.$.getJSON('site/options').then(function(data){
      var Options = container.lookup('options:main');

  		for(var i in data)
  		{
  			Options.set(i, data[i]);
  		}
  		application.advanceReadiness();

  	});
  },

});