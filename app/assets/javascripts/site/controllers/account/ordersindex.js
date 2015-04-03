App.OrdersIndexController = Em.Controller.extend({
	queryParams: {
        
        page: {
            replace: true
        },
    },

    page: 1,
});