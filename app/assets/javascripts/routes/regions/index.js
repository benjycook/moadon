App.RegionsRoute = App.ProtectedRoute.extend({});

App.RegionsIndexRoute = App.ProtectedRoute.extend({

	model: function()
	{
		return $.getJSON('regions');
	},

	setupController: function(ctrl, model)
	{
		ctrl.set('categories', {
			name: 'root element',
			children: model
		});
	},

	actions: 
	{
		addItem: function(){
			var node = {
				name: 'אזור ראשי חדש',
				children: []
			};

			this.controller.get('categories.children').pushObject(node);
		},

		save: function()
		{
			var treeData = JSON.stringify(this.controller.get('categories.children'));
			var self = this;
			$.ajax({
				type: 'POST',
				url: 'regions',
				data: treeData
			}).then(function(data){
				App.set('regions',data.regions);
				self.controller.set('success','השינויים נשמרו בהצלחה');
				self.controller.set('categories.children',data.tree);
				self.render('regions/notice',{into:'application',outlet:'modal'});
			}).fail(function(data){
				if(data.status == 500)
					var error = "אנא נסה שנית או פנה לתמיכה טכנית";
				else
				{
					var error = data.responseJSON.error;
					self.controller.set('categories.children',data.responseJSON.tree);
				}
				self.controller.set('error',error);
				self.render('regions/notice',{into:'application',outlet:'modal'});
			});
		},
		'back':function()
		{
			this.controller.set('error',null);
			this.controller.set('success',null);
			this.render('empty',{into:'application',outlet:'modal'});
		}
	}

});