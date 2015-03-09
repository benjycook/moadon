App.SupplierController = Em.ObjectController.extend({
	contactFullName:function()
	{
		return this.get('contact.firstName')+" "+this.get('contact.lastName');
	}.property('content'),
});