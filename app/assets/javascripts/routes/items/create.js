App.ItemsEditController = Em.ObjectController.extend({
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

	lengthTest:function(obj,key)
	{
		var test = this.get(key);
		if(key=='description'&&test.length>255)
			test = test.substr(0,255);
		if(key=='name'&&test.length>50)
			test = test.substr(0,50);
		if(key=='shortDescription'&&test.length>14)
			test = test.substr(0,14);
		this.set(key,test);
		this.set('changed',true);
	}.observes('name','shortDescription','description')
});


App.ItemsCreateRoute = App.ItemsEditRoute = App.ProtectedRoute.extend({
	controllerName:'itemsEdit',

	model: function(params)
	{
		if(params.items_id)
			return $.getJSON('items/'+params.items_id);
		return $.getJSON("items/create");
	},

	setupController: function(ctrl, model)
	{
		ctrl.set('model', model);
	},


	renderTemplate: function()
	{		
		this.render('items/index');
		this.render('items/modal', {into: 'application',outlet: 'modal'});
		
	}
});
