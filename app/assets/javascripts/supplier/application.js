App = Ember.Application.create({});

Ember.Application.initializer({
  name: "options",
 
  initialize: function(container, application) {
  	application.deferReadiness();
	    $.ajax({
				type: 'GET',
				url: 'options',
			}).then(function(data){
				App.set('logedin',data.logedin);
				application.advanceReadiness();
			});
  }
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
		this.$('form').on('submit',function(event){
			event.preventDefault();
		});
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
		this.$('form').on('submit',function(event){
			event.preventDefault();
		});
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