App.SupplierController = Em.ObjectController.extend({
	contactFullName:function()
	{
		return this.get('contactFirstName')+" "+this.get('contactLastName');
	}.property('content'),
});