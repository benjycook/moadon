App.UiRteComponent = Em.Component.extend({

	tagName: 'div',
	classNames: ['ui-rte'],

	setHtmlContent: function()
	{
		var content = this.$('.content').html(this.get('html'));
		//this.set('html', content.html());
		//console.log(this.$('.content').html(this.get('html')), this.get('html'));
		console.log(this.$());
		var editor = new MediumEditor(this.$('.content')[0], 
		{
			buttons: [
				'bold',
				'italic',
				'underline', 
				'justifyLeft',
				'justifyFull',
				'justifyCenter',
				'justifyRight',
				'anchor'
			],

			anchorInputPlaceholder: 'הזן את כתובת הקישור',
			staticToolbar: true,
			//stickyToolbar: true,
			toolbarAlign: "left",
			//elementsContainer: this.$()[0]
		});
		var self = this;
		content.on('input', function(){
			self.set('html', content.html());
			console.log(content.html());
		});

		console.log(editor);
	}.on('didInsertElement'),

	keyup: function(evt){
		console.log('keyup', evt);
	}

});