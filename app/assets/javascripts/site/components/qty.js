App.UiQtyComponent = Ember.Component.extend({
  tagName: 'div',
  classNames: ['qty'],

  //count: 0,

  actions: {
  	up: function(){
      this.set('count', this.get('count') + 1);
  	},

  	down: function(){
  		if(this.get('count') > 1)
  			this.set('count', this.get('count') - 1);
  	}
  }
});