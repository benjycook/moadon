App.ClubController = Em.ObjectController.extend({
	displayTotalDiscount:function()
	{
		return (this.get('regularDiscount')+this.get('creditDiscount'));
	}.property('content'),

	
});