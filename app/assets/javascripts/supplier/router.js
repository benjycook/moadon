App.Router.map(function(){

	this.route('login');
	this.route('logout');
	this.route('order');
	this.resource('realizations', function(){});
});