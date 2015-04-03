App.Router.map(function(){

	this.route('login');
	this.route('logout');
	this.route('cart');

	// this.route('account/register');
	//this.route('login');

	this.route('search', {path: 'search'});

	// this.route('search', function(){
	// 	this.route('category',  			{path: 'category/:category_id'});
	// 	this.route('region', 	  			{path: 'region/:region_id'});
	// 	this.route('categoryRegion', 	{path: 'category/:category_id/region/:region_id'});
	// });

	this.resource('account', function(){
		this.resource('orders',function(){
			this.route('view',{path:':orders_id/view'});
		});
		this.route('register');
	});

	this.route('newsuppliers', {path: 'בתי-עסק-חדשים'});
	this.route('mostviewed', {path: 'הנצפים-ביותר'});
	this.route('hotdeals', {path: 'מבצעים-חמים'});


	this.route('supplier', {path: 'supplier/:id'});

});