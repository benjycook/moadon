App.ItemController = Em.ObjectController.extend({
	displayDate:function()
	{
		var date = this.get('expirationDate');
		return ((date.split('-')).reverse()).join('/');
	}.property('expirationDate'),

	state:function()
	{
		var states_id = this.get('states_id');
		return App.states.findBy('id',states_id).name;
	}.property('states_id'),

	single:function()
	{
		var itemtypes_id = this.get('itemtypes_id');
		if(itemtypes_id&&itemtypes_id!=2)
			return true;
		return false;
	}.property('itemtypes_id'),

	group:function()
	{
		var itemtypes_id = this.get('itemtypes_id');
		if(itemtypes_id&&itemtypes_id!=1)
			return true;
		return false;
	}.property('itemtypes_id'),

	openClose:function()
	{
		var rowState = this.get('rowState');
		if(rowState==true)
			return 'display:block;';
		return 'display:none';
	}.property('rowState'),

	actions:
	{
		'openDetailes':function()
		{
			var rowState = this.get('rowState');
			if(rowState)
				this.set('rowState',false);
			else
				this.set('rowState',true);
		},

	}
});