App.ApplicationRoute = Em.Route.extend({
	model: function(){
		return $.getJSON('options');
	}
});