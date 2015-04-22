App.ItemController = Em.ObjectController.extend({

	total: function(){
		return this.get('count') * this.get('priceSingle');
	}.property('count', 'priceSingle'),

	totalListPrice: function(){
		return this.get('count') * this.get('listPrice');
	}.property('count', 'listPrice'),

	galleryImages: function(){
		var images = this.get('images');
		var data = [];
		for(var i = 0; i < images.length; i++)
		{
			data.push({
				src: images[i]
			});
		}
		return data;
	}.property('images')

});