App.UiSliderComponent = Em.Component.extend({
		classNames:['ui-slider'],
		index  : -1,
		slides : 0,
		data: [],

		setupControoler: function(){
			var lis = this.get('data.length');
			var firstSlide = this.$('.slides ul.images li')[0].offsetWidth;
			var totalWidth = (this.$('.slides ul.images li')[0].offsetWidth) * lis;
			totalWidth = parseInt(totalWidth * 100) / 100;
			var slideWidth = this.$('.slides ul.images')[0].offsetWidth;
			var totalSlides = Math.ceil( parseInt((totalWidth / slideWidth) * 100) / 100 );

			var properties = {
				index : 0,
				slides : totalSlides,
				width: slideWidth,
			};

			this.setProperties(properties);

		}.on('didInsertElement'),

		computPos: function (index){
			var newPos = index * this.get('width');
			return -newPos;
		},

		navigation: function(){
			var length = this.get('slides');
			var dots = [];

			for (var i = 0 ; i < length ; i++){
				var obj = {
					index: i ,
					active: i === this.get('index') ,
				};
				dots.push(obj);
			}

			return dots;

		}.property( "slides" ,"index"),

		controls: function(){
			if(this.get('slides') > 1)
				return true;
			return false;
		}.property('slides'),

	actions : {
		setSlide: function(index)
		{
			var slides =  this.get('slides');

			if( index >= slides ){
				index = 0;
			}


			if(index < 0){
				index= slides - 1;
			}

			var newPos =  this.computPos(index);
			this.$('ul.images').css('right' , newPos + 'px');
			this.set('index', index);
		},

		next: function() {
			var next = this.get('index') + 1;
			this.send('setSlide' , next);
		},

		prev: function(){
			var prev = this.get('index') - 1;
			this.send('setSlide' , prev);
		}
	}
});

