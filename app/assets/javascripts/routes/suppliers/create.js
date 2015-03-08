App.SuppliersEditController = Em.ObjectController.extend({
	
	disableTabs:function()
	{
		if(this.get('supplier.id'))
			return false;
		return true;
	}.property('supplier.id'),

	itemsNum:function()
	{
		var items = this.get('items');
		items.forEach(function(item){
			item.num = items.indexOf(item)+1;
		});
	}.observes('items.length'),

	messagesReset:function(sender,field)
	{
		if(this.get(""+field)!=null)
		{
			if(field=='error')
				this.set('success',null);
			else
				this.set('error',null);
		}
	}.observes('success','error'),

	lengthTest:function(obj,key)
	{
		var description = this.get('sitedetails.description');
		if(description&&description.length>49)
			this.set('sitedetails.description',description.substr(0,49));
	}.observes('sitedetails.description'),

	

	// sortedLevel1:function()
	// {
	// 	return App.get('regions').filterBy('parent_id',0);
	// }.property('content'),

	// sortedLevel2:function()
	// {
	// 	var mainRegion = this.get('mainRegion');
	// 	var secondaryRegion = this.get('secondaryRegion');
	// 	secondaryRegion = App.get('regions').findBy('id',secondaryRegion);
	// 	if(secondaryRegion&&mainRegion!=secondaryRegion.parent_id)
	// 		this.set('secondaryRegion',0);
	// 	if(mainRegion)
	// 		return App.get('regions').filterBy('parent_id',mainRegion);
	// 	return [];
	// }.property('mainRegion'),

	// sortedLevel3:function()
	// {
	// 	var secondaryRegion = this.get('secondaryRegion');
	// 	if(secondaryRegion)
	// 		return App.get('regions').filterBy('parent_id',secondaryRegion);
	// 	this.set('sitedetails.regions_id',0);
	// 	return [];
	// }.property('secondaryRegion'),

	

});

App.SuppliersCreateRoute = App.SuppliersEditRoute = App.ProtectedRoute.extend({
	controllerName:'suppliersEdit',
	
	templateName: 'suppliers/create',
	
	model: function(params)
	{
		if(params.suppliers_id)
			return $.getJSON('suppliers/'+params.suppliers_id);
		return $.getJSON('suppliers/create');
	},

	setupController: function(ctrl, model)
	{
		model.categories = {
			name: 'root element',
			children: model.categories
		};

		ctrl.set('model',model);
	},


	actions:
	{

		'editItem':function(id,items)
		{
			if(id)
			{
				var self =this;
					new Ember.RSVP.Promise(function(resolve) { 
								$.getJSON('items/'+id).then(function(data){
									var ctrl = App.ItemsEditController.create({model:data});
									ctrl.set('target',self);
									self.render('suppliers/tabs/partials/item', {into: 'application',outlet: 'modal',controller:ctrl});
									resolve('resovled');
							});
					});
			}
			else
			{
				var self = this;
				$.getJSON('items/create').then(function(data){
					var ctrl = App.ItemsEditController.create({model:data});
					ctrl.set('target',self);
					self.render('suppliers/tabs/partials/item', {into: 'application',outlet: 'modal',controller:ctrl});
				});
			}
			
		},
		'closeWindow': function() 
	    {
	      this.render('empty', {into: 'application',outlet: 'modal'});
	    },

	    'saveItem':function(model,view,controller)
	    {
			var items = this.controllerFor('suppliersEdit').get('items');
			model.suppliers_id = this.controllerFor('suppliersEdit').get('supplier.id');
			var form = view.$('form');
			var valid = form.parsley().validate();
			if(!valid)
			{
				var tabName = form.find('.parsley-error').first().closest('.tab-pane').attr('id');
	            Em.$('[href=#'+tabName+']').click();
	            return;
			}
			var self = this;
			var type = "POST";
			var url  = 'items';
			if(model.id)
			{
				url  = 'items/'+model.id;
				type = "PUT";
			}
			this.send('close',controller);
			$.ajax({
				type: type,
				url: url,
				data: JSON.stringify(model)
			}).then(function(data){
				var item = items.findBy('id',data.id);
				if(item == undefined)
					items.pushObject(data);
				else
					item.galleries = data.galleries;
				controller.set('error',null);
				controller.set('success',"נשמר בהצלחה.");
				self.send('closeWindow');
			}).fail(function(data){
				controller.set('success',null);
				if(data.status == 500)
					var error = "אנא נסה שנית או פנה לתמיכה טכנית";
				else
					var error = data.responseJSON.error;
					controller.set('error',error);
			});
	    },

	    'saveSiteDetails':function(model,view,controller)
	    {
	    	console.log(view);
			var form = view.$('form');
			var valid = form.parsley().validate();
			model.suppliers_id = this.controllerFor('suppliersEdit').get('supplier.id');
			if(!valid)
				return;
			var self = this;
			var type = "POST";
			var url  = 'sitedetails';
			if(model.id)
			{
				url  = 'sitedetails/'+model.id;
				type = "PUT";
			}
			this.send('close',controller);
			$.ajax({
				type: type,
				url: url,
				data: JSON.stringify(model)
			}).then(function(data){
				controller.set('sitedetails',data);
				controller.set('error',null);
				controller.set('success',"נשמר בהצלחה.");
				form.parsley().reset();
			}).fail(function(data){
				controller.set('success',null);
				if(data.status == 500)
					var error = "אנא נסה שנית או פנה לתמיכה טכנית";
				else
					var error = data.responseJSON.error;
					controller.set('error',error);
			});
	    },

	    'saveSupplier': function(model,view,controller)
		{
			var self = this;
			var form = view.$('form');
			var valid = form.parsley().validate();
			if(!valid)
	            return;	
			var url = "suppliers";
			var type = "POST";
			if(model.id)
			{
				url  = "suppliers/"+model.id;
				type = "PUT"; 
			}
			this.send('close',controller);
			$.ajax({
				type: type,
				url: url,
				data: JSON.stringify(model)
			}).then(function(data){
				controller.set('supplier',data.supplier);
				controller.set('error',null);
				controller.set('success',"נשמר בהצלחה.");
				form.parsley().reset();
				if(!model.id)
					controller.set('sitedetails',data.siteDetails);
			}).fail(function(data){
				controller.set('success',null);
				if(data.status == 500)
					var error = "אנא נסה שנית או פנה לתמיכה טכנית";
				else
					var error = data.responseJSON.error;
					controller.set('error',error);
			});
		},
		'saveMiniSite':function(model,view,controller)
		{
			var self = this;
			var form = view.$('form');
			var valid = form.parsley().validate();
			if(!valid)
	            return;
			url  = "sitedetails/minisite/"+model.id;
			type = "POST"; 
			this.send('close',controller);
			$.ajax({
				type: type,
				url: url,
				data: JSON.stringify(model)
			}).then(function(data){
				controller.set('error',null);
				controller.set('success',"נשמר בהצלחה.");
				form.parsley().reset();
			}).fail(function(data){
				controller.set('success',null);
				if(data.status == 500)
					var error = "אנא נסה שנית או פנה לתמיכה טכנית";
				else
					var error = data.responseJSON.error;
					controller.set('error',error);
			});
		},

		'confirm':function(id)
		{
			var controller = this.controllerFor('suppliersEdit');
			controller.set('deleteID',id);
			this.render('suppliers/deleteConfirm', {
				controller:controller,
			  into: 'application',
			  outlet: 'modal',
			});
		},
		'delete':function(controller)
		{
			var self = this;
			$.ajax({
				type: "DELETE",
				url: 'items/'+controller.get('deleteID'),
			}).then(function(data){
				var items = controller.get('items');
				var item = items.findBy('id',controller.get('deleteID'));
				items.removeObject(item);
				self.render('empty', {into: 'application',outlet: 'modal'});
				controller.set('deleteID',null);
				controller.set('error',null);
			}).fail(function(data){
				if(data.status == 500)
					var error = "אנא נסה שנית או פנה לתמיכה טכנית";
				else
					var error = data.responseJSON.error;
					controller.set('error',error);
			});
		},
		'cancel':function(controller)
		{
			controller.set('deleteID',null);
			controller.set('error',null);
			this.render('empty', {into: 'application',outlet: 'modal'});
		},
	}
});
