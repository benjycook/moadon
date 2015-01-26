App.UiTreeNodeComponent = Em.Component.extend({
  tagName: 'li',
  className: 'tree-node',
  classNameBindings: ['active'],
  active: function() {
    return this.get('childViews').anyBy('active');
  }.property('childViews.@each.active')
});

App.UiTreeBranchComponent = Em.Component.extend({
  tagName: 'ul',
  classNames: ['tree-branch'],
  classNameBindings: ['active'],
  active: function() {
    return this.get('childViews').anyBy('active');
  }.property('childViews.@each.active')  
});