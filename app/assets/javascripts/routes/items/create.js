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
	}.observes('name','shortDescription','description'),

	profitPrecent:function()
	{
		var clubCommissionPrecent  = this.get('clubCommission');
		var priceSingle     = this.get('priceSingle') || 0;
		var netPrice 		= this.get('netPrice') || 0;
		var vat 			= App.get('vat');
		var creditComission = App.get('creditCommission');
		if(netPrice==0)
			return "100%";
		if(priceSingle==0)
			return "0%";
		priceSingle = priceSingle/(vat/100+1);
		netPrice    = netPrice/(vat/100+1);
		var creditComission = priceSingle*(creditComission/100);
		var clubCommission  = (priceSingle-creditComission)*(clubCommissionPrecent/100);	
		var income 			= priceSingle-creditComission-clubCommission;
		var profitPrecent   = 100-Math.floor((100/income)*netPrice);
		if(profitPrecent==Infinity||profitPrecent==-Infinity)
			profitPrecent = 0;
		return profitPrecent+"%";
	}.property('priceSingle','netPrice'),
	discountPrecent:function()
	{
		var priceSingle     = this.get('priceSingle') || 0;
		var listPrice 		= this.get('listPrice') || 0;
		var discountPrecent = 100-Math.floor(priceSingle/listPrice*100);
		if(isNaN(discountPrecent))
			return "0%";
		return discountPrecent+"%";
	}.property('priceSingle','listPrice'),

	profitPrecentGroup:function()
	{
		//var clubCommissionPrecent  = this.get('clubCommission');
		var priceGroup     	  = this.get('priceGroup') || 0;
		var netPrice 		= this.get('netPrice') || 0;
		var vat 			= App.get('vat');
		var creditComission = App.get('creditCommission');
		if(netPrice==0)
			return "100%";
		if(priceGroup==0)
			return "0%";
		priceGroup = priceGroup/(vat/100+1);
		netPrice    = netPrice/(vat/100+1);
		var creditComission = priceGroup*(creditComission/100);
		//var clubCommission  = (priceGroup-creditComission)*(clubCommissionPrecent/100);	
		var income 			= priceGroup-creditComission;//-clubCommission
		var profitPrecent   = 100-Math.floor((100/income)*netPrice);
		if(profitPrecent==Infinity||profitPrecent==-Infinity)
			profitPrecent = 0;
		return profitPrecent+"%";
	}.property('priceGroup','netPrice'),
	
	discountPrecentGroup:function()
	{
		var priceGroup     = this.get('priceGroup') || 0;
		var listPrice 		= this.get('listPrice') || 0;
		var discountPrecent = 100-Math.floor(priceGroup/listPrice*100);
		if(isNaN(discountPrecent))
			return "0%";
		return discountPrecent+"%";
	}.property('priceGroup','listPrice'),
	
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
