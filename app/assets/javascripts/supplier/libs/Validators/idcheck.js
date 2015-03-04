window.ParsleyConfig.i18n.en = $.extend(window.ParsleyConfig.i18n.en || {}, {});
window.ParsleyValidator
  .addValidator('idcheck', function (value, requirement) 
  {
    if(value.length==9)
    {
      var sum=0;
      for (var i=1;i<value.length+1;i++) 
      {
        var num=value[i-1];
        if(isNaN(num))
          return false;
        if(i%2)
          num=num*1;
        else
          num=num*2;
        if(num>9)
          num=num%10+parseInt(num/10);
        sum+=num;
      }
      if(sum%10)
        return false;
      return true;
    }
    else
    {
    	return false;
    }
  }, 256)
  .addMessage('en', 'idcheck', 'המספר שהזנת אינו תקין');