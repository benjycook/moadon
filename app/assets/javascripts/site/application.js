App = Ember.Application.create({});

Em.TextField.reopen({
  attributeBindings: ['data-parsley-mobile','data-parsley-range','required','data-parsley-type','data-parsley-minlength','data-parsley-maxlength','readonly',"data-parsley-equalto","data-parsley-min",'data-parsley-idcheck']
});