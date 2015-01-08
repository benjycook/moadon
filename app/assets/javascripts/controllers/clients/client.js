App.ClientController = Em.ObjectController.extend({
	name:function()
	{
		return this.get('firstName')+" "+this.get('lastName');
	}.property('content'),
});