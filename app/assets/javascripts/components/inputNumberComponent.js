App.InputNumberComponent = Em.TextField.extend({
	init: function(){
		this._super();
	},
	attributeBindings: ['value', 'type', 'class', 'required', 'parsley-type', 'parsley-min', 'parsley-max'],
	tagName: 'input',

	update: function(){
		var val = this.$().val();
		//test for 2 dots and replace if neccesary
		if(val.split('.').length>2)
			val = replaceAt(val,val.lastIndexOf('.'),' ');
		val = val.replace(/[^\d|\.]/g,'');//remove any char thats not (digit Or dote).
		this.set('value', val);
	}.observes('value'),
})