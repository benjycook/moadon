var get = Ember.get;
var set = Ember.set;
// var escapeExpression = Ember.Handlebars.Utils.escapeExpression;
App.UiChosenComponent = Ember.Component.extend({
  tagName: 'select',
  classNames: ["ember-chosenselect", "form-control", "chosen-rtl"],
  attributeBindings: ['disabled','multiple', 'required'],
  layout: Ember.Handlebars.compile('{{compiledOptions}}'),
  optionValuePath:    'id',
  optionLabelPath:    'name',
  optionDisabledPath: 'disabled',
  compiledOptions: null,

  settings: null,
  
 // disabled: true,

  //chosen commit action
  commitAction: null,


  //chosen settings
  allow_single_deselect: false,
  disable_search: false,
  disable_search_threshold: 10,
  enable_split_word_search: true,
  inherit_select_classes: true,
  max_selected_options: "Infinity",
  no_results_text: "No matching results",
  placeholder_text_multiple: "Select Some Options",
  placeholder_text_single: "Select an Option",
  search_contains: true,
  single_backstroke_delete: true,
  width: "100%",
  display_disabled_options: true,
  display_selected_options: true,

  _computedCompiledOptions: function(){
    var selectedValue       = this.get('selected');
    var optionDisabledPath  = this.get('optionDisabledPath');
    var optionLabelPath     = this.get('optionLabelPath');
    var optionValuePath     = this.get('optionValuePath');

    var options = get(this, 'options.model') || get(this, 'options') || [];
    var prompt  = this.get('prompt');

    var output  = "";
    var disabledAttribute = 'disabled="disabled"';
    var selectedAttribute = 'selected="selected"';
    var itemSelected, promptSelected;

    for (var i = 0; i < options.length; i++) {
      var item = options[i],
          disabled = '',
          selected = '',
          option, value, label;
      if(Em.isArray(selectedValue))
      {
        if(selectedValue.indexOf(item['id']) !== -1)
        {
          itemSelected = item;
          selected = selectedAttribute;
        }
      }else{
        if(selectedValue === item['id']) {
          itemSelected = item;
          selected = selectedAttribute;
        }        
      }

      if (typeof item === 'object') {
        value = Ember.Handlebars.Utils.escapeExpression(get(item, optionValuePath));
        label = Ember.Handlebars.Utils.escapeExpression(get(item, optionLabelPath));
        disabled = get(item, optionDisabledPath) ? disabledAttribute : '';
      } else {
        label = value = Ember.Handlebars.Utils.escapeExpression(item);
      }

      output += '<option ' + disabled + ' ' + selected + ' value="' + value + '">' + label + '</option>';
    }

    if (prompt) {
      //select only if not other selection
      var promptSelected = '';
      if (!itemSelected) {
        promptSelected = selectedAttribute;

        //if (selectedValue) { set(this, 'selected', null); }
      }
      disabledAttribute = '';
      output = '<option value="" ' + promptSelected + ' ' + disabledAttribute + '>' + prompt + '</option>' + output;
    }


    set(this, 'compiledOptions', new Ember.Handlebars.SafeString(output));
  },

  valueTracker: function(){
    var self = this;
    
    Ember.run.next(function(){
      self._setSelectedIndex();
      self.$().trigger("chosen:updated");
    });

  }.observes('selected','disabled', 'compiledOptions'),


 getSelectedIndex: function(){
    var offset = 0;
    var selected = this.get('selected');
    var allOptions = get(this, 'options.model') || get(this, 'options') || [];
    
    if(allOptions)
    {
      if (get(this, 'prompt')) { offset = 1; }

      for (var i = 0; i < allOptions.length; i++) {
        if(selected == allOptions[i].id)
        {
          return i+offset;
        }  
      }
    }


    return 0;
 },

 _selectedValue: function() {
    var offset = 0;
    var selectedIndex = this.$()[0].selectedIndex;
    var selectedIndexs = [];
    var allOptions = get(this, 'options.model') || get(this, 'options') || [];
   
    if (get(this, 'prompt')) { offset = 1; }

    if(this.get('multiple'))
    {
      offset = 0;
      var items = this.$()[0].options;
      for(var i = 0; i < items.length; i++)
      {
        if(items[i].selected)
          selectedIndexs.push(allOptions[i]);
      }
    
      return selectedIndexs;
    }

    return allOptions[selectedIndex - offset];
  },
 

  didInsertElement: function(){

    var that = this;

    this.set('settings.placeholder_text_single', 'בחר...');
    if( typeof jQuery().chosen === undefined ){
      console.error("Ember.ChosenSelectView: Unable to initialize. jQuery Chosen not found. Please aquire Chosen plugin: http://harvesthq.github.io/");
    } else {
      this.$().chosen(this.get("settings"));
      
      this.$().on('change', function(){
          //that.executeCommitAction();

          that.change();
        });

      if(this.get('commitAction') ){
        var that = this;
        
        this.$().on('change', function(){
          that.executeCommitAction();
        });
      }
    }

  },

  _setSelectedIndex: function(){
    if(!this.get('multiple'))
    {    
      var selected = this.getSelectedIndex();
      if(selected)
        this.$()[0].selectedIndex = selected;
    }else{
      var selected = this.get('selected');
      var options = this.$()[0].options;
      for(var i = 0; i < options.length; i++)
      {
        var id = parseInt(options[i].value);
        if(selected.indexOf(id) != -1)
          options[i].selected = true;
        else
          options[i].selected = false;
      } 
    }
  },

  change: function()
  {    
      var selected = this._selectedValue();

      if(Em.isArray(selected))
      {
          var ids = selected.map(function(item){
              return item['id'];
          });
          this.set('selected', ids);   
      }else if(selected){
          this.set('selected', selected['id']);
      }else{
          this.set('selected', null);
      }
  },

  _setupOptionsObservers: Ember.observer(function(){
    var optionDisabledPath = this.get('optionDisabledPath');
    var optionLabelPath    = this.get('optionLabelPath');
    var optionValuePath    = this.get('optionValuePath');
    var options            = this.get('options');
    var complexOptions     = this.get('complexOptions');
    var setupComplexOptionsObservers = false;

    Ember.addObserver(this, 'options.[]', this._computedCompiledOptions);

    setupComplexOptionsObservers = complexOptions;

    if (setupComplexOptionsObservers === undefined) {
      setupComplexOptionsObservers = typeof options[0] === "object";
    }

    if (setupComplexOptionsObservers) {
      Ember.addObserver(this, 'options.@each.' + optionDisabledPath, this._computedCompiledOptions);
      Ember.addObserver(this, 'options.@each.' + optionValuePath, this._computedCompiledOptions);
      Ember.addObserver(this, 'options.@each.' + optionLabelPath, this._computedCompiledOptions);
    }

    this._computedCompiledOptions();
  }).on('init'),


  //setup chosen options
  _setupChosenOptions: Ember.observer(function(){
      var properties = this.getProperties(
          "allow_single_deselect",
          "disable_search",
          "disable_search_threshold",
          "enable_split_word_search",
          "inherit_select_classes",
          "max_selected_options",
          "no_results_text",
          "placeholder_text_multiple",
          "placeholder_text_single",
          "search_contains",
          "single_backstroke_delete",
          "width",
          "display_disabled_options",
          "display_selected_options"
      );

      this.set("settings", properties);

      return this.get("settings");
  }).on('init')
});


//fix chosen for ember
AbstractChosen.prototype.winnow_results = function() {
  var escapedSearchText, option, regex, regexAnchor, results, results_group, searchText, startpos, text, zregex, _i, _len, _ref;
  this.no_results_clear();
  results = 0;
  searchText = this.get_search_text();
  escapedSearchText = searchText.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
  regexAnchor = this.search_contains ? "" : "^";
  regex = new RegExp(regexAnchor + escapedSearchText, 'i');
  zregex = new RegExp(escapedSearchText, 'i');
  _ref = this.results_data;
  for (_i = 0, _len = _ref.length; _i < _len; _i++) {
    option = _ref[_i];
    option.search_match = false;
    results_group = null;
    if (this.include_option_in_results(option)) {
      if (option.group) {
        option.group_match = false;
        option.active_options = 0;
      }
      if ((option.group_array_index != null) && this.results_data[option.group_array_index]) {
        results_group = this.results_data[option.group_array_index];
        if (results_group.active_options === 0 && results_group.search_match) {
          results += 1;
        }
        results_group.active_options += 1;
      }
      if (!(option.group && !this.group_search)) {
        option.search_text = option.group ? option.label : option.text;
        option.search_match = this.search_string_match(option.search_text, regex);
        if (option.search_match && !option.group) {
          results += 1;
        }
        if (option.search_match) {
          if (searchText.length) {
            startpos = option.search_text.search(zregex);
            text = option.search_text.substr(0, startpos + searchText.length) + '</em>' + option.search_text.substr(startpos + searchText.length);
            option.search_text = text.substr(0, startpos) + '<em>' + text.substr(startpos);
          }
          if (results_group != null) {
            results_group.group_match = true;
          }
        } else if ((option.group_array_index != null) && this.results_data[option.group_array_index].search_match) {
          option.search_match = true;
        }
      }
    }
  }
  this.result_clear_highlight();
  if (results < 1 && searchText.length) {
    this.update_results_content("");
    return this.no_results(searchText);
  } else {
    this.update_results_content(this.results_option_build());
    return this.winnow_results_set_highlight();
  }
};