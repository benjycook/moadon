App.SupplierController = Em.ObjectController.extend({
	
	needs: ['application', 'search'],

	application: Ember.computed.alias("controllers.application"),
	options: Ember.computed.alias("application.options"),
	workingHoursLines: function(){
		return this.get('workingHours').split("\n").join('<br />');
	}.property('workingHours'),

	ageDevisionLines: function(){
		return this.get('ageDevision').split("\n").join('<br />');
	}.property('ageDevision'),

	city: function()
	{
		var city = this.get('options.cities').findBy('id', this.get('cities_id'));
		if(city)
			return city;
		return false;
	}.property('cities_id'),

	regionName: function()
	{
		var region = this.get('application.flatRegions').findBy('id', this.get('city.regions_id'));
		if(region)
			return region.name;
		return 'לא ידוע';
	}.property('regions_id', 'city'),

	cityName: function()
	{
		var city = this.get('options.cities').findBy('id', this.get('cities_id'));
		if(city)
			return city.name;
		return 'לא ידוע';
	}.property('cities_id'),

	categoryName: function()
	{
		var category = this.get('application.categories.children').findBy('id', this.get('mainCategory'));
		if(category)
			return category.name;

		return false;
	}.property('mainCategory'),

	image: function(){
		return this.get('images.0');
	}.property('imagea'),

	galllery: function(){
		return this.get('images.0');
	}.property('images'),

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
	}.property('images'),

	phoneNumbers: function(){
		var parts = [];
		var phone1 = this.get('phone1');
		var phone2 = this.get('phone2');
		var phone3 = this.get('phone3');

		if(phone2)
			parts.push(phone2);
		if(phone1)
			parts.push(phone1);
		if(phone3)
			parts.push(phone3);

		return parts.join(', ');
	}.property('phone1', 'phone2', 'phone3')

});