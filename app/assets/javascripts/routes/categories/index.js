App.CategoriesRoute = App.ProtectedRoute.extend({});

App.CategoriesIndexRoute = App.ProtectedRoute.extend({

	model: function()
	{
		return $.getJSON('categories');
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
				name: 'קטגוריה ראשית חדשה',
				children: []
			};
			this.controller.get('categories.children').pushObject(node);
		},

		save: function()
		{
			var self = this;
			var treeData = JSON.stringify(this.controller.get('categories.children'));
			$.ajax({
				type: 'POST',
				url: 'categories',
				data: treeData
			}).then(function(data){
				App.set('categories',data.categories);
				self.controller.set('success','השינויים נשמרו בהצלחה');
				self.controller.set('categories.children',data.tree);
				self.render('categories/notice',{into:'application',outlet:'modal'});
			}).fail(function(data){
				if(data.status == 500)
					var error = "אנא נסה שנית או פנה לתמיכה טכנית";
				else
				{
					var error = data.responseJSON.error;
					self.controller.set('categories.children',data.responseJSON.tree);
				}
				self.controller.set('error',error);
				self.render('categories/notice',{into:'application',outlet:'modal'});
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