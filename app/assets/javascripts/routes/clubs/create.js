App.ClubsEditController = Em.ObjectController.extend({

	
	errorExists:function()
	{
		var error = this.get('error');
		if(error)
			$('.modal').scrollTop(0)
	}.observes('error'),

	uploadUrl:function()
	{
		return this.get('linkId')+"/uploadImage";
	}.property('content'),

	logoUrlChanged:function()
	{
		var src = this.get('logoUrl');
		src = src.slice(src.indexOf(this.get('linkId')));
		src = src.slice(0,src.indexOf('?'));
		this.set('logo',src);
	}.observes('logoUrl'),
});

App.ClubsCreateRoute = App.ClubsEditRoute = App.ProtectedRoute.extend({
	controllerName:'clubsEdit',

	model: function(params)
	{
		if(params.clubs_id)
			return $.getJSON('clubs/'+params.clubs_id);
		var rand = "logo"+(Math.floor((Math.random()*10000000)+1)+"temp"+Math.floor((Math.random()*10000000)+1)+Math.floor((Math.random()*10000000)+1));
		return {linkId:rand};
	},

	setupController: function(ctrl, model)
	{
		if(model.logo == undefined||model.logo=="")
			model.logoUrl = App.defualtImg;
		else
			model.logoUrl =  model.logoUrl+"?"+new Date().getTime();
		ctrl.set('model', model);
	},

	renderTemplate: function()
	{		
		this.render('clubs/index');
		this.render('clubs/modal',{into: 'application',outlet: 'modal'});
	}
});
