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
        GRID.column('num',              {title: '#',                style: 'min center', display: 'always' ,isSortable: false    }),
        GRID.column('name',             {title: 'שם מועדון',     style: ''                                    ,isSortable: false    }),
        GRID.column('clubCommission',          {title: 'עמלת מועדון',       style: 'sortable'                                                }),
        GRID.column('regularDiscount', 			{title: 'הנחה רגילה',		style: ''					,isSortable:false	}),
        GRID.column('creditDiscount',            {title: 'הנחת אשראי',     style: ''  ,  isSortable: false  }),
        GRID.column('displayTotalDiscount',            {title: 'סך הנחה',     style: ''  , isSortable: false  }),
        GRID.column('actions',			{title: 'פעולות',			style: 'min center', templateName: 'clubs/grid/actions'  })
    ],

    rowClick: function(){
        console.log('row click');
        return true;
    }

});
