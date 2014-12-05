var WPStoryBox = {

  isMobileDevice : function(device){
    return navigator.userAgent.match(RegExp(device, "i"));
  },

  Init : function(nodeList, disabledDevices) {
    var objectInstance = this,
        runStoryBox    = true;

    jQuery.each(disabledDevices, function(key, disabledDevice){
      if(objectInstance.isMobileDevice(disabledDevice))
        runStoryBox = false;
    });

    if(runStoryBox == true) {
      jQuery.each(nodeList, function(node, information){
        if(typeof information.show !== "undefined"
            && information.show != '')
              jQuery(node).attr('data-sb', information.show.join(','));

        if(typeof information.hide !== "undefined"
            && information.hide != '')
              jQuery(node).attr('data-sb-hide', information.hide.join(','));
      });

      StoryBox.Init(jQuery('body'));
    }
  }

};