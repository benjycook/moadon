App.PagingController = Em.ArrayController.extend({

	url: '',
	collection: 'content',
	maxPages: false,

	page: 0,
	pages: 0,
	items: 5,
	count: 0,

	query: '',

	hasNext: function(){
		var page = this.get('page');
		var pages = this.get('pages');
		if(page < pages)
			return true;
	}.property('page', 'pages'),

	'hasPrev': function(){
		var page = this.get('page');
		var pages = this.get('pages');
		if(page > 1)
			return true;
	}.property('page', 'pages'),

	search: function(){
		this.set('page', 1);
		this.reload();

	}.observes('query'),

	reload: function(page){
		var self = this;
		var params = [];
		params.push('page='+this.get('page'));
		params.push('items='+this.get('items'));
		params.push('query='+this.get('query'));
		params = params.join('&');

		var url = this.get('url');
		var collection = this.get('collection');

		this.request = $.getJSON(url+'?'+params).then(function(data){	
			var count = data.meta.count;
			var items = parseInt(self.get('items'));
			var pages = data.meta.pages;
			var page = self.get('page');

			var maxPages = self.get('maxPages');

			var center = Math.round(maxPages / 2);

			var firstPage = Math.max(page - center, 1);
			var lastPage = page + center;


			var pageing = [];

			for(var i = firstPage; i <= lastPage; i++)
			{
				pageing.push({num: i, active: page == i});
			}

			self.set('pageing', pageing);
			self.set(collection, data[collection]);
			self.set('count', count);
			self.set('pages', pages);

		});

	}.observes('page', 'items'),

	actions: {

		'first': function(){
			this.set('page', 1);
		},

		'last': function(){
			this.set('page', this.get('pages'));
		},

		'next': function(){
			this.incrementProperty('page');
		},

		'prev': function(){
			this.decrementProperty('page');
		},

		'setPage': function(page)
		{
			this.set('page', parseInt(page));
		}
	}

});