

Ember.ChosenSelectView = Ember.Select.extend(Ember.TargetActionSupport,
  {
    //defaultTemplate: precompileTemplate('{{#if view.prompt}}<option value=""></option>{{/if}}{{#if view.optionGroupPath}}{{#each view.groupedContent}}{{view view.groupView content=content label=label}}{{/each}}{{else}}{{#each view.content}}{{view view.optionView content=this}}{{/each}}{{/if}}'),
    classNames: ["ember-chosenselect"],
    
    ///////////////
    //! Settings //
    ///////////////
    optionLabelPath: 'content.name',
    optionValuePath: 'content.id',

    settings: null,
    

    commitAction: null,
    
    /**
     * Chosen Options
     * Reference: http://harvesthq.github.io/chosen/options.html
     */
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
    
    
    init: function(){
        var selected = this.get('selected');

        var self = this;
        var content = this.get('content');
        var path = this.get('optionValuePath');
        var selected = this.get('selected');
       
        var multiple = this.get('multiple');
       
        var valuePath = path.replace(/^content\.?/, '');
        if(multiple)
        {
            var selection = [];
            if(!selected)
              selected = [];
            for(var i = 0; i < selected.length; i++)
            {
                var found = content.findBy(valuePath, selected[i]);
                if(found)
                    selection.push(found);
            }
            this.set('selection', selection);
        }else{
            selection = content.findBy(valuePath, selected)
            this.set('selection', selection);
        }


        this._super();
    },
    /**
     * Ember.ChosenSelectView#valueTracker
     * Observes the set value on the view to automatically trigger a chosen:updated event.
     */
    valueTracker: function(){

      // User Ember.run.next to ensure the update doesn't happen too early.
      console.log('valueTracker');
      Ember.run.next(this, function(){
        this.$().trigger("chosen:updated");
      });
    }.observes('value','disabled','content'),
    
    observesSelected: function(){
      var multiple = this.get('multiple');
      if(!multiple)
      {
         console.log('observesSelected',this.get('selected'));
        this.set('value', this.get('selected'));
      }
    }.observes('selected'),

    /////////////
    //! Events //
    /////////////
    
    /**
     * Ember.ChosenSelectView#willInsertElement
     * Populates #settings for later initialization.
     */
    willInsertElement: function(){
        
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
    },
    
    /**
     * Ember.ChosenSelectView#willInsertElement
     * Initializes the chosen select box.
     */
    didInsertElement: function(){
      var that = this;


      //this.set('settings.placeholder_text_single', 'בחר...');
      if( typeof jQuery().chosen === undefined ){
        console.error("Ember.ChosenSelectView: Unable to initialize. jQuery Chosen not found. Please aquire Chosen plugin: http://harvesthq.github.io/");
      } else {
        this.$().chosen(this.get("settings"));
        
        if( this.get('commitAction') ){
          var that = this;
          
          this.$().on('change', function(){
            that.executeCommitAction();
          });
        }
      }
    },
    
    /**
     * Ember.ChosenSelectView#executeCommitAction
     * Executes commit action on the change event of the select box.
     * Must be an event on the controller that is assigned to the view.
     */
    executeCommitAction: function(){
      var that = this;
      if( that.get("commitAction") !== null ){
        that.triggerAction({
          target: that.get("controller"),
          action: that.get("commitAction")
        });
      }
    },
    
    /**
     * Ember.ChosenSelectView#willInsertElement
     * Destroys the chosen select box on view teardown.
     */
    willDestroyElement: function(){
      this.$().chosen("destroy");
    },


    change: function()
    {
        var selection = this.get('selection');
        var multiple = this.get('multiple');
        var path = this.get('optionValuePath');
        var valuePath = path.replace(/^content\.?/, '');
        console.log(selection);
        if(Em.isArray(selection))
        {
            var ids = selection.map(function(item){
                return item[valuePath];
            });
            this.set('selected', ids);    
        }else if(selection){
            this.set('selected', selection[valuePath]);
        }else{
            this.set('selected', null);
        }
    }
  }
);

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