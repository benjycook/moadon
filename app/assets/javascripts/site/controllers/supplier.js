App.SupplierController = Em.ObjectController.extend({
	
	needs: ['application'],

	application: Ember.computed.alias("controllers.application"),

	workingHoursLines: function(){
		return this.get('workingHours').split("\n").join('<br />');
	}.property('workingHours'),

	ageDevisionLines: function(){
		return this.get('ageDevision').split("\n").join('<br />');
	}.property('ageDevision'),

	city: function()
	{
		var city = this.get('application.cities').findBy('id', this.get('cities_id'));
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
		var city = this.get('application.cities').findBy('id', this.get('cities_id'));
		if(city)
			return city.name;
		return 'לא ידוע';
	}.property('cities_id'),

	image: function(){
		return this.get('images.0');
	}.property('imagea'),

	galllery: function(){
		return this.get('images.0');
	}.property('images')

});