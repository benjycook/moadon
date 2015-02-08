App.Router.map(function(){

	this.route('login');
	this.route('logout');

	this.route('search', {path: 'search'});

	// this.route('search', function(){
	// 	this.route('category',  			{path: 'category/:category_id'});
	// 	this.route('region', 	  			{path: 'region/:region_id'});
	// 	this.route('categoryRegion', 	{path: 'category/:category_id/region/:region_id'});
	// });

	this.resource('account', function(){
		this.route('orders');
	});

	this.resource('cart', function(){

	});

	this.route('supplier', {path: 'supplier/:id'});

});