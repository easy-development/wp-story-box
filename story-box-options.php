<?php

class StoryBoxOptions {

  protected static $_instance = null;

  /**
   * @return StoryBoxOptions
   */
  public static function getInstance() {
    if(self::$_instance == null)
      self::$_instance = new self();

    return self::$_instance;
  }

  public static function resetInstance() {

  }

  public $availableItemsList       = array(
    '.post', '.widget', '.sb-element', '.row', '.col-md-6', '.col-md-3', '.site-title',
    '.featuredpost', 'span', 'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div > p',
    'div > *'
  );

  public $availableEntranceEffects = array(
    'classic' => array(
      'name'        => 'Classic',
      'color'       => '#95a5a6',
      'description' => 'Default Effects provided by <a href="https://daneden.me/animate/" target="_blank">animate.css</a>',
      'effects' => array(
        'fadeInUp', 'fadeInRight', 'fadeInDown', 'fadeInLeft', 'fadeInUpBig', 'fadeInRightBig',
        'fadeInDownBig', 'fadeInLeftBig', 'bounceInUp', 'bounceInRight', 'bounceInDown', 'bounceInLeft', 'flipInX', 'flipInY',
        'rotateIn', 'rotateInDownLeft', 'rotateInDownRight', 'rotateInUpLeft', 'rotateInUpRight',
        'pulse', 'swing', 'wobble', 'flash', 'tada'
      )
    ),
    'zen'     => array(
      'name'        => 'Zen',
      'color'       => '#3498db',
      'description' => 'Special Effects Created for our friends from <a href="http://themeforest.net/user/stylishthemes" target="_blank">StylishThemes</a>',
      'effects'     => array(
        'zenFadeIn', 'zenFadeInUp', 'zenFadeInLeft', 'zenFadeInDown', 'zenFadeInRight',
        'zenFadeInBig', 'zenFadeInUpBig', 'zenFadeInLeftBig', 'zenFadeInDownBig', 'zenFadeInRightBig'
      )
    )
  );
  public $availableExitEffects     = array(
    'classic' => array(
      'name'        => 'Classic',
      'color'       => '#95a5a6',
      'description' => 'Default Effects provided by <a href="https://daneden.me/animate/" target="_blank">animate.css</a>',
      'effects' => array(
        'hinge', 'fadeOutUp', 'fadeOutRight', 'fadeOutDown', 'fadeOutLeft',
        'fadeOutUpBig', 'fadeOutRightBig', 'fadeOutDownBig', 'fadeOutLeftBig',
        'bounceOutUp', 'bounceOutRight', 'bounceOutDown', 'bounceOutLeft',
        'flipOutX', 'flipOutY',
        'rotateOut', 'rotateOutDownLeft', 'rotateOutDownRight', 'rotateOutUpLeft', 'rotateOutUpRight'
      )
    ),
  );
  public $availableDisabledMobileDevices = array(
    'Android', 'BlackBerry', 'iPhone', 'iPad', 'iPod', 'Opera Mini', 'IEMobile'
  );

  public $defaultAvailableItems  = array(
    '.post', '.widget', '.sb-element', '.row', '.col-md-6', '.col-md-3', '.site-title'
  );
  public $defaultEntranceEffects = array(
    'fadeInUp', 'fadeInRight', 'fadeInDown', 'fadeInLeft',
    'fadeInUpBig', 'fadeInRightBig', 'fadeInDownBig', 'fadeInLeftBig'
  );
  public $defaultExitEffects = array(
    'fadeOutUp', 'fadeOutRight', 'fadeOutDown', 'fadeOutLeft',
    'fadeOutUpBig', 'fadeOutRightBig', 'fadeOutDownBig', 'fadeOutLeftBig'
  );
  public $defaultDisabledMobileDevices = array(

  );

  public $themeAvailableItems        = array();
  public $themeEntranceEffects       = array();
  public $themeExitEffects           = array();
  public $themeDisabledMobileDevices = array();

  public $entranceEffects       = false;
  public $exitEffects           = false;
  public $availableItems        = array();
  public $disabledMobileDevices = array();

  public $userOptionAvailableItems          = 'sb_user_available_items';
  public $userOptionEntranceEffects         = 'sb_user_entrance_effects';
  public $userOptionExitEffects             = 'sb_user_exit_effects';
  public $userOptionDisabledMobileDevices   = 'sb_user_disabled_mobile_devices';

  public $userDefinedAvailableItems       = array();
  public $userOptionDefinedAvailableItems = 'sb_user_defined_available_items';

  public function __construct() {
    $this->_setDefaults();
    $this->_setThemeOptions();
    $this->_setUserSettings();
  }

  private function _setDefaults() {
    $this->entranceEffects       = $this->defaultEntranceEffects;
    $this->exitEffects           = $this->defaultExitEffects;
    $this->availableItems        = $this->defaultAvailableItems;
    $this->disabledMobileDevices = $this->defaultDisabledMobileDevices;
  }

  private function _setThemeOptions() {
    $this->themeAvailableItems        = (defined('SB_AVAILABLE_ITEMS'))         ? json_decode(SB_AVAILABLE_ITEMS)         : array();
    $this->themeEntranceEffects       = (defined('SB_ENTRANCE_EFFECTS'))        ? json_decode(SB_ENTRANCE_EFFECTS)        : array();
    $this->themeExitEffects           = (defined('SB_EXIT_EFFECTS'))            ? json_decode(SB_EXIT_EFFECTS)            : array();
    $this->themeDisabledMobileDevices = (defined('SB_DISABLED_MOBILE_DEVICES')) ? json_decode(SB_DISABLED_MOBILE_DEVICES) : array();

    $this->availableItems         = !empty($this->themeAvailableItems)         ? $this->themeAvailableItems        : $this->availableItems;
    $this->entranceEffects        = !empty($this->themeEntranceEffects)        ? $this->themeEntranceEffects       : $this->entranceEffects;
    $this->exitEffects            = !empty($this->themeExitEffects)            ? $this->themeExitEffects           : $this->exitEffects;
    $this->disabledMobileDevices  = !empty($this->themeDisabledMobileDevices)  ? $this->themeDisabledMobileDevices : $this->disabledMobileDevices;
  }

  private function _setUserSettings() {
    $this->availableItems        = get_option($this->userOptionAvailableItems,        $this->availableItems);
    $this->entranceEffects       = get_option($this->userOptionEntranceEffects,       $this->entranceEffects);
    $this->exitEffects           = get_option($this->userOptionExitEffects,           $this->exitEffects);
    $this->disabledMobileDevices = get_option($this->userOptionDisabledMobileDevices, $this->disabledMobileDevices);

    $this->userDefinedAvailableItems = get_option($this->userOptionDefinedAvailableItems,     $this->userDefinedAvailableItems);
  }

  public function setUserDefinedAvailableItems($userDefinedAvailableItems) {
    $this->userDefinedAvailableItems = $userDefinedAvailableItems;
    update_option($this->userOptionDefinedAvailableItems, $userDefinedAvailableItems);
  }

  public function setUserAvailableItems($availableItems) {
    $this->availableItems = $availableItems;
    update_option($this->userOptionAvailableItems, $availableItems);
  }

  public function setUserEntranceEffects($entranceEffects) {
    $this->entranceEffects = $entranceEffects;
    update_option($this->userOptionEntranceEffects, $entranceEffects);
  }

  public function setUserExitEffects($exitEffects) {
    $this->exitEffects = $exitEffects;
    update_option($this->userOptionExitEffects, $exitEffects);
  }

  public function setUserDisabledMobileDevices($disabledMobileDevices) {
    $this->disabledMobileDevices = $disabledMobileDevices;
    update_option($this->userOptionDisabledMobileDevices, $disabledMobileDevices);
  }

  public function listJQueryControllerParams() {
    $ret = '';

    if($this->availableItems != false)
      $ret .= ' data-available-items="' . implode(',', $this->availableItems) . '" ';

    if($this->entranceEffects != false)
      $ret .= ' data-entrance-effects="' . implode(',', $this->entranceEffects) . '" ';

    if($this->exitEffects != false)
      $ret .= ' data-exit-effects="' . implode(',', $this->exitEffects) . '" ';

    return $ret;
  }

}