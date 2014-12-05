<?php

class StoryBoxAdmin {

  protected static $_instance;

  public static function getInstance() {
    if(self::$_instance == null)
      self::$_instance = new self();

    return self::$_instance;
  }

  public static function resetInstance() {
    self::$_instance = null;
  }

  public $scriptName          = 'Story Box';
  public $scriptShortName     = "Story Box";
  public $scriptAlias         = "story-box";
  public $scriptBasePath      = '';

  public function __construct() {
    $this->scriptBasePath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
    $this->setWordPressHooks();
  }

  public function setWordPressHooks() {
    add_action( 'admin_menu', array( $this, '_addAdministrationMenu' ) );
  }

  public function _addAdministrationMenu() {
    add_menu_page(
      $this->scriptName,
        '&nbsp;' . $this->scriptShortName,
      'manage_options',
      $this->scriptAlias,
      array(
        $this, 'displayAdministration'
      ),
      plugins_url( 'admin_icon.png', __FILE__)
    );
  }

  public function displayAdministration() {

    require_once('_header.php');

    if(isset($_GET['sub-page']) && $_GET['sub-page'] == 'node-list')
      $this->administrationNodeListPage();
    else if(isset($_GET['sub-page']) && $_GET['sub-page'] == 'device-settings')
      $this->administrationDeviceSettings();
    else
      $this->administrationIndexPage();

    $this->_administrationJavascript();
  }

  public function administrationIndexPage() {
    if(isset($_POST['entranceEffect']))
      StoryBoxOptions::getInstance()->setUserEntranceEffects($_POST['entranceEffect']);
    if(isset($_POST['exitEffect']))
      StoryBoxOptions::getInstance()->setUserExitEffects($_POST['exitEffect']);

    if(isset($_POST['exitEffect']) || isset($_POST['entranceEffect']))
      echo '<div class="updated below-h2" id="message"><p>' . __('Your Settings have been saved') . '</p></div>';

    echo '<form method="POST">';
      echo '<div class="row">';
        echo '<div style="width:48%;margin:1%;float:left;">';
          $this->_administrationEntranceEffects();
          echo '<br/>';
          echo '<input type="submit" name="submit" class="button button-primary right" value="Save All"/>';
        echo '</div>';
        echo '<div style="width:48%;margin:1%;float:left;">';
          $this->_administrationExitEffects();
          echo '<br/>';
          echo '<input type="submit" name="submit" class="button button-primary right" value="Save All"/>';
        echo '</div>';
        echo '<div style="clear:both"></div>';
      echo '</div>';
    echo '</form>';
  }

  public function administrationNodeListPage() {
    if(isset($_POST['story_box_new_node_submit'])) {
      $userDefinedAvailable = StoryBoxOptions::getInstance()->userDefinedAvailableItems;
      $userDefinedAvailable[] = $_POST['story_box_new_node_list'];

      $userDefinedAvailable = array_unique($userDefinedAvailable);

      StoryBoxOptions::getInstance()->setUserDefinedAvailableItems($userDefinedAvailable);

      if(isset($_POST['story_box_new_node_list_is_available'])) {
        $availableItems = StoryBoxOptions::getInstance()->availableItems;
        $availableItems[] = $_POST['story_box_new_node_list'];

        $availableItems = array_unique($availableItems);

        StoryBoxOptions::getInstance()->setUserAvailableItems($availableItems);
      }

      echo '<div class="updated below-h2" id="message"><p>' . __('Your New Node has been successfully Added') . '</p></div>';
    } else if(isset($_POST['storyBoxEditNodeTriggerSave'])) {
      $keys = array_keys($_POST['storyBoxEditNodeTriggerSave']);

      foreach($keys as $key) {
        $currentItem = $_POST['storyBoxEditNode'][$key];

        $userDefinedAvailable    = StoryBoxOptions::getInstance()->userDefinedAvailableItems;
        $userDefinedAvailableKey = array_flip($userDefinedAvailable);

        if(isset($userDefinedAvailableKey[$currentItem['node_before']]))
          unset($userDefinedAvailable[$userDefinedAvailableKey[$currentItem['node_before']]]);

        $userDefinedAvailable[] = $currentItem['node'];

        $userDefinedAvailable = array_unique($userDefinedAvailable);

        StoryBoxOptions::getInstance()->setUserDefinedAvailableItems($userDefinedAvailable);

        $availableItems = StoryBoxOptions::getInstance()->availableItems;
        $availableItemsKey = array_flip($availableItems);

        if(isset($availableItemsKey[$currentItem['node_before']]))
          unset($availableItems[$availableItemsKey[$currentItem['node_before']]]);

        if(isset($currentItem['is_displayed'])) {
          $availableItems[] = $currentItem['node'];
        }

        $availableItems = array_unique($availableItems);

        StoryBoxOptions::getInstance()->setUserAvailableItems($availableItems);

        echo '<div class="updated below-h2" id="message"><p>' . __('Node has been successfully edited') . '</p></div>';
      }
    } else if(isset($_POST['storyBoxDeleteNode'])) {
      $keys = array_keys($_POST['storyBoxDeleteNode']);
      $userDefinedAvailable    = StoryBoxOptions::getInstance()->userDefinedAvailableItems;

      foreach($keys as $key) {
        $node = $userDefinedAvailable[$key];

        unset($userDefinedAvailable[$key]);

        StoryBoxOptions::getInstance()->setUserDefinedAvailableItems($userDefinedAvailable);

        $availableItems = StoryBoxOptions::getInstance()->availableItems;
        $availableItemsKey = array_flip($availableItems);

        if(isset($availableItemsKey[$node])) {
          unset($availableItems[$availableItemsKey[$node]]);
          StoryBoxOptions::getInstance()->setUserAvailableItems($availableItems);
        }
      }

      echo '<div class="updated below-h2" id="message"><p>' . __('Node has been successfully deleted') . '</p></div>';

    } else if(isset($_POST['availableItems'])) {
      StoryBoxOptions::getInstance()->setUserAvailableItems($_POST['availableItems']);
      echo '<div class="updated below-h2" id="message"><p>' . __('Your Settings have been saved') . '</p></div>';
    }

    echo '<form method="POST">';
      echo '<div class="row">';
        echo '<div style="width:98%;margin:1%;max-width: 800px;">';
          $this->_administrationIntroAvailableItems();
          $this->_administrationUserDefinedAvailableItems();
          $this->_administrationAvailableItems();
        echo '<br/>';
        echo '<input type="submit" name="submit" class="button button-primary right" value="Save All"/>';
        echo '</div>';
      echo '</div>';
    echo '</form>';
  }

  public function administrationDeviceSettings() {
    if(isset($_POST['disabledDevice'])) {
      StoryBoxOptions::getInstance()->setUserDisabledMobileDevices($_POST['disabledDevice']);

      echo '<div class="updated below-h2" id="message"><p>' . __('Your Settings have been saved') . '</p></div>';

    }

    echo  '<h2>' . __('Disabled Device Administration') . '</h2>';

    echo  '<div style="display:inline-block;width:45%;cursor:pointer;padding:15px 1%;background:#3498DB;color:#ffffff;text-align:center;">'  .
              __('Disable Story Box for certain devices') . '</div>';

    echo '<form method="POST" style="width:47%;">';

      echo    '<table class="wp-list-table widefat">';
      echo      '<thead>';
      echo        '<tr>';
      echo          '<th>' . __('Device Name')  .'</th>';
      echo          '<th>' . __('Disable ?')  .'</th>';
      echo          '<th>' . __('Status')  .'</th>';
      echo        '</tr>';
      echo      '</thead>';
      echo      '<tbody>';

      foreach(StoryBoxOptions::getInstance()->availableDisabledMobileDevices as $device) {
        echo      '<tr>';
        echo        '<td>' . $device . '</td>';
        echo        '<td><input type="checkbox" name="disabledDevice[]" value="' . $device . '" ' . (in_array($device, StoryBoxOptions::getInstance()->disabledMobileDevices ) ? 'checked="checked"' : ''). '/></td>';
        echo        '<td>' . (in_array($device, StoryBoxOptions::getInstance()->themeDisabledMobileDevices) ? '<strong style="color: #2ecc71;">Theme Suggested</strong>' : (in_array($device, StoryBoxOptions::getInstance()->defaultDisabledMobileDevices) ? '<strong style="color: #3498db;">Default</strong>' : 'Available')) . '</td>';
        echo      '</tr>';
      }
      echo      '</tbody>';
      echo    '</table>';

      echo '<br/>';

      echo '<input type="submit" name="submit" class="button button-primary right" value="Save All"/>';

    echo '</form>';
  }

  private function _administrationEntranceEffects() {
    echo  '<h2>' . __('Entrance Effects') . '</h2>';

    echo '<div class="row">';
      foreach(StoryBoxOptions::getInstance()->availableEntranceEffects as $packAlias => $effectPack) {
        echo  '<div style="display:inline-block;width:45%;cursor:pointer;padding:2%;background:' . $effectPack['color']. ';color:#ffffff;text-align:center;" class="effectPackTrigger" data-pack-group="entrance" data-pack-alias="' . $packAlias . '">' . $effectPack['name'] . ' (' . count($effectPack['effects']) . ' ' . __('Effects') . ') </div>';
      }
    echo '</div>';

    foreach(StoryBoxOptions::getInstance()->availableEntranceEffects as $packAlias => $effectPack) {
      echo  '<div class="effectPack" data-pack-group="entrance" data-pack-alias="' . $packAlias . '" style="display:none;">';
      echo    '<p>' . $effectPack['description'] . '</p>';
      echo    '<table class="wp-list-table widefat">';
      echo      '<thead>';
      echo        '<tr>';
      echo          '<th>' . __('Effect Name')  .'</th>';
      echo          '<th>' . __('Display ?')  .'</th>';
      echo          '<th>' . __('Information')  .'</th>';
      echo        '</tr>';
      echo      '</thead>';
      echo      '<tbody>';

      foreach($effectPack['effects'] as $effect) {
        echo      '<tr>';
        echo        '<td>' . $effect . '</td>';
        echo        '<td><input type="checkbox" name="entranceEffect[]" value="' . $effect . '" ' . (in_array($effect, StoryBoxOptions::getInstance()->entranceEffects ) ? 'checked="checked"' : ''). '/></td>';
        echo        '<td>' . (in_array($effect, StoryBoxOptions::getInstance()->themeEntranceEffects) ? '<strong style="color: #2ecc71;">Theme Suggested</strong>' : (in_array($effect, StoryBoxOptions::getInstance()->defaultEntranceEffects) ? '<strong style="color: #3498db;">Default</strong>' : 'Available')) . '</td>';
        echo      '</tr>';
      }
      echo      '</tbody>';
      echo    '</table>';

      echo '</div>';
    }
  }

  private function _administrationExitEffects() {
    echo  '<h2>' . __('Exit Effects') . '</h2>';


    echo '<div class="row">';
      foreach(StoryBoxOptions::getInstance()->availableExitEffects as $packAlias => $effectPack) {
        echo  '<div style="display:inline-block;width:96%;cursor:pointer;padding:2%;background:' . $effectPack['color']. ';color:#ffffff;text-align:center;" class="effectPackTrigger" data-pack-group="exit" data-pack-alias="' . $packAlias . '">' . $effectPack['name'] . ' (' . count($effectPack['effects']) . ' ' . __('Effects') . ') </div>';
      }
    echo '</div>';

    foreach(StoryBoxOptions::getInstance()->availableExitEffects as $packAlias => $effectPack) {
      echo  '<div class="effectPack" data-pack-group="exit" data-pack-alias="' . $packAlias . '" style="display:none;">';
      echo    '<p>' . $effectPack['description'] . '</p>';
      echo    '<table class="wp-list-table widefat">';
      echo      '<thead>';
      echo        '<tr>';
      echo          '<th>' . __('Effect Name')  .'</th>';
      echo          '<th>' . __('Display ?')  .'</th>';
      echo          '<th>' . __('Information')  .'</th>';
      echo        '</tr>';
      echo      '</thead>';
      echo      '<tbody>';

      foreach($effectPack['effects'] as $effect) {
        echo      '<tr>';
        echo        '<td>' . $effect . '</td>';
        echo        '<td><input type="checkbox" name="exitEffect[]" value="' . $effect . '" ' . (in_array($effect, StoryBoxOptions::getInstance()->exitEffects ) ? 'checked="checked"' : ''). '/></td>';
        echo        '<td>' . (in_array($effect, StoryBoxOptions::getInstance()->themeExitEffects) ? '<strong style="color: #2ecc71;">Theme Suggested</strong>' : (in_array($effect, StoryBoxOptions::getInstance()->defaultExitEffects) ? '<strong style="color: #3498db;">Default</strong>' : 'Available')) . '</td>';
        echo      '</tr>';
      }
      echo      '</tbody>';
      echo    '</table>';

      echo '</div>';
    }
  }

  private function _administrationIntroAvailableItems() {
    echo  '<h2>' . __('Available Items Node List ( Advanced )') . '</h2>';
    echo  '<p>' . __('Nothing bad can happen, just enjoy more features from Story Box, experimenting different elements which the system will add effects to') . '</p>';
  }

  private function _administrationUserDefinedAvailableItems() {
    echo  '<h4>User Defined</h4>';

    echo  '<table class="wp-list-table widefat story-box-smart-edit-table">';
    echo    '<thead>';
    echo      '<tr>';
    echo        '<th>' . __('Structure')  .'</th>';
    echo        '<th>' . __('Display ?')  .'</th>';
    echo        '<th></th>';
    echo      '</tr>';
    echo    '</thead>';
    echo    '<tbody>';


    foreach(StoryBoxOptions::getInstance()->userDefinedAvailableItems as $key => $userDefinedAvailableItem) {
      echo '<tr class="view-' . $key . '">';
      echo  '<td>' . $userDefinedAvailableItem . '</td>';
      echo  '<td>' . (in_array($userDefinedAvailableItem, StoryBoxOptions::getInstance()->availableItems) ? 'Yes' : 'No') . '</td>';
      echo  '<td><a class="toggle-edit button-primary" data-display="' . $key . '">Edit</a> <input type="submit" class="toggle-edit button-primary" name="storyBoxDeleteNode[' . $key . ']" value="Delete" style="background: #e74c3c;border-color: #e74c3c;"/></td>';
      echo '</tr>';
      echo '<tr class="edit-' . $key . '" style="display:none;">';
      echo  '<td><input type="hidden" name="storyBoxEditNode[' . $key . '][node_before]"value="' . $userDefinedAvailableItem . '"/><input type="text" name="storyBoxEditNode[' . $key . '][node]" style="width:100%;" value="' . $userDefinedAvailableItem . '"/>' . '</td>';
      echo  '<td><input type="checkbox" name="storyBoxEditNode[' . $key . '][is_displayed]" value="' . $userDefinedAvailableItem . '" ' . (in_array($userDefinedAvailableItem, StoryBoxOptions::getInstance()->availableItems ) ? 'checked="checked"' : ''). '/></td>';
      echo  '<td><input type="submit" class="button-primary" name="storyBoxEditNodeTriggerSave[' . $key . ']" value="Save"/> <a class="toggle-view button-secondary" data-display="' . $key . '">Cancel</a> </td>';
      echo '</tr>';
    }

    echo '<tr>';
    echo  '<td><input type="text" name="story_box_new_node_list" style="width:100%;"/></td>';
    echo  '<td><input type="checkbox" name="story_box_new_node_list_is_available" value=""/></td>';
    echo  '<td><input type="submit" name="story_box_new_node_submit" class="button-primary" value="Create New Node Item"/></td>';
    echo '</tr>';

    echo    '</tbody>';
    echo  '</table>';
  }

  private function _administrationAvailableItems() {
    echo  '<h4>Default & Theme Suggested</h4>';

    echo  '<table class="wp-list-table widefat">';
    echo    '<thead>';
    echo      '<tr>';
    echo        '<th>' . __('Structure')  .'</th>';
    echo        '<th>' . __('Information')  .'</th>';
    echo        '<th>' . __('Display ?')  .'</th>';
    echo      '</tr>';
    echo    '</thead>';
    echo    '<tbody>';

    foreach(array_unique(array_merge(StoryBoxOptions::getInstance()->availableItemsList, StoryBoxOptions::getInstance()->themeAvailableItems)) as $availableItem) {
      echo '<tr>';
      echo  '<td>' . $availableItem . '</td>';
      echo  '<td>' . (in_array($availableItem, StoryBoxOptions::getInstance()->themeAvailableItems) ? '<strong style="color: #2ecc71;">Theme Suggested</strong>' : (in_array($availableItem, StoryBoxOptions::getInstance()->defaultAvailableItems) ? '<strong style="color: #3498db;">Default</strong>' : 'Available')) . '</td>';
      echo  '<td><input type="checkbox" name="availableItems[]" value="' . $availableItem . '" ' . (in_array($availableItem, StoryBoxOptions::getInstance()->availableItems ) ? 'checked="checked"' : ''). '/></td>';
      echo '</tr>';
    }

    echo    '</tbody>';
    echo  '</table>';
  }

  private function _administrationJavascript() {
    ?>
      <script type="text/javascript">
        jQuery(document).ready(function(){
          jQuery('.effectPackTrigger').bind('click', function(){
            jQuery('.effectPack[data-pack-group="' + jQuery(this).attr('data-pack-group') +  '"]')
                .not('[data-pack-alias="' + jQuery(this).attr('data-pack-alias') +  '"]')
                .slideUp('slow');
            jQuery('.effectPack[data-pack-group="' + jQuery(this).attr('data-pack-group') +  '"][data-pack-alias="' + jQuery(this).attr('data-pack-alias') +  '"]')
                .slideDown('slow');
          });

          jQuery('.effectPackTrigger[data-pack-alias="classic"]').trigger('click');

          jQuery('.story-box-smart-edit-table').find('.toggle-edit').bind('click', function(){
            var tr = jQuery(this).parents('table:first');

            tr.find('.view-' + jQuery(this).attr('data-display')).hide();
            tr.find('.edit-' + jQuery(this).attr('data-display')).show();
          });

          jQuery('.story-box-smart-edit-table').find('.toggle-view').bind('click', function(){
            var tr = jQuery(this).parents('table:first');

            tr.find('.view-' + jQuery(this).attr('data-display')).show();
            tr.find('.edit-' + jQuery(this).attr('data-display')).hide();
          });
        });
      </script>
    <?php
  }

}

StoryBoxAdmin::getInstance();
