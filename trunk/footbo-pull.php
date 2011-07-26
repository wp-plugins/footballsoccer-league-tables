{\rtf1\ansi\ansicpg1252\cocoartf1038\cocoasubrtf350
{\fonttbl\f0\fmodern\fcharset0 Courier;}
{\colortbl;\red255\green255\blue255;}
\paperw11900\paperh16840\margl1440\margr1440\vieww11800\viewh9820\viewkind0
\deftab720
\pard\pardeftab720\ql\qnatural

\f0\fs24 \cf0 <?php\
/*\
Plugin Name: Football/Soccer League Tables\
Plugin URI: http://footballteamnews.info\
Description: Football/Soccer League Tables grabs the latest football Tables from footbo.com and post them into your wordpress website/blog.\
\
The plugin uses shortcodes to display the information which means you can have as many league tables as you want!\
\
This is the free version of Football League Tables, you can upgrade to pro and get team fixtures! Go to http://chelseateamnews.info for more information on how to get the pro version.\
\
Example:\
\
Go to http://www.footbo.com/Leagues and find the link from footbo.com and add the league name from the url \'96 http://www.footbo.com/Competitions/Premier_League then add the shortcode to your page/post.\
\
[footbo type="league" name="Premier_League"]\
\
Leagues supported are:\
\
France\
\
Ligue 2 (20)\uc0\u8232 Ligue 1 (20 Teams)\u8232 Coupe de France (194)\u8232 Coupe de la Ligue (24)\
\
England\
\
Premier League (20 Teams)\uc0\u8232 Championship (24)\u8232 League One (24)\
\
Spain\
\
La Liga (20 Teams)\uc0\u8232 Segunda Division Spain (22)\u8232 Tercera Division (359)\u8232 Segunda B (83)\
\
Italy\
\
Serie A Italy (20 Teams)\uc0\u8232 Serie B Italy (22)\u8232 Coppa Italia (79)\
\
Germany\
\
Bundesliga (18 Teams)\uc0\u8232 3. Bundesliga (20)\u8232 2. Bundesliga (18)\u8232 DFB Pokal (64)\
\
Netherlands\
\
Eredivisie (19)\uc0\u8232 KNVB Cup (89)\
\
Portugal\
\
Portugal Super Liga (16 Teams)\uc0\u8232 Taca de Portugal (172)\
\
United States\
\
MLS (16)\uc0\u8232 USL 1 (12)\u8232 W-League (29)\u8232 WPS (6)\
\
Brazil\
\
Brasileirao (21 Teams)\uc0\u8232 Copa do Brasil (64)\u8232 Paulista (20)\u8232 Mineiro (12)\u8232 Carioca (16)\u8232 Baiano (12)\u8232 Gaucho (16)\
\
Argentina\
\
Argentina Primera Division (22 Teams)\
\
Mexico\
\
Mexico Primera Division (19 Teams)\
Russia\
\
Vischaya (17 Teams)\
\
Scotland\
\
Scottish Premier League (12 Teams)\uc0\u8232 Scottish Cup (81)\
\
Turkey\
\
Superlig (19 Teams)\uc0\u8232 Turkiye Kupasi (72)\u8232 TFF 1.LIG (19)\u8232 TFF 2.LIG (41)\
\
Ireland\
\
FAI League (11 Teams)\uc0\u8232 FAI Cup (45)\
\
Switzerland\
\
Super League Switzerland (10 Teams)\uc0\u8232 Swiss Cup (143)\
\
Austria\
\
Austria_Bundesliga (11 Teams)\uc0\u8232 OFB Cup (67)\
\
Sweden\
\
Allsvenskan (17 Teams)\
\
Belgium\
\
Jupiler Pro League (16)\uc0\u8232 Beker van Belgie (299)\
Northern Ireland\
Premiership (12)\uc0\u8232 Irish Cup (62)\
\
Chile\
\
Chile Primera Division (18 Teams)\
\
Uruguay\
\
Uruguay Primera Division (16 Teams)\
\
Venezuela\
\
Venezuela Primera Division (18 Teams)\
\
Costa Rica\
\
Costa Rica Primera Division (12 Teams)\
\
Peru\
\
Peru Primera Division (16 Teams)\
\
Colombia\
\
Primera A Colombia (18 Teams)\
\
Full list of league tables: http://www.footbo.com/Leagues\
\
Version: 1.1\
Author: B Apps\
License: GPL2\
*/\
\
require 'pull.inc.php';\
\
\
/* [footboo_pull] */\
add_shortcode('footbo', 'footbo_puller');\
function footbo_puller($attrib)\{\
  \
  $attrib = shortcode_atts(\
    array(\
    'type'  => '',\
    'name'  => '',\
  ), $attrib);\
  \
  $resp = pull($attrib['type'], $attrib['name']);\
  \
  if(!empty($resp)) \{\
  	if($attrib['type'] == 'league')\
      return '<div class="footbo_league">' . $resp . '</div>';\
  	\
  \}\
  else\
    return 'currently not available - try reload the page!';\
\
\}\
\
\
// add stylesheet\
$footboUrl = WP_PLUGIN_URL . '/footbo-pull/css/footbo_style.css';\
$footboFile = WP_PLUGIN_DIR . '/footbo-pull/css/footbo_style.css';\
if ( file_exists($footboFile) ) \{\
  wp_register_style('footbo_pull_style', $footboUrl);\
  wp_enqueue_style( 'footbo_pull_style');\
\}\
\
\
// add options page ------------------------/\
//------------------------------------------/\
add_action('admin_menu', 'footbo_menu');\
\
function footbo_menu() \{\
  add_options_page('Footbo Info Puller Options', 'Footbo-Pull', 'manage_options', 'footbo-info-puller', 'footbo_plugin_options');\
  add_action( 'admin_init', 'register_footbo_settings' );\
\}\
\
function register_footbo_settings() \{\
  //register our settings\
  register_setting( 'footbo-settings', 'footbo-field-cache', 'cache_field_sanitize');\
  add_settings_section('footbo-general-section', 'footbo Info Puller Options', 'footbo_section_callback', 'footbo-info-puller');\
  add_settings_field('footbo-field-cache-id', 'Cache Expiry Time (minutes)', 'footbo_field_callback', 'footbo-info-puller', 'footbo-general-section');\
\}\
\
function footbo_plugin_options() \{\
  if (!current_user_can('manage_options'))  \{\
    wp_die( __('You do not have sufficient permissions to access this page.') );\
  \}\
?>\
  <div class="wrap">\
    <form action="options.php" method="post">  \
    <?php settings_fields('footbo-settings'); ?>\
    <?php do_settings_sections('footbo-info-puller'); ?><!-- slug -->\
    It's important not to fetch data on every pageload. Fetch it one time and then cache it for a while, which increases\
    page load times drastically.\
    <p><input name="Submit" type="submit" value="<?php esc_attr_e('Save'); ?>" /></p>\
    </form>\
  </div>\
\
\
<?php  \
\}\
\
function footbo_field_callback() \{\
  echo '<input id="footbo-field-cache-id" name="footbo-field-cache" type="text" value="'.get_option('footbo-field-cache').'" />';\
\}\
\
function footbo_section_callback() \{\
  echo '<h4>General</h4>';\
\}\
\
function cache_field_sanitize($input) \{\
  $val = trim($input);\
  if(!preg_match('/[0-9]/', $val) || $val < 1) \{\
    $val = 1;\
  \}\
  return $val; \
\}}