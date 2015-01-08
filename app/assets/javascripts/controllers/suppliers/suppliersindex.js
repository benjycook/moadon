App.SuppliersIndexController = GRID.TableController.extend({

	limit: 10,

	endpoint: 'suppliers',
	collection: 'collection',
	itemController: 'App.SupplierController',

    toolbar: [
    		Em.View.extend({
    			tagName: 'div',
    			classNames: 'panel-label',
    			template: Ember.Handlebars.compile('ספקים'),
    		}),

    		Em.View.extend({
    			tagName: 'div',
    			classNames: ['pull-left', 'actions'],
                templateName: 'suppliers/grid/create'
    		}),

        //GRID.ColumnSelector,
        GRID.Filter
    ],

    columns: [
        GRID.column('num', 				{title: '#',				style: 'min center', display: 'always'                 }),
        GRID.column('name',             {title: 'שם',     style: ''                                                  }),
        GRID.column('contactFullName',           {title: 'איש קשר',     style: ''                                                  }),
        GRID.column('contactEmail',            {title: 'דוא"ל איש קשר',     style: ''                                                  }),
        GRID.column('actions',			{title: 'פעולות',			style: 'min center', templateName: 'suppliers/grid/actions'  })
    ]

});
