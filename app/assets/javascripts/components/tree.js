App.UiTreeComponent = Ember.Component.extend({
  isTreeComponent: true,
  tagName: 'ul',
  classNames: ['tree-branch']
});

App.UiTreeNodeComponent = Ember.Component.extend({
  tagName: 'li',

  classNames: ['tree-node'],

  isExpanded: false,
  isEditable: false,

  allowAdd: function() {
    if(this.get('level') < 3)
      return true;
    return false;
  }.property('level'),

  isBranch: function() {
      return this.get('node.children').length > 0;
  }.property('node.children', 'node.children.@each'),

  isLeaf: function() {
      return !this.get('node.children').length;
  }.property('node.children', 'node.children.@each'),

  level: function() {
    var parent = this.nearestWithProperty('isTreeComponent');
    var level = 0;
    while(parent)
    {
      level++;
      parent = parent.nearestWithProperty('isTreeComponent');
    }
    return level;
  }.property('parent'),

  isDeletable: function()
  {
    return !this.get('isBranch');
  }.property('isBranch'),

  actions: {
      toggle: function() {
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
        this.get('parent.children').removeObject(this.get('node'));
      }

  }

});
