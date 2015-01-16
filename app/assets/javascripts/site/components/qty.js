App.UiQtyComponent = Ember.Component.extend({
  tagName: 'div',
  classNames: ['qty'],

  actions: {
  	up: function(){
  		this.incrementProperty('count');
  	},

  	down: function(){
  		if(this.get('count') > 1)
  			this.decrementProperty('count');
  	}
  }
});