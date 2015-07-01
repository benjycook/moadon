App.OrdersIndexController = GRID.TableController.extend({

	limit: 10,

	endpoint: 'orders',
	collection: 'collection',
	itemController: 'App.OrderController',

    toolbar: [
    		Em.View.extend({
    			tagName: 'div',
    			classNames: 'panel-label',
    			template: Ember.Handlebars.compile('הזמנות'),
    		}),

    		Em.View.extend({
    			tagName: 'div',
    			classNames: ['pull-left', 'actions'],
                templateName: 'orders/grid/create'
    		}),

        //GRID.ColumnSelector,
        GRID.Filter
    ],

    columns: [
        GRID.column('num', 				{title: '#',				style: 'min center', display: 'always'                 }),
        GRID.column('createdAt',             {title: 'תאריך יצירת ההזמנה',     style: ''                                                  }),
        GRID.column('code',             {title: 'קוד הזמנה',     style: ''                                                  }),
        GRID.column('fullName',             {title: 'שם לקוח',     style: ''                                                  }),
        GRID.column('mobile',            {title: 'נייד',     style: ''                                                  }),
        GRID.column('email',            {title: 'דוא"ל',     style: ''                                                  }),
        GRID.column('total',            {title: 'סכום',     style: ''                                                  }),
        GRID.column('clubName',            {title: 'מועדון',     style: ''                                                  }),
        GRID.column('actions',			{title: 'פעולות',			style: 'min center', templateName: 'orders/grid/actions'  })
    ]

});