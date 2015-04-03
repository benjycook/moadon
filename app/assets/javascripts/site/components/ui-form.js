App.UiFormComponent = Em.Component.extend({

    tagName: 'form',

    addParslay: function(){
        this.$().parsley();
        this.$().attr('autocomplete','off');
    }.on('didInsertElement'),

    submit: function()
    {
        this.sendAction();
    }

});