App.UiTreeComponent = Ember.Component.extend({
  tagName: 'ul',
  classNames: ['tree-branch']
});

App.UiTreeNodeComponent = Ember.Component.extend({
  tagName: 'li',

  classNames: ['tree-node'],

  isExpanded: false,
  isEditable: false,

  isSelected: function(){
    return this.get('node.selected');
  }.property('node.selected'),

  isLeaf: function() {
      return !this.get('node.children').length;
  }.property('node.children', 'node.children.@each'),

  isDeletable: function()
  {
    return this.get('node.children.length') < 1
  }.property('node.children.@each'),

  actions: {

      select: function(){
        var isSelected = this.get('node.selected');
        this.set('node.selected', !isSelected);
      },

      expend: function(){
        var expended = this.get('isExpanded');
        if(!expended)
        {
          this.set('isExpanded', !expended);
          Ember.run.scheduleOnce('afterRender', this, function(){
            this.$('> ul').hide();
            this.$('> ul').slideDown();
          });
        }
        else
          Ember.run.scheduleOnce('afterRender', this, function(){
            var self = this;
            this.$('> ul').slideUp(function(){
              self.set('isExpanded', !expended);
            });
          });
      },

      toggle: function() {
        var isLeaf = this.get('isLeaf')

        if(isLeaf)
          this.send('select');
        else
          this.send('expend');
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
