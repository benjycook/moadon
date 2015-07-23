App.ReportController = Em.ObjectController.extend({
	displayRealizedPayedTotal:function()
    {
    	var realizedPayedTotal = this.get('realizedPayedTotal');
    	var priceSingleRealizedTotal = this.get('priceSingleRealizedTotal');
    	return  realizedPayedTotal+" ("+priceSingleRealizedTotal+")";
    }.property('realizedPayedTotal'),
    displayOrdersPayedTotal:function()
    {
    	var ordersPayedTotal = this.get('ordersPayedTotal');
    	var priceSingleTotal = this.get('priceSingleTotal');
    	return  ordersPayedTotal+" ("+priceSingleTotal+")";
    }.property('ordersPayedTotal'),
     displayRealizations:function()
    {
        var realizations = this.get('realizations');
        var realizedNum = this.get('realizedNum');
        return  realizations+" ("+realizedNum+")";
    }.property('realizations'),
     displayRealizedPayedTotal:function()
    {
        var realizedPayedTotal = this.get('realizedPayedTotal');
        var priceSingleRealizedTotal = this.get('priceSingleRealizedTotal');
        return  realizedPayedTotal+" ("+priceSingleRealizedTotal+")";
    }.property('realizedPayedTotal'),
     displayCanceledTotal:function()
    {
        var ordersCanceledTotal = this.get('ordersCanceledTotal');
        var ordersCanceledTotalSingle = this.get('ordersCanceledTotalSingle');
        return  ordersCanceledTotal+" ("+ordersCanceledTotalSingle+")";
    }.property('ordersCanceledTotal'),
});