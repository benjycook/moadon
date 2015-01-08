App.UserController = Em.ObjectController.extend({
	fullName:function()
	{
		return this.get('firstName')+" "+this.get('lastName');
	}.property('content'),
});