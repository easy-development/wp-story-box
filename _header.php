<h2><?php echo StoryBoxAdmin::getInstance()->scriptName;?></h2>

<ul class="subsubsub sb-head-menu">
  <li style="margin-bottom: 0" class="<?php echo !(isset($_GET['sub-page']) && ($_GET['sub-page'] == 'node-list' || $_GET['sub-page'] == 'device-settings')) ? 'active' : ''?>">
    <a href="?page=<?php echo StoryBoxAdmin::getInstance()->scriptAlias;?>">
      <?php echo __('Administration')?>
    </a>
  </li>
  <li style="margin-bottom: 0" class="<?php echo (isset($_GET['sub-page']) && $_GET['sub-page'] == 'node-list') ? 'active' : ''?>">
    <a href="?page=<?php echo StoryBoxAdmin::getInstance()->scriptAlias;?>&sub-page=node-list">
      <?php echo __('Settings')?>
    </a>
  </li>
  <li style="margin-bottom: 0" class="<?php echo (isset($_GET['sub-page']) && $_GET['sub-page'] == 'device-settings') ? 'active' : ''?>">
    <a href="?page=<?php echo StoryBoxAdmin::getInstance()->scriptAlias;?>&sub-page=device-settings">
      <?php echo __('Device Settings')?>
    </a>
  </li>
</ul>

<style>
  .sb-head-menu {
    float: none;
    width: 98%;
    border-bottom: 2px solid #3498DB;
    margin-bottom: 25px;
  }
  .sb-head-menu > li > a {
      display: block;
      background: #95A5A6;
      color: #FFFFFF;
      padding: 5px 20px;
      border-radius: 5px 5px 0 0;
      margin: 0;
  }

  .sb-head-menu > li > a:hover {
    color: #ffffff;
  }

  .sb-head-menu > li.active > a {
      background: #3498DB;
  }
</style>

<div style="clear: both;"></div>