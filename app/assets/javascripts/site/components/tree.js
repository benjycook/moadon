App.UiTreeComponent = Ember.Component.extend({
  
  isTreeComponent: true,

  root: function() {
    var parent = this;
    var last = this;
    
    while(parent)
    {
      last = parent;
      parent = parent.nearestWithProperty('isTreeComponent');
    }
    return last;
  },

  updateSelected: function(){
    var selected = this.get('selected');

    var visitor = function(arr, func)
    {
      for(var i = 0; i < arr.length; i++)
      {
        func(arr[i]);
        visitor(arr[i]['children'], func);
      }
    };

    var setSelected = function(item)
    {
      if(selected && selected.indexOf(item.id) !== -1)
        item.selected = true;
    };

    visitor(this.get('node.children'), setSelected);

  }.on('init'),

  tagName: 'ul',
  classNames: ['tree-branch']
});

App.UiTreeNodeComponent = Ember.Component.extend({
  tagName: 'li',

  classNames: ['tree-node'],

  isEditable: false,

  hasSelections: function(){
    return this.get('node.children').filter(function(item){
      if(item.selected)
        return true;
      return false;
    }).length > 0;
  }.property('node.children', 'node.children.@each'),

  allowEdit: function(){
    return this.nearestWithProperty('editable').get('editable');
  }.property(),

  allowAdd: function() {
    if(this.get('level') < 3 && this.get('allowEdit'))
      return true;
    return false;
  }.property('level', 'allowEdit'),

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

  isSelectable: function(){
    return this.get('level') > 2 && !this.get('allowEdit');
  }.property('level', 'allowEdit'),

  isSelected: function(){
    return this.get('node.selected');
  }.property('node.selected'),

  isExpanded: function(){
    return this.get('node.expended');
  }.property('node.expended'),

  isDeletable: function()
  {
    return !this.get('isBranch') && this.get('allowEdit');
  }.property('isBranch', 'allowEdit'),
  
  actions: {

      select: function(){
        var root = this.nearestWithProperty('isTreeComponent').root();

        var isSelected = !this.get('node.selected');
        this.set('node.selected', isSelected);

        var selected = Em.A(root.get('selected'));
        
        var id = this.get('node.id');
        if(isSelected)
        {
          if(selected.indexOf(id) === -1)
            selected.pushObject(id);
        }else{
          selected.removeObject(id);
        }
      },

      expend: function(){
        var root = this.nearestWithProperty('isTreeComponent').root();
        root.collapseAll();
        var isExpanded = this.get('node.expended');
        this.set('node.expended', !isExpanded);

       // var expended = Em.A(root.get('expended'));

        // var id = this.get('node.id');
        // if(isExpanded)
        // {
        //   if(expended.indexOf(id) === -1)
        //     expended.pushObject(id);
        // }else{
        //   expended.removeObject(id);
        // }
        // if(!expended)
        // {
        //   this.set('isExpanded', !expended);
        //   Ember.run.scheduleOnce('afterRender', this, function(){
        //     this.$('> ul').hide();
        //     this.$('> ul').slideDown();
        //   });
        // }
        // else
        //   Ember.run.scheduleOnce('afterRender', this, function(){
        //     var self = this;
        //     this.$('> ul').slideUp(function(){
        //       self.set('isExpanded', !expended);
        //     });
        //   });
      },

      toggle: function() {
        var isLeaf = this.get('isLeaf');
        var level = this.get('level');

        if(isLeaf && level > 2)
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
