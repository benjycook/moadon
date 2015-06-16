App.ContactRoute = App.ProtectedRoute.extend({

	model: function(params)
	{
		return {};
	},

	renderTemplate: function()
	{
		this.render('contact', {into: 'application'});
		this.render('search/sidebar', {
			into: 'application', 
			outlet: 'search',
			controller: this.controllerFor('search')
		});
	},

	actions: {
		send: function(){
			var cdata = this.get('controller').get('model');
			var _this = this;

			var model = {
				 header:"הטופס נשלח בהצלחה",
				 msg:"פנייתך התקבלה במערכת. נציגנו יצרו איתך קשר בהקדם."
			};

			$.ajax({
				type: 'POST',
				url: 'contact',
				data: JSON.stringify(cdata)
			}).always(function(){
				var ctrl = Em.Controller.create({model:model});
				ctrl.set('target',_this);
				_this.render('account/notice', {
					into: 'application',
					outlet: 'lightbox',
					controller: ctrl
				});	
				for(var i in cdata)
				{
					Em.set(cdata, i, '');
				}
			});

		}
	}

});