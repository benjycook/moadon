App = Ember.Application.create({
	//LOG_TRANSITIONS: true, 
	defualtImg: "../img/defualt.png",
	logedin: false,
	base: "/galleries/tempimages",
});

App.identificationTypes = Em.ArrayController.create();
App.states = Em.ArrayController.create();
App.regions = Em.ArrayController.create();
App.categories = Em.ArrayController.create();
App.clubs = Em.ArrayController.create();
App.itemTypes = Em.ArrayController.create();
Ember.Application.initializer({
  name: "options",
 
  initialize: function(container, application) {
  	application.deferReadiness();
	    $.ajax({
				type: 'GET',
				url: 'options',
			}).then(function(data){
				App.identificationTypes.set('content', data.identificationTypes);
				App.states.set('content', data.states);
				App.regions.set('content', data.regions);
				App.categories.set('content', data.categories);
				App.clubs.set('content', data.clubs);
				App.itemTypes.set('content', data.itemTypes);
				App.set('logedin',data.logedin);
				application.advanceReadiness();
			});
  }
});

App.Router.map(function(){
	
	this.route('login');
	this.route('logout');

	this.resource('orders', function(){
		//this.route('create');
		this.route('edit', {path: ':orders_id/edit'});
	});
	this.resource('clients', function(){
		this.route('create');
		this.route('edit', {path: ':clients_id/edit'});
	});
	this.resource('users', function(){
		this.route('create');
		this.route('edit', {path: ':user_id/edit'});
	});

	this.resource('clubs', function(){
		this.route('create');
		this.route('edit', {path: ':clubs_id/edit'});
	});

	this.resource('suppliers', function(){
		this.route('create');
		this.route('edit', {path: ':suppliers_id/edit'});
	});

	this.resource('members', function(){
		this.route('create');
		this.route('edit', {path: ':members_id/edit'});
	});

	this.resource('items', function(){
		this.route('create');
		this.route('edit', {path: ':items_id/edit'});
	});

	this.resource('regions', function(){
		// this.route('create');
		// this.route('edit', {path: ':regions_id/edit'});
	});
	this.resource('categories', function(){
		// this.route('create');
		// this.route('edit', {path: ':categories_id/edit'});
	});

});

Em.TextField.reopen({
  attributeBindings: ['size','data-parsley-range','required','data-parsley-type','data-parsley-minlength','data-parsley-maxlength','readonly',"data-parsley-equalto","data-parsley-min",'data-parsley-idcheck']
});
Em.TextArea.reopen({
  attributeBindings: ['data-parsley-range','required','data-parsley-type','data-parsley-minlength','data-parsley-maxlength','readonly',"data-parsley-equalto","data-parsley-min",'data-parsley-idcheck']
});
Em.Select.reopen({
	attributeBindings: ['required','pattern']
});

App.ModalView = Em.View.extend({
	didInsertElement: function(){
		//console.log('view', this);
		this.$('.modal').show().addClass('in');
		//document.ontouchmove = function(e){ e.preventDefault(); };
		this.$('form').parsley();
		window.scrollTo(0,0);
		$(document.body).addClass('lockscroll');
		//document.body.style.overflow="hidden";
	},

	
	keyPress:function(event,view)
	{

		if(event.keyCode == 13&&this.$('.sendEnter'))
		{
		  this.$('.sendEnter').trigger('click');
		}
		else 
		{
		  if(event.key == 27&&this.$('.sendExit'))
		  {
		  	this.$('.sendExit').trigger('click');
		  }
		}
	},
	willDestroyElement: function()
	{
		$(document.body).removeClass('lockscroll');
		//document.ontouchmove = function(e){ return true; }
	}
});



App.FormView = Em.View.extend({
	didInsertElement: function(){
		this.$('form').parsley();

	},
	keyPress:function(event,view)
	{
		var localName = 'textarea';
		if(event.keyCode == 13&&this.$('.sendEnter')&&event.target.localName.toLowerCase()!=localName)
		{
		  this.$('.sendEnter').trigger('click');
		}
		else 
		{
		  if(event.key == 27&&this.$('.sendExit'))
		  {
		  	this.$('.sendExit').trigger('click');
		  }
		}
	},
	input: function()
	{
		if(this.get('model')) 
			this.set('model.changed',true);
		if(this.get('_context.content')) 
			this.set('_context.content.changed',true);

	},

	change: function()
	{
		if(this.get('model')) 
			this.set('model.changed',true);
		if(this.get('_context.content')) 
			this.set('_context.content.changed',true);
	},
	willDestroyElement: function(){
		//console.log('distroy model and form validation');
	}
});