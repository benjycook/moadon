App.UsersIndexController = GRID.TableController.extend({

	limit: 10,

	endpoint: 'users',
	collection: 'collection',
	itemController: 'App.UserController',

    toolbar: [
    		Em.View.extend({
    			tagName: 'div',
    			classNames: 'panel-label',
    			template: Ember.Handlebars.compile('משתמשים'),
    		}),

    		Em.View.extend({
    			tagName: 'div',
    			classNames: ['pull-left', 'actions'],
                templateName: 'users/grid/create'
    		}),

        //GRID.ColumnSelector,
        GRID.Filter
    ],

    columns: [
        GRID.column('num', 				{title: '#',				style: 'min center', display: 'always'                 }),
        GRID.column('fullName',             {title: 'שם מלא',     style: ''                                                  }),
        GRID.column('username', 			{title: 'שם משתמש',		style: ''												   }),
        GRID.column('email',            {title: 'דוא"ל',     style: ''                                                  }),
        GRID.column('actions',			{title: 'פעולות',			style: 'min center', templateName: 'users/grid/actions'  })
    ]

});