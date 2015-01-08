App.ClubsIndexController = GRID.TableController.extend({

	limit: 10,

	endpoint: 'clubs',
	collection: 'collection',
	itemController: 'App.ClubController',

    toolbar: [
    		Em.View.extend({
    			tagName: 'div',
    			classNames: 'panel-label',
    			template: Ember.Handlebars.compile('מועדונים'),
    		}),

    		Em.View.extend({
    			tagName: 'div',
    			classNames: ['pull-left', 'actions'],
                templateName: 'clubs/grid/create'
    		}),

        //GRID.ColumnSelector,
        GRID.Filter
    ],

    columns: [
        GRID.column('num',              {title: '#',                style: 'min center', display: 'always'                 }),
        GRID.column('name',             {title: 'שם מועדון',     style: ''                                                  }),
        GRID.column('regularDiscount', 			{title: 'הנחה רגילה',		style: ''												   }),
        GRID.column('creditDiscount',            {title: 'הנחת אשראי',     style: ''  ,     }),
        GRID.column('displayTotalDiscount',            {title: 'סך הנחה',     style: ''  ,       }),
        GRID.column('actions',			{title: 'פעולות',			style: 'min center', templateName: 'clubs/grid/actions'  })
    ]

});
