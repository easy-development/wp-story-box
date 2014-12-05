<?php
/*
Plugin Name: Story Box
Plugin URI: http://easy-development.com
Description: We believe in Stories
Author: Easy Development
Version: 1.6
Author URI: http://easy-development.com
*/

require_once('story-box-options.php');
require_once('story-box-admin.php');

function storyBoxCustomizeFieldType($wp_customize) {

  class StoryBox_Customize_Element_Control extends WP_Customize_Control {
    public $type = 'story_box_element';

    public function render_content() {
      ?>
      <style>
        .story_box_admin_element .effectActiveController > li {
          width: 100%;
          background: #3498db;
          color: #ffffff;
        }

        .story_box_admin_element .effectActiveController > li > span{
          padding: 10px;
          color: #ffffff;
          display: inline-block;
        }

        .story_box_admin_element .effectActiveController > li > .removeEffect {
          float:right;
          background: #e74c3c;
          cursor: pointer;
          color: #ffffff;
          padding: 10px;
          display: inline-block;
        }
      </style>
      <script type="text/javascript" src="<?php echo plugins_url( 'admin-customize.min.js', __FILE__);?>"></script>
      <div class="story_box_admin_element"
           <?php echo StoryBoxOptions::getInstance()->listJQueryControllerParams();?>>
        <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
        <input class="serializedInformation"
               type="hidden" <?php $this->link(); ?>
               value='<?php echo $this->value(); ?>'/>

        <div class="description"></div>

        <a class="button left clearAll">Clear All Effects</a>
        <a class="button right closeStoryEffect" style="display: none;">Close Add Effects</a>
        <a class="button button-primary right startStoryEffect">Start Add Effects</a>

        <div class="manage"></div>

      </div>
    <?php
    }
  }

}

add_action( 'customize_register', 'storyBoxCustomizeFieldType' );

class StoryBox  {
  public $sectionAlias = 'storybox';
  public $sectionTitle = 'Story Box';

  public $settingSections      = 'storyboxsections';

  public function __construct() {

    $this->settingSections = md5(wp_get_theme()->Name) . $this->settingSections;

    add_action('wp_head', array($this, 'frontIntegration'));
    add_action('customize_register', $this->setupParams());
    add_action('init', array($this, 'queAssets'));
  }

  public function setupParams() {
    return array($this, 'wordPressHook');
  }

  /**
   * @param WP_Customize_Manager $wp_customize
   */
  public function wordPressHook($wp_customize) {
    $wp_customize->add_section( $this->sectionAlias , array(
      'title'      => __($this->sectionTitle),
      'priority'   => 30,
    ) );

    $wp_customize->add_setting( $this->settingSections, array(
      'default'        => '{}',
      'type'           => 'theme_mod',
      'capability'     => 'edit_theme_options',
      'transport'      => 'postMessage'
    ) );

    $wp_customize->add_control(
      new StoryBox_Customize_Element_Control(
        $wp_customize,
        $this->settingSections,
        array(
        'section'    => $this->sectionAlias,
        'settings'   => $this->settingSections
        )
      )
    );
  }

  public function frontIntegration() {

    ?>
    <script type="text/javascript">
      jQuery(document).ready(function(){
        WPStoryBox.Init(<?php echo get_theme_mod($this->settingSections); ?>, <?php echo json_encode(StoryBoxOptions::getInstance()->disabledMobileDevices); ?>);
      });
    </script>
    <?php
  }

  public function queAssets() {
    wp_enqueue_script("jquery");
    wp_enqueue_script("story_box-f", plugins_url( 'front.js', __FILE__));
    wp_enqueue_script("story_box-p", plugins_url( 'lib/story-box.min.js', __FILE__));
    wp_enqueue_style('animate.css', plugins_url( 'lib/animate.css', __FILE__));
    wp_enqueue_style('story-box-zen.css', plugins_url( 'lib/story-box-zen.css', __FILE__));
  }

}

$instance = new StoryBox();