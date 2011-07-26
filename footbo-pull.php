<?php
/*
Plugin Name: Football Info Puller
Plugin URI: http://footballteamnews.info
Description: Add Tables and Fixtures to a WordPress blog.
Version: 1.0
Author: B Apps
License: GPL2
*/

require 'pull.inc.php';


/* [footboo_pull] */
add_shortcode('footbo', 'footbo_puller');
function footbo_puller($attrib){
  
  $attrib = shortcode_atts(
    array(
    'type'  => '',
    'name'  => '',
  ), $attrib);
  
  $resp = pull($attrib['type'], $attrib['name']);
  
  if(!empty($resp)) {
  	if($attrib['type'] == 'league')
      return '<div class="footbo_league">' . $resp . '</div>';
  	
  }
  else
    return 'currently not available - try reload the page!';

}


// add stylesheet
$footboUrl = WP_PLUGIN_URL . '/footbo-pull/css/footbo_style.css';
$footboFile = WP_PLUGIN_DIR . '/footbo-pull/css/footbo_style.css';
if ( file_exists($footboFile) ) {
  wp_register_style('footbo_pull_style', $footboUrl);
  wp_enqueue_style( 'footbo_pull_style');
}


// add options page ------------------------/
//------------------------------------------/
add_action('admin_menu', 'footbo_menu');

function footbo_menu() {
  add_options_page('Footbo Info Puller Options', 'Footbo-Pull', 'manage_options', 'footbo-info-puller', 'footbo_plugin_options');
  add_action( 'admin_init', 'register_footbo_settings' );
}

function register_footbo_settings() {
  //register our settings
  register_setting( 'footbo-settings', 'footbo-field-cache', 'cache_field_sanitize');
  add_settings_section('footbo-general-section', 'footbo Info Puller Options', 'footbo_section_callback', 'footbo-info-puller');
  add_settings_field('footbo-field-cache-id', 'Cache Expiry Time (minutes)', 'footbo_field_callback', 'footbo-info-puller', 'footbo-general-section');
}

function footbo_plugin_options() {
  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }
?>
  <div class="wrap">
    <form action="options.php" method="post">  
    <?php settings_fields('footbo-settings'); ?>
    <?php do_settings_sections('footbo-info-puller'); ?><!-- slug -->
    It's important not to fetch data on every pageload. Fetch it one time and then cache it for a while, which increases
    page load times drastically.
    <p><input name="Submit" type="submit" value="<?php esc_attr_e('Save'); ?>" /></p>
    </form>
  </div>


<?php  
}

function footbo_field_callback() {
  echo '<input id="footbo-field-cache-id" name="footbo-field-cache" type="text" value="'.get_option('footbo-field-cache').'" />';
}

function footbo_section_callback() {
  echo '<h4>General</h4>';
}

function cache_field_sanitize($input) {
  $val = trim($input);
  if(!preg_match('/[0-9]/', $val) || $val < 1) {
    $val = 1;
  }
  return $val; 
}