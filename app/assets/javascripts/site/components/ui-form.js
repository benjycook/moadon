App.UiFormComponent = Em.Component.extend({

    tagName: 'form',
    autocomplete:0,
    parsley:true,
    addParsley: function(){
        if(this.get('parsley'))
            this.$().parsley();
        if(this.get('autocomplete'))
            this.$().attr('autocomplete','on');
        else
            this.$().attr('autocomplete','off');
        if(this.$('.modal'))
            this.$('.modal').show().addClass('in');
    }.on('didInsertElement'),

    submit: function()
    {
        this.sendAction();
        return false;
    }

});