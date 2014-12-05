var StoryBoxController = {

  panel                  : {},
  areaSelectionNamespace : 'storyBoxController',
  previewIFrame          : {},
  availableItems         : ['.post', '.widget', '.sb-element', '.row', '.col-md-6', '.col-md-3', '.site-title'],
  availableItemColor     : 'rgba(52, 152, 219,0.6)',
  currentItemColor       : 'rgba(231, 76, 60, 0.6)',
  setItemColor           : 'rgba(46, 204, 113,0.6)',
  currentMAP             : {},
  entranceEffects        : [],
  exitEffects            : [],

  Init : function(panel, params) {
    var objectInstance = this;
    this.panel = panel;

    jQuery.each(params, function(key, value){
      objectInstance[key] = value;
    });

    this.currentMAP = JSON.parse(this.panel.find('.serializedInformation:first').val());

    this.panel.find('.manage').html('');
    this.panel.find('.description').html(
        '<p>Available blocks will be marked with this color : </p>' +
            '<p style="display: block;height:20px;width:100%;background:' + this.availableItemColor + '"></p>' +
            '<p>Current blocks will be marked with this color : </p>' +
            '<p style="display: block;height:20px;width:100%;background:' + this.currentItemColor + '"></p>' +
            '<p>Assigned blocks will be marked with this color : </p>' +
            '<p style="display: block;height:20px;width:100%;background:' + this.setItemColor + '"></p>'
    );

    this.panel.find('.startStoryEffect').bind('click', function(event){
      event.preventDefault();
      event.stopPropagation();

      objectInstance.setIFrame();
      objectInstance.bindAssignElement();

      objectInstance.panel.find('.startStoryEffect').hide();
      objectInstance.panel.find('.closeStoryEffect').show();
    });

    this.panel.find('.closeStoryEffect').bind('click', function(event){
      event.preventDefault();
      event.stopPropagation();

      objectInstance.unbindAssignElement();

      objectInstance.panel.find('.closeStoryEffect').hide();
      objectInstance.panel.find('.startStoryEffect').show();
      objectInstance.panel.find('.manage').html('');
    });

    this.panel.find('.clearAll').bind('click', function(event){
      var r = confirm("Are you sure ?");
      if (r==true) {
        objectInstance.currentMAP = {};
        objectInstance.panel.find('.closeStoryEffect').click();
        objectInstance.save();
      }
    });
  },

  setIFrame : function() {
    this.previewIFrame = jQuery('#customize-preview > iframe');
    this.bindAssignElement();
  },

  bindAssignElement : function() {
    var objectInstance = this;

    this.colorElementByFind(this.availableItems, this.availableItemColor);

    jQuery.each(this.currentMAP, function(key, value) {
      objectInstance.colorElementByFind(
          objectInstance.previewIFrame.contents().find(key),
          objectInstance.setItemColor
      );
    });

    this.previewIFrame
        .contents()
        .find((this.availableItems instanceof Array ? this.availableItems.join(', ') : this.availableItems))
        .bind('click.' + this.areaSelectionNamespace, function(event){
          event.preventDefault();
          event.stopImmediatePropagation();

          jQuery.each((objectInstance.availableItems instanceof Array ? objectInstance.availableItems : [objectInstance.availableItems]), function(key, value) {
            objectInstance.colorElementByFind(
                objectInstance.previewIFrame.contents().find(value),
                objectInstance.availableItemColor
            );
          });

          jQuery.each(objectInstance.currentMAP, function(key, value) {
            objectInstance.colorElementByFind(
                objectInstance.previewIFrame.contents().find(key),
                objectInstance.setItemColor
            );
          });

          var currentPattern = objectInstance._getElementInDepthPath(jQuery(this));

          objectInstance.setCurrentElementPath(currentPattern);
        });
  },

  _getElementInDepthPath : function(elementObject) {
    var elementIdentifier = '';

    jQuery.each(this.availableItems, function(key, value){
      if(elementObject.is(value))
        elementIdentifier += value;
    });

    var parent = elementObject.parent();
    do {
      elementIdentifier = parent[0].tagName.toLowerCase() + ' > ' + elementIdentifier;

      parent = parent.parent();
    } while(parent[0].tagName != 'HTML' && parent[0].tagName != 'html');

    return elementIdentifier;
  },

  unbindAssignElement : function() {
    this.resetElementByFind(this.availableItems);

    this.previewIFrame
        .contents()
        .find((this.availableItems instanceof Array ? this.availableItems.join(', ') : this.availableItems))
        .unbind('click.' + this.areaSelectionNamespace);
  },

  colorElementByFind : function(findPath, color) {
    var objectInstance = this;

    if(findPath instanceof Array
        || typeof findPath == "object")
      jQuery.each(findPath, function(key, value){
        objectInstance._colorElement(value, color);
      });
    else
      this._colorElement(findPath, color);
  },

  _colorElement : function(findPath, color) {
    this.previewIFrame
        .contents()
        .find(findPath)
        .css('border', '3px solid ' + color);
  },

  resetElementByFind : function(findPath) {
    var objectInstance = this;

    if(findPath instanceof Array
        || typeof findPath == "object")
      jQuery.each(findPath, function(key, value){
        objectInstance._resetElement(value);
      });
    else
      this._resetElement(findPath);
  },

  _resetElement : function(findPath) {
    this.previewIFrame
        .contents()
        .find(findPath)
        .css('border', '');
  },

  setCurrentElementPath : function(path) {
    this.colorElementByFind(path, this.currentItemColor);
    this._displayCurrentEffectOptions(path);

  },

  _displayCurrentEffectOptions : function(path) {
    var objectInstance = this,
        html = '',
        currentElements = typeof this.currentMAP[path] == "undefined" ? {show : [], hide : []} : this.currentMAP[path];

    html += '<h2>Entrance</h2>';

    html += '<select name="effectControll" data-effect-type="show" style="width:100%;">';

    html += '<option value="0">Please Select...</option>';

    jQuery.each(this.entranceEffects, function(key, effect){
      html += '<option value="' + effect + '"' + (jQuery.inArray(effect, currentElements.show) != -1 ? ' disabled="disabled" ' : '') + '>' + effect + '</option>';
    });

    html += '</select>';

    html += '<ul class="effectActiveController"  data-effect-type="show">';

    jQuery.each(currentElements.show, function(key, effect){
      html += '<li><span>' + effect + '</span><a class="removeEffect" data-effect="' + effect + '">Remove</a></li>';
    });

    html += '</ul>';

    html += '<h2>Exit ( Optional ) </h2>';

    html += '<select name="effectControll"  data-effect-type="hide" style="width:100%;">';

    html += '<option value="0">Please Select...</option>';

    jQuery.each(this.exitEffects, function(key, effect){
      html += '<option value="' + effect + '"' + (jQuery.inArray(effect, currentElements.hide) != -1 ? ' disabled="disabled" ' : '') + '>' + effect + '</option>';
    });

    html += '</select>';

    html += '<ul class="effectActiveController" data-effect-type="hide">';

    jQuery.each(currentElements.hide, function(key, effect){
      html += '<li><span>' + effect + '</span><a class="removeEffect" data-effect="' + effect + '">Remove</a></li>';
    });

    html += '</ul>';

    this.panel.find('.manage').html(html);

    objectInstance._setPanelRemove(path);

    this.panel.find('.manage [name="effectControll"]').change(function(){
      if(jQuery(this).val() == 0)
        return;

      var effect = jQuery(this).val();

      jQuery(this).find('> option[value="' + effect + '"]').attr('disabled', 'disabled');
      jQuery(this).val(0);

      objectInstance
          .panel
          .find('.effectActiveController[data-effect-type="' + jQuery(this).attr('data-effect-type') + '"]')
          .append('<li><span>' + effect + '</span><a class="removeEffect" data-effect="' + effect + '">Remove</a></li>');

      objectInstance._setPanelRemove(path);
      objectInstance._readPanelFromInputAndSave(path);
    });
  },

  _setPanelRemove : function(path) {
    var objectInstance = this;

    this.panel.find('.effectActiveController .removeEffect').unbind('click').bind('click', function() {
      objectInstance.panel
          .find('select[data-effect-type="' + jQuery(this)
              .parent()
              .parent()
              .attr('data-effect-type') + '"] ' +
              '> option[value="' + jQuery(this).attr('data-effect') + '"]')
          .removeAttr('disabled');

      jQuery(this).parent().remove();

      objectInstance._readPanelFromInputAndSave(path);
    });
  },

  _readPanelFromInputAndSave : function(path) {
    var currentElements = {show : [], hide : []};

    this.panel.find('.manage [name="effectControll"] > option:disabled').each(function(){
      currentElements[jQuery(this).parent().attr('data-effect-type')]
          [currentElements[jQuery(this).parent().attr('data-effect-type')].length] = jQuery(this).val();
    });

    this.setElementPathEffect(path, currentElements.show, currentElements.hide);
  },

  setElementPathEffect : function(path, effectShow, effectHide) {
    if(jQuery.isEmptyObject(effectShow) && jQuery.isEmptyObject(effectHide))
      delete this.currentMAP[path];
    else
      this.currentMAP[path] = {
        show : effectShow,
        hide : effectHide
      };

    this.save();
  },

  save : function() {
    this.panel.find('.serializedInformation').val(JSON.stringify(this.currentMAP));
    this.panel.find('.serializedInformation').change();
  }

};

jQuery(document).ready(function(){
  var element = jQuery('.story_box_admin_element:first');
  if(element.length > 0) {

    var attributes = {}, params = {};

    jQuery.each( element[0].attributes, function( index, attr ) {
      attributes[ attr.name ] = attr.value;
    });

    jQuery.each(attributes, function(key, value){
      if(key.indexOf('data-') == 0) {
        var name        = key.replace('data-', ''),
            finalName   = '';

        name = name.split('-');

        value = value.split(',');

        jQuery.each(name, function(key, token){
          finalName += (key == 0) ? token : (token.charAt(0).toUpperCase() + token.slice(1));
        });

        params[finalName] = value;
      }
    });

    StoryBoxController.Init(element, params);
  }
});