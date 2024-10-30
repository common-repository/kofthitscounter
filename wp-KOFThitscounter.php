<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-38371699-1']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
<?php
/*
Plugin Name: KOFT Hits Counter
Author URI: http://www.Knowledgeonfingertips.com
Description: KOFT Post Hits Counter displays the number of hits / views on each of page or post. A simple count indicates how many times a page has been accessed or opened.
Version: 1.5
Author: Knowledgeonfingertips.com
Author URI: http://www.Knowledgeonfingertips.com
*/
global $wpdb;
define('HC_TABLE_NAME', $wpdb->prefix . 'KOFT_hitcount');
define('HC_PATH', ABSPATH . 'wp-content/plugins/KOFThitcounter');
require_once(ABSPATH . '/wp-includes/pluggable.php');
$themename = "KOFTMenu";
$shortname = "KM";
$position = '63.3'; 
$icon = $file_dir."/images/koft.png";
// Hook for adding admin menus
add_action('admin_menu', 'mt_add_pages');
// action function for above hook
function mt_add_pages() {
     # add submenu
  $mincap=get_option('KOFT_mincap');
  if($mincap == '') {
    $mincap="level_2";
  }
  add_menu_page('KOFT Admin', 'KOFT Admin', $mincap, __FILE__, 'iniKOFT', plugins_url('KOFThitscounter/images/KOFT.png',dirname(plugin_basename(__FILE__))));
  add_submenu_page(__FILE__, __('Check Hits','KOFT'), __('Check Hits','KOFT'), $mincap, __FILE__ . '&KOFT_action=overview', 'iniKOFT');
    add_submenu_page(__FILE__, __('Visit Website','KOFT'), __('Visit Website','KOFT'), $mincap,  __FILE__ . '&KOFT_action=redirect', 'iniKOFT');
}
function iniKOFT() 
{
    if(isset($_GET['KOFT_action']))
	{
      if ($_GET['KOFT_action'] == 'redirect')
	  { 
           iniKOFTRedirect();      
   	  } 
	  elseif ($_GET['KOFT_action'] == 'overview')
	  {
           iniKOFTMain();
	  }
	}else iniKOFTMain();
}
function iniKOFTRedirect() {
  echo "<script language=javascript>window.location.href= 'http://www.knowledgeonfingertips.com'</script>";
}
/**
 * Show overwiew
 */
function iniKOFTMain() 
{
  global $wpdb;
  $table_name = $wpdb->prefix . "KOFT_hitcount";
  $querylimit="LIMIT 50";
  # Tabella Last hits
  print "<div class='wrap'><h2>". __('Top Page Hits','KOFT'). "</h2><table class='widefat'><thead><tr><th colspan='2' scope='col'><script type="text/javascript"><!--
google_ad_client = "ca-pub-3100332509000145";
/* KOFT ads on WP */
google_ad_slot = "3185802680";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script></th></tr><tr><th scope='col'>". __('Page Url','KOFT'). "</th><th scope='col'>". __('Hit Count','KOFT'). "</th></tr></thead>";
  print "<tbody id='the-list'>";	

  $KOFTdrafts = $wpdb->get_results("SELECT * FROM $table_name ORDER bY hit DESC $querylimit");
  foreach ($KOFTdrafts as $KOFTdrafts) 
  {
    print "<tr>";
    print "<td>". $KOFTdrafts->name ."</td>";
    print "<td>". $KOFTdrafts->hit ."</td>";
    print "</tr>";
  }
  print "</table></div>";
}
function KOFThitcounter_install(){
global $wpdb;
if ( $wpdb->get_var('SHOW TABLES LIKE "' . HC_TABLE_NAME . '"') != HC_TABLE_NAME )
{
$sql = "CREATE TABLE IF NOT EXISTS `". HC_TABLE_NAME . "` (";
$sql .= "`SN` BIGINT NOT NULL AUTO_INCREMENT,";
$sql .= "`name` VARCHAR( 1000 ) NOT NULL,";
$sql .= "`hit` BIGINT NOT NULL DEFAULT '1',";
$sql .= "PRIMARY KEY ( `SN` )";
$sql .= ") ENGINE = MYISAM;";
$wpdb->query($sql);
 }
} 
function KOFThitcounter_uninstall(){
global $wpdb;
$sql = "DROP TABLE `". HC_TABLE_NAME . "`;";
$wpdb->query($sql);
}
register_activation_hook(__FILE__, 'KOFThitcounter_install');
register_deactivation_hook(__FILE__, 'KOFThitcounter_uninstall');?>
<?php
function KOFThitcounter_curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}
function get_KOFThitcounter(){
$url = KOFThitcounter_curPageURL();
	$url = str_replace ("http://",'',$url);
	$url = str_replace ("www.",'',$url);
    if ($url != "")
		{
			$query = "Select hit from `". HC_TABLE_NAME . "` where name = '$url'"; 
			$result = mysql_query($query);
			if (!$result) 
			{
    			die('Invalid query: ' . mysql_error());
			}
			if (mysql_affected_rows()==0)
			{
				$query = "Insert into `". HC_TABLE_NAME . "` (name) values ('$url')";
				$result = mysql_query($query);
				echo " Total Views: 1 ";
				if (!$result) 
				{
    				die('Invalid query: ' . mysql_error());
				}
			}
			else
			{
				$hitcount = mysql_result($result, 0);
				$hitcount++;
				echo " Total Views: $hitcount ";
				$query = "Update `". HC_TABLE_NAME . "` set hit = $hitcount where name = '$url'";
				$result = mysql_query($query);
				if (!$result) 
				{
    				die('Invalid query: ' . mysql_error());
				}
			}
		}?>
<?php }
//admin setting
add_action('admin_menu', 'KOFThitcounter_menu');
function KOFThitcounter_menu() {
add_options_page('Plugin KOFThitcounter', 'KOFThitcounter Options', 1, 'plugin_KOFThitcounter_menu', 'KOFThitcounter_options');
}
function KOFThitcounter_options() {
if (!current_user_can(1))  {
wp_die( __('You do not have sufficient permissions to access this page.') );
}
echo '
<div class="wrap">';
echo '<h3>Enable KOFT Hits Counter</h3>';
echo 'Just insert the following shortcode anywhere in your blog (for use in a widget: use the text-widget and insert the shortcode there) <br/>';
echo '<pre>&lt;?php get_KOFThitcounter();?&gt;</pre>';
}
?>