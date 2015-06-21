App.ItemController = Em.ObjectController.extend({

	
	unitPrice: function(){
		return Math.round(this.get('priceSingle'));
	}.property('priceSingle'),

	totalPrice: function(){
		return Math.round(this.get('count') * this.get('priceSingle'));
	}.property('count', 'priceSingle'),

	totalListPrice: function(){
		return Math.round(this.get('count') * this.get('listPrice'));
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