App.SuppliersReportIndexController = Em.ObjectController.extend({

    queryParams: {
        
        startDate: {
            replace: true
        },

        endDate: {
            replace: true
        },
    },

    startDate: moment().format('DD/MM/YYYY'),
    endDate: moment().format('DD/MM/YYYY'),
});