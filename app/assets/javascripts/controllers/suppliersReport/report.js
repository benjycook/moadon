App.ReportController = Em.ObjectController.extend({
	displayRealizedPayedTotal:function()
    {
    	var realizedPayedTotal = this.get('realizedPayedTotal');
    	if(realizedPayedTotal)
        {
            var priceSingleRealizedTotal = this.get('priceSingleRealizedTotal');
            return  realizedPayedTotal+" ("+priceSingleRealizedTotal+")";
        }
        return "0 (0)";
    }.property('realizedPayedTotal'),
    displayOrdersPayedTotal:function()
    {
    	var ordersPayedTotal = this.get('ordersPayedTotal');
    	if(ordersPayedTotal)
        {
            var priceSingleTotal = this.get('priceSingleTotal');
            return  ordersPayedTotal+" ("+priceSingleTotal+")";
        }
        return "0 (0)";
    }.property('ordersPayedTotal'),
     displayRealizations:function()
    {
        var realizations = this.get('realizations');
        if(realizations)
        {
            var realizedNum = this.get('realizedNum');
            return  realizations+" ("+realizedNum+")";
        }
        return "0 (0)";
    }.property('realizations'),
     displayRealizedPayedTotal:function()
    {
        var realizedPayedTotal = this.get('realizedPayedTotal');
        if(realizedPayedTotal)
        {
          var priceSingleRealizedTotal = this.get('priceSingleRealizedTotal');
          return  realizedPayedTotal+" ("+priceSingleRealizedTotal+")";
        }
        return "0 (0)";
    }.property('realizedPayedTotal'),
     displayCanceledTotal:function()
    {
        var ordersCanceledTotal = this.get('ordersCanceledTotal');
        if(ordersCanceledTotal)
        {
            var ordersCanceledTotalSingle = this.get('ordersCanceledTotalSingle');
            return  ordersCanceledTotal+" ("+ordersCanceledTotalSingle+")";
        }
        return "0 (0)";
    }.property('ordersCanceledTotal'),

    displayOrdersCanceled:function()
    {
        var ordersCanceled = this.get('ordersCanceled');
        if(ordersCanceled)
        {
            var ordersCanceledQty = this.get('ordersCanceledQty');
            return  ordersCanceled+" ("+ordersCanceledQty+")";
        }
        return "0 (0)";
    }.property('ordersCanceled'),
    displayOrdersNum:function()
    {
        var ordersNum = this.get('ordersNum');
        if(ordersNum)
        {
            var ordersTotalQty = this.get('ordersTotalQty');
            return  ordersNum+" ("+ordersTotalQty+")";
        }
        return "0 (0)";
    }.property('ordersNum'),
    
});