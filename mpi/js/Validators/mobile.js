window.ParsleyConfig.i18n.en = $.extend(window.ParsleyConfig.i18n.en || {}, {});
window.ParsleyValidator
  .addValidator('mobile', function (value, requirement) 
  {
    value = value.replace(/-/g, '');
    var prefixes = ["050","052","053","054","055","057","058"];
    if(value.length<10||value.length>10)
      return false;
    console.log(value.length);
    var prefix = value.substring(0,3);
    if(prefixes.indexOf(prefix)==-1)
        return false;
    for(var i=1;i<value.length+1;i++) 
    {
      if(isNaN(value[i-1]))
        return false;
    }
    return true;
  }, 256)
  .addMessage('en', 'mobile', 'המספר שהזנת אינו תקין');