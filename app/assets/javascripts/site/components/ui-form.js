App.UiFormComponent = Em.Component.extend({

    tagName: 'form',
    autocomplete: true,
    parsley: true,

    _setup: function(){

        if(this.get('parsley'))
            this.$().parsley();

        if(!this.get('autocomplete'))
            this.$().attr('autocomplete','off');

    }.on('didInsertElement'),

    submit: function()
    {
        this.sendAction();
        return false;
    }

});