App.UiPaginationComponent = Em.Component.extend({

	tagName: 'ul',

	classNames: ['ui-pagination'],

	page: 0,
	pages: 0,
	perPage: 20,
	visiblePages: 2,

	pagesList: function()
	{
    var page = this.get('page');
    var pages = this.get('pages');

    var start = Math.max(1, page - this.get('visiblePages'));
    var end = Math.min(pages, page + this.get('visiblePages'));
    
    var pages = [];
    for (var i = start; i <= end; i++) {
        pages.push({
            number: i,
            title: i,
            active: (page == i)
        });
    }

    return pages;

	}.property('page', 'pages', 'visiblePages'),

	actions: {

		setPage: function(number)
		{
			if(number <= this.get('pages') && number > 0)
				this.set('page', number);
			else
				console.error('illigal page number ' + number);
		},

		nextPage: function()
		{
			var page = this.get('page');
			this.send('setPage', [page + 1]);
		},

		prevPage: function()
		{
			var page = this.get('page');
			this.send('setPage', [page - 1]);
		},

		firstPage: function()
		{
			this.send('setPage', [1]);
		},

		lastPage: function()
		{
			var pages = this.get('pages');
			this.send('setPage', [pages]);
		}

	}

});