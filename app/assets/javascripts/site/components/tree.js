App.UiTreeComponent = Ember.Component.extend({
  tagName: 'ul',

  classNames: ['tree-branch']
});

App.UiTreeNodeComponent = Ember.Component.extend({
  tagName: 'li',

  classNames: ['tree-node'],

  isExpanded: false,
  isEditable: false,

  isDeletable: function()
  {
    return this.get('node.children.length') < 1
  }.property('node.children.@each'),

  actions: {
      toggle: function() {
        //if(this.get('node.children.length'))
          this.toggleProperty('isExpanded');
      },
      
      toggleEdit: function() {
        this.toggleProperty('isEditable');
        var self = this;

        Ember.run.scheduleOnce('afterRender', this, function(){
          this.$('input').focus();
        });
      },

      addChild: function() {
        var children = this.get('node.children');
        var node = {
          name: this.nearestWithProperty('title').get('title'),
          children: []
        };
        children.pushObject(node);
        this.set('isExpanded', true);
      },

      remChild: function(){
        console.log(this.get('parent'), this.get('node'));
        this.get('parent.children').removeObject(this.get('node'));
      }

  }

});
