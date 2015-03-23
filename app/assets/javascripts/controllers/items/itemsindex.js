App.ItemsIndexController = GRID.TableController.extend({

	limit: 10,

	endpoint: 'items',
	collection: 'collection',
	itemController: 'App.ItemController',

    toolbar: [
    		Em.View.extend({
    			tagName: 'div',
    			classNames: 'panel-label',
    			template: Ember.Handlebars.compile('מוצרים'),
    		}),

    		Em.View.extend({
    			tagName: 'div',
    			classNames: ['pull-left', 'actions'],
                templateName: 'items/grid/create'
    		}),

        //GRID.ColumnSelector,
        GRID.Filter
    ],

    columns: [
        GRID.column('num', 				{title: '#',				style: 'min center', display: 'always'                 }),
        GRID.column('name',             {title: 'שם המוצר',     style: ''                                                  }),
        GRID.column('supplier.name',     {title: 'ספק',     style: ''                                                  }),
        GRID.column('clubPrice', 			{title: 'מחיר קופונופש',		style: ''												   }),
        GRID.column('displayDate',            {title: 'בתוקף עד',     style: ''                                                  }),     
        GRID.column('actions',			{title: 'פעולות',			style: 'min center', templateName: 'items/grid/actions'  })
    ]

});