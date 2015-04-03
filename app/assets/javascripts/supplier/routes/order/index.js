App.OrderRoute = App.ProtectedRoute.extend({
	templateName:"order/index",
	
	model:function()
	{
		return {};
	},

	setupController: function(ctrl,model)
	{
		ctrl.set('model',model);
	},
	actions:
	{
		'load':function(id)
		{
			var self = this;
			$.getJSON('order/'+id).then(function(data){
				self.set('controller.model',{});
				var ctrl = Em.ObjectController.create({model:data});
				ctrl.set('target',self);
				self.render('order/realize',{into: 'application',outlet: 'modal',controller:ctrl});
			}).fail(function(data){
				if(data.status == 500)
					var msg = {msg:"אנא נסה שנית או פנה לתמיכה טכנית"};
				else
					var msg = data.responseJSON.msg;
				self.set('controller.model',{msg:msg});
			});
		},
		'close':function(controller)
		{
			controller.set('error',null);
		},
		// 'selectQty':function(item)
		// {
		// 	var left = item.qty-item.realized;
		// 	var options = [];
		// 	options.pushObject({id:left,name:left});
		// 	// for (var i = 1; i <=left ; i++) {
		// 	// 	options.pushObject({id:i,name:i});
		// 	// };
		// 	var ctrl = Em.ObjectController.create({model:{realizedQty:0,options:options,item:item}});
			
		// 	this.render('order/realize',{into: 'application',outlet: 'modal',controller:ctrl});
		// },
		'closeWindow':function()
		{
			this.render('empty',{into: 'application',outlet: 'modal'});
		},
		'realize':function(model,view,controller)
		{
			var form = view.$('form');
			var valid = form.parsley().validate();
			var self = this;
			if(!valid)
				return;
			$('.sendEnter').attr('disabled','disabled');
			$.ajax({
				type: 'POST',
				url: 'realize',
				data: JSON.stringify(model)
			}).then(function(data){
				self.send('closeWindow');
			}).fail(function(data){
				if(data.status == 500)
					var error = "אנא נסה שנית או פנה לתמיכה טכנית";
				else
					var error = data.responseJSON.error;
				controller.set('error',error);
			}).always(function(){
					$('.sendEnter').attr('disabled',false);
				});
		},
	}
});
	