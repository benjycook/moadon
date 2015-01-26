App.UiTreeNodeComponent = Em.Component.extend({
  tagName: 'li',
  classNames: ['tree-node'],
  classNameBindings: ['active', 'leaf', 'selected'],

  isSelected: false,

  selected: function(){
    return this.get('isSelected') === true;
  }.property('isSelected'),

  leaf: function(){
    return this.get('level') > 2;
  }.property('level'),

  active: function() {
    return this.get('childViews').anyBy('active');
  }.property('childViews.@each.active'),

  //parent: this.nearestWithProperty()
  selectable: function(){
    if(this.get('level') > 2)
      return true;
    return false;
  }.property('level'),

  type: function(){
    var type = this.nearestWithProperty('type').get('type');
    return type;
  }.property(),

  isCategory: function(){
    if(this.get('type') == 'category')
      return true;
    return false;
  }.property('type'),

  isRegion: function(){
    if(this.get('type') == 'region')
      return true;
    return false;
  }.property('type'),

  level: function() {
    var parent = this.nearestWithProperty('isTreeComponent');
    var level = 0;
    while(parent)
    {
      level++;
      parent = parent.nearestWithProperty('isTreeComponent');
    }
    return level;
  }.property().volatile(),

  actions: {
    'toggle': function(){
      this.set('isSelected', !this.get('isSelected'));
    }
  }
});

App.UiTreeBranchComponent = Em.Component.extend({

  isTreeComponent: true,

  tagName: 'ul',
  classNames: ['tree-branch'],
  classNameBindings: ['active'],
  active: function() {
    return this.get('childViews').anyBy('active');
  }.property('childViews.@each.active')  
});