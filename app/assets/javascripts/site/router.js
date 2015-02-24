App.Router.map(function(){

	this.route('login');
	this.route('logout');
	this.route('cart');

	this.route('register');
	//this.route('login');

	this.route('search', {path: 'search'});

	// this.route('search', function(){
	// 	this.route('category',  			{path: 'category/:category_id'});
	// 	this.route('region', 	  			{path: 'region/:region_id'});
	// 	this.route('categoryRegion', 	{path: 'category/:category_id/region/:region_id'});
	// });

	this.resource('account', function(){
		this.route('orders');
	});


	this.route('supplier', {path: 'supplier/:id'});

});