App.UiFormComponent = Em.Component.extend({

    tagName: 'form',

    addParslay: function(){
    		if(this.get('parslay') !== false)
    		{
       	 	this.$().parsley();
        	this.$().attr('autocomplete','off');
        }
    }.on('didInsertElement'),

    submit: function()
    {
        this.sendAction();
        return false;
    }

});