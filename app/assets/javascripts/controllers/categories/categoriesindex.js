App.CategoriesIndexController = GRID.TableController.extend({

	limit: 10,

	endpoint: 'categories',
	collection: 'collection',
	itemController: 'App.CategoryController',

    toolbar: [
    		Em.View.extend({
    			tagName: 'div',
    			classNames: 'panel-label',
    			template: Ember.Handlebars.compile('קטגוריות'),
    		}),

    		Em.View.extend({
    			tagName: 'div',
    			classNames: ['pull-left', 'actions'],
                templateName: 'categories/grid/create'
    		}),

        //GRID.ColumnSelector,
        GRID.Filter
    ],

    columns: [
        GRID.column('num', 				{title: '#',				style: 'min center', display: 'always'                 }),
        GRID.column('name',             {title: 'שם',     style: ''                                                  }),
        GRID.column('actions',			{title: 'פעולות',			style: 'min center', templateName: 'categories/grid/actions'  })
    ]

});