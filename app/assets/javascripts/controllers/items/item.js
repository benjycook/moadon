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
	

	profitPrecentGroup:function()
	{
		var priceGroup     	  = this.get('priceGroup') || 0;
		var netPrice 		  = this.get('netPriceGroup') || 0;
		var vat 			  = App.get('vat');
		if(netPrice==0)
			return "100%";
		if(priceGroup==0)
			return "0%";
		priceGroup = priceGroup/(vat/100+1);
		netPrice    = netPrice/(vat/100+1);
		var profitPrecent   = 100-Math.floor((100/priceGroup)*netPrice);
		if(profitPrecent==Infinity||profitPrecent==-Infinity)
			profitPrecent = 0;
		return profitPrecent+"%";
	}.property('priceGroup','netPriceGroup'),

});