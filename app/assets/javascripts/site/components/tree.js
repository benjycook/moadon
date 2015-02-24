App.UiTreeLinkComponent = Em.LinkView.extend({
  params: ['search'],

  computeQueryParamsObject: function(){
    var type = this.get('base');
    var item = this.get('item');
    var values = {};
    values[type] = item.id;
    this.queryParamsObject = {};
    this.queryParamsObject['values'] = values;
  }.on('init'),

  _invoke: function(evt){
    var queryParams =  this.get('loadedParams.queryParams');
    queryParams[this.nearestWithProperty('subreset').get('subreset')] = [];
    if(this.get('active'))
    {
     // var queryParams =  this.get('loadedParams.queryParams');
      var type = this.get('base');
      var item = this.get('item');

      queryParams[type] = item.parent_id;



      // var values = {};
      // values[type] = 0;
      // this.queryParamsObject = {};
      // this.queryParamsObject['values'] = values;
      // console.log('active', this.get('item'), this.get('loadedParams'));
    }
    this._super(evt);
  },

});

App.UiTreeNodeComponent = Em.Component.extend({
  tagName: 'li',
  classNames: ['tree-node'],
  classNameBindings: ['active', 'leaf', 'selected'],

  isSelected: false,

  spin: function(){
    if(this.get('node.id') == this.get('loading'))
      return true;
    return false;
  }.property('loading'),

  parent: function(){
    return this.nearestWithProperty('value');
  }.property().volatile(),

  selected: function(){
    if(this.get('parent.value.length') && this.get('parent.value').indexOf(this.get('node.id')) != -1)
      return true;
    return false;
  }.property('parent.value'),

  leaf: function(){
    return this.get('level') > 2;
    //return this.get('selectable');
  }.property('level'),

  isExpended: function(){
    if(this.get('node.id') == this.get('main'))
      return true;
    var children = this.get('node.children');
    var walk = function(children, search)
    {
      for(var i = 0; i < children.length; i++)
      {
        if ((children[i].id == search) || walk(children[i].children, search))
          return true;
      }
      return false;
    }
    return walk(children, this.get('main'));
  }.property('main'),

  active: function() {
    return this.get('childViews').anyBy('active');
  }.property('childViews.@each.active'),

  selectable: function(){
    //if(this.get('level') > 2)
    if(!this.get('node.children.length') && this.get('level') > 2)
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
      var id = this.get('node.id');
      var arr = Em.A(this.get('parent.value'));
 
      if(arr.indexOf(id) === -1)
      {  
        arr.pushObject(id);
      }
      else
      {
        arr.removeObject(id);
      }
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