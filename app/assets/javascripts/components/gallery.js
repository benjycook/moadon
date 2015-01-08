App.UiGalleryComponent = Em.Component.extend({
	layoutName:"components/ui-gallery",
	//images:[],
	base:"",
	defualt:"../img/defualt.png",
	uploadUrl:"",
	setup:function()
	{
		var images = this.get('data.images');
		var newImages = Em.ArrayController.create({
		sortProperties: ['pos'],
		content:[]
		});
		var base  = this.get('data.base');
		var defualt = this.get('defualt');
		var uploadUrl = this.get('uploadUrl');
		images.forEach(function(item){
			var temp = Em.Object.create(item);
			if(item.src == undefined)
				item.src = defualt;
			temp.set('fullSrc',base+item.src+"?"+new Date().getTime());
			temp.set('url',item.id+uploadUrl);
			newImages.pushObject(temp);
		});
		this.set('images',newImages);
	}.on('init'),

	didInsertElement:function()
	{
		var self = this;
		var sortable = this.$('ul').sortable({
			update:function()
			{
				var indexes = [];
				$(this).find('li').each(function(index){
					indexes.push($(this).data('id'));
				});
				self.updatePositions(indexes);
			}
		});
		this.set('sortable',sortable);
	},
	updatePositions:function(indexes)
	{
		//var images = Em.ArrayProxy.create({content:Em.A(this.get('images'))});
		var images = this.get('images');
		images.beginPropertyChanges();
		for (var i = 0; i < indexes.length; i++) {
			var imageId = indexes[i];
			var image = images.findBy('id',imageId);
			image.set('pos',i+1);
		};
		var self = this;
		images.endPropertyChanges();
		Em.run.next(function(){
			self.rerender();
		});
	
	},

	imagesChanged:function()
	{
		var images = this.get('images.content');
		this.set('data.images',images.sortBy('pos'));
	}.observes('images.@each.fullSrc','images.@each.pos'),

	changed:function()
	{
		this.setup();
	}.observes('data'),

	actions: {
		'add':function()
		{
			var images = this.get('images');
			var image = {};
			image.id = (Math.floor((Math.random()*10000000)+1)+Math.floor((Math.random()*10000000)+1))+"temp";
			image.url = image.id+this.get('uploadUrl');
			image.src = null;
			image.pos = (images.get('content.length')+1)+"";
			image.fullSrc = this.get('base')+this.get('defualt');
			images.pushObject(Em.Object.create(image));
		},
		'remove':function(id)
		{
			var images = this.get('images');
			image = images.findBy('id',id);
			images.removeObject(image);
		}
  	}
});