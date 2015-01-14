App.SupplierController = Em.ObjectController.extend({
	
	needs: ['application'],

	application: Ember.computed.alias("controllers.application"),

	regionName: function()
	{
		var region = this.get('application.flatRegions').findBy('id', this.get('regions_id'));
		return region.name;
	}.property('regions_id')
});