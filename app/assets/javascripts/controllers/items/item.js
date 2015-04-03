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

	// lengthTest:function(obj,key)
	// {
	// 	var name = this.get('name');
	// 	if(name&&name.length>49)
	// 		this.set('name',name.substr(0,49));
	// }.observes('name'),

});