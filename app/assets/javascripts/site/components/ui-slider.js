App.UiSliderComponent = Em.Component.extend({

	classNames: 'slider',
	
	currentSlide: 1,

	slides: function(){
		return this.images.length;
	}.property('images'),

	links: function(){
		var count = this.get('slides');
		var currentSlide = this.get('currentSlide');
		var nav = [];
		for(var i = 1; i <= count; i++)
		{	
			nav.push({
				index: i,
				active: currentSlide == i
			});
		}
		return nav;
	}.property('slides', 'currentSlide'),

	// click: function(){
	// 	if(this.get('currentSlide') >= this.get('slides'))
	// 		this.setSlide(1);
	// 	else
	// 		this.setSlide(this.get('currentSlide') + 1);
	// },

	setSlide: function(index)
	{
		this.set('currentSlide', index);
		var slides = this.$('ul.slides');
		var width = slides.find('li').first().width();
		var right = - (width * (index - 1));
		slides.css('right', right + 'px');
	},

	actions: {
		'setSlide': function(index){
			this.setSlide(index);
		}
	}

});