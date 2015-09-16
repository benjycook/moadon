App.ClientsIndexController = GRID.TableController.extend({

	limit: 10,

	endpoint: 'clients',
	collection: 'collection',
	itemController: 'App.ClientController',

    toolbar: [
    		Em.View.extend({
    			tagName: 'div',
    			classNames: 'panel-label',
    			template: Ember.Handlebars.compile('לקוחות'),
    		}),

    		Em.View.extend({
    			tagName: 'div',
    			classNames: ['pull-left', 'actions'],
                templateName: 'clients/grid/create'
    		}),

        //GRID.ColumnSelector,
        GRID.Filter
    ],

    columns: [
        GRID.column('num', 				{title: '#',				style: 'min center ', display: 'always'  ,isSortable: false     }),
        GRID.column('name', 			{title: 'שם הלקוח',		style: ''	}),
        GRID.column('email',            {title: 'דוא"ל',   style: ''        }),
          GRID.column('password',            {title: 'סיסמא',   style: ''        }),
        GRID.column('mobile',           {title: 'נייד',       style: ''            }),
         GRID.column('taxId',           {title: 'ת.ז',       style: ''            }),
        GRID.column('actions',			{title: 'פעולות',			style: 'min center ', templateName: 'clients/grid/actions' ,isSortable: false  })
    ]

});