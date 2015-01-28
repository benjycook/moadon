App.UiRteComponent = Em.Component.extend({

	tagName: 'div',

	setHtmlContent: function()
	{
		var content = this.$('.content').html(this.get('html'));
		//this.set('html', content.html());
		//console.log(this.$('.content').html(this.get('html')), this.get('html'));
		var editor = new MediumEditor(this.$('.content')[0], {
			buttons: ['bold', 'italic', 'anchor'],
			anchorInputPlaceholder: 'הזן את כתובת הקישור'
		});
		var self = this;
		content.on('input', function(){
			self.set('html', content.html());
			console.log(content.html());
		});

		console.log(editor);
	}.on('didInsertElement')

});