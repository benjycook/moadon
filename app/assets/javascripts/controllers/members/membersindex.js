App.MembersIndexController = GRID.TableController.extend({

	limit: 10,

	endpoint: 'members',
	collection: 'collection',
	itemController: 'App.MemberController',

    toolbar: [
    		Em.View.extend({
    			tagName: 'div',
    			classNames: 'panel-label',
    			template: Ember.Handlebars.compile('חברי מועדון'),
    		}),

    		Em.View.extend({
    			tagName: 'div',
    			classNames: ['pull-left', 'actions'],
                templateName: 'members/grid/create'
    		}),

        //GRID.ColumnSelector,
        GRID.Filter
    ],

    columns: [
        GRID.column('num', 				{title: '#',				style: 'min center', display: 'always'                 }),
        GRID.column('fullName',             {title: 'שם מלא',     style: ''                                                  }),
        GRID.column('club.name',            {title: 'מועדון',     style: ''                                                  }),
        GRID.column('employeeId',            {title: 'מספר מזהה',     style: ''                                                  }),
        GRID.column('idNumber',            {title: 'ת.ז',     style: ''                                                  }),
        GRID.column('phone', 			{title: 'טלפון',		style: ''												   }),
        GRID.column('email',            {title: 'דוא"ל',     style: ''                                                  }),
        GRID.column('actions',			{title: 'פעולות',			style: 'min center', templateName: 'members/grid/actions'  })
    ],
});

    
