Options = App.Options = Em.ObjectController.create();

Options.weekDays = Em.ArrayController.create();

Options.weekDays.set('content', [
	{id: 0,  name: "ראשון"},
	{id: 1,  name: "שני"	},
	{id: 2,  name: "שלישי"	},
	{id: 3,  name: "רביעי"	},
	{id: 4,  name: "חמישי"	},
	{id: 5,  name: "שישי"	},
	{id: 6,  name: "שבת"	}
]);

Options.displayTypes = Em.ArrayController.create();

Options.displayTypes.set('content', [
	{id: "1",  name: "all"},
	{id: "2",  name: "personal"},
]);


/*info@segs.co.il
123456*/