App.Router.map(function(){

	this.route('search');

	this.resource('account', function(){
		this.route('orders');
	});

	this.resource('cart', function(){

	});

	this.route('supplier', {path: 'supplier/:id'});

});