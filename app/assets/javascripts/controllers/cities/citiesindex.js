App.CitiesIndexController = GRID.TableController.extend({

	limit: 10,

	endpoint: 'cities',
	collection: 'collection',
	itemController: 'App.CityController',

    toolbar: [
    		Em.View.extend({
    			tagName: 'div',
    			classNames: 'panel-label',
    			template: Ember.Handlebars.compile('ישובים'),
    		}),

    		Em.View.extend({
    			tagName: 'div',
    			classNames: ['pull-left', 'actions'],
                templateName: 'cities/grid/create'
    		}),

        //GRID.ColumnSelector,
        GRID.Filter
    ],

    columns: [
        GRID.column('num', 				{title: '#',				style: 'min center', display: 'always'                 }),
        GRID.column('name',             {title: 'שם',     style: ''                                                  }),
        GRID.column('actions',			{title: 'פעולות',			style: 'min center', templateName: 'cities/grid/actions'  })
    ]

});