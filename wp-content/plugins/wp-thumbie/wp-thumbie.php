<?php
/*
Plugin Name: Wp-Thumbie
Version:     0.1.9
Plugin URI:  http://www.blogsdna.com/5038/wp-thumbie-wordpress-plugin-from-blogsdna-lab.htm
Description: Show user defined number of related posts with thumbnail images from <a href="http://www.blogsdna.com"><strong>BlogsDNA</strong></a>. <a href="options-general.php?page=crp_options">Configure...</a>
Author:      blogsdna
Author URI:  http://www.blogsdna.com
*/

if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

define('ALD_crp_DIR', dirname(__FILE__));
define('CRP_LOCAL_NAME', 'crp');

// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );

function ald_crp_init() {
	//* Begin Localization Code */
	$crp_localizationName = CRP_LOCAL_NAME;
	$crp_comments_locale = get_locale();
	$crp_comments_mofile = ALD_crp_DIR . "/languages/" . $crp_localizationName . "-". $crp_comments_locale.".mo";
	load_textdomain($crp_localizationName, $crp_comments_mofile);
	//* End Localization Code */
}
add_action('init', 'ald_crp_init');


/*********************************************************************
*				Main Function (Do not edit)							*
********************************************************************/
function ald_crp() {
	global $wpdb, $post, $single,$WP_PLUGIN_URL;
	if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
	$pluginDir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)); 
	$crp_settings = crp_read_options();
	$limit = (stripslashes($crp_settings['limit']));
	$exclude_categories = explode(',',$crp_settings['exclude_categories']);
	
	// Make sure the post is not from the future
	$time_difference = get_settings('gmt_offset');
	$now = gmdate("Y-m-d H:i:s",(time()+($time_difference*3600)));

	if($crp_settings['match_content']) {
		$stuff = addslashes($post->post_title. ' ' . $post->post_content);
	}
	else {
		$stuff = addslashes($post->post_title);
	}
	
	
	if (($post->ID != '')||($stuff != '')) {
		$sql = "SELECT DISTINCT ID,post_title,post_date,post_content,"
		. "MATCH(post_title,post_content) AGAINST ('".$stuff."') AS score "
		. "FROM ".$wpdb->posts." WHERE "
		. "MATCH (post_title,post_content) AGAINST ('".$stuff."') "
		. "AND post_date <= '".$now."' "
		. "AND post_status = 'publish' "
		. "AND id != ".$post->ID." ";
		if ($crp_settings['exclude_pages']) $sql .= "AND post_type = 'post' ";
		$sql .= "ORDER BY score DESC ";
		
		$search_counter = 0;
		$searches = $wpdb->get_results($sql);
	} else {
		$searches = false;
	}
	
	$output = '<div id="wp_thumbie" style= "border: 0pt none ; margin: 0pt; padding: 0pt; clear: both;">';
	
	if($searches){
		//Setting css part for Image size
		$height = $crp_settings[height]+4;
		$output .= '<div id="wp_thumbie_rl1">'.(stripslashes($crp_settings[title])).'</div>';
		if($crp_settings[related_post_style] == '1') {
		}
		if($crp_settings[related_post_style] == '2') {
		$output .= '<ul class="wp_thumbie_ul_list" style="list-style-type: none;">';}
		if($crp_settings[related_post_style] == '3') {$output .= '<ul>';} //start of raw format tag
		foreach($searches as $search) {
			$categorys = get_the_category($search->ID);	//Fetch categories of the plugin
			$p_in_c = false;	// Variable to check if post exists in a particular category
			$title = trim(stripslashes($search->post_title));
			if ($crp_settings[display_post_text]) {
			$post_text = strip_tags($search->post_content);
			//$post_text = wp_trim_excerpt($post_text); // Get Post Ecerpt
			$post_text = truncate($post_text,$crp_settings[post_text_char]); // Get Post Excerpt
			}
			$image = ""; // Null Variable to verify for no impage found case
			
			preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', $search->post_content, $matches );
			if ( isset( $matches ) ) $image = $matches[1][0];
			
			if (strlen(trim($image))==0) { 
			$image = $pluginDir.'images/default.png' ; // when no image found in post 
			}
			//$image=parse_url($image, PHP_URL_PATH); to support external site images feature of 
			foreach ($categorys as $cat) {	// Loop to check if post exists in excluded category
				$p_in_c = (in_array($cat->cat_ID, $exclude_categories)) ? true : false;
				if ($p_in_c) break;	// End loop if post found in category
			}

			if (!$p_in_c) {
		//--------------Code to Output WP-Thumbie in Horizontal Format---------
				if($crp_settings[related_post_style] == '1') { //Related post in Horizontal Format
				$output .='Horizontal ';
				}
		//--------------Code to Output WP-Thumbie in Verticle Format---------
				if($crp_settings[related_post_style] == '2') { 
				$output .='<li id="wp_thumbie_li" style="height:'.$height.'px;"><div id="wp_thumbie_image"><a href="'.get_permalink($search->ID).'" target="_top"><img id="wp_thumbie_thumb" src="'.$pluginDir.'timthumb.php?src='.$image.'&w='.$crp_settings[width].'&h='.$crp_settings[height].'&zc=1"/></a></div><div id="wp_thumbie_title"><a href="'.get_permalink($search->ID).'" target="_top">'.$title.'</a></div><p id="description">'.$post_text.'</p></li>';
				}
		//--------------Code to Output WP-Thumbie in Raw Format---------
				if($crp_settings[related_post_style] == '3') { 
					$output .= '<li id="wp_thumbie_li"><div id="wp_thumbie_image"><a href="'.get_permalink($search->ID).'" rel="bookmark" target="_top"><img id="wp_thumbie_thumb" src="'.$pluginDir.'timthumb.php?src='.$image.'&w='.$crp_settings[width].'&h='.$crp_settings[height].'&zc=1"/></div><div id="wp_thumbie_title">'.$title.'</div></a><div id="description">'.$post_text.'</div></li>'; }
				$search_counter++; 
			}
			if ($search_counter == $limit) break;	// End loop when related posts limit is reached
		} //end of foreach loop
		if($crp_settings[related_post_style] == '1') { $output .= '</ul>'; } //end of Horizontal ul
		if($crp_settings[related_post_style] == '2') { $output .= '</ul>'; } //end of Verticle ul
		if($crp_settings[related_post_style] == '3') { $output .= '</ul>'; } //end of raw format tag
	} 
	else{
		$output = '<div id="crp_related">';
		$output .= ($crp_settings['blank_output']) ? ' ' : '<p>'.__('No related posts found',CRP_LOCAL_NAME).'</p>'; 
	}
	
	/* below code is commented which checks for li in output reason: unknown */
	//if ((strpos($output, '<li>')) === false) {
		//$output = '<div id="crp_related">';
		//$output .= ($crp_settings['blank_output']) ? ' ' : '<p>'.__('No related posts found',CRP_LOCAL_NAME).'</p>'; }
	
	$output .= '<div id="wp_thumbie_rl2"><small>By </small><a href="http://www.blogsdna.com"><small>Blogsdna</small></a></div></div><div class="clear"></div>';
	return $output;
}

function ald_crp_content($content) {
	
	global $single;
	$crp_settings = crp_read_options();
	$output = ald_crp();
	
    if((is_single())&&($crp_settings['add_to_content'])) {
        return $content.$output;
    } elseif((is_page())&&($crp_settings['add_to_page'])) {
        return $content.$output;
	} elseif((is_feed())&&($crp_settings['add_to_feed'])) {
        return $content.$output;
    } else {
        return $content;
    }
}
add_filter('the_content', 'ald_crp_content');

function wp_thumbie() {
	$output = ald_crp();
	echo $output;
}

// Default Options
function crp_default_options() {
	$title = __('<h3>Related Posts :</h3>',CRP_LOCAL_NAME);

	$crp_settings = 	Array (
						title => $title,			// Add before the content
						add_to_content => true,		// Add related posts to content (only on single posts)
						add_to_page => false,		// Add related posts to content (only on single pages)
						add_to_feed => false,		// Add related posts to feed
						limit => '5',				// How many posts to display?
						width => '70',				// Width of Image Thumbnail
						height => '70',				// Width of Image Thumbnail
						match_content => true,		// Match against post content as well as title
						exclude_pages => true,		// Exclude Pages
						blank_output => true,		// Blank output?
						exclude_categories => '',	// Exclude these categories
						exclude_cat_slugs => '',	// Exclude these categories
						related_post_style => '2',	// Related post format
						display_post_text => '',	// Display Post Excerpt
						post_text_char => '100' 	// Nummber Char for Post Text aka discription 
						);
	return $crp_settings;
}

// Function to read options from the database
function crp_read_options() 
{
	$crp_settings_changed = false;
	
	$defaults = crp_default_options();
	
	$crp_settings = array_map('stripslashes',(array)get_option('ald_crp_settings'));
	unset($crp_settings[0]); // produced by the (array) casting when there's nothing in the DB
	
	foreach ($defaults as $k=>$v) {
		if (!isset($crp_settings[$k]))
			$crp_settings[$k] = $v;
		$crp_settings_changed = true;	
	}
	if ($crp_settings_changed == true)
		update_option('ald_crp_settings', $crp_settings);
	
	return $crp_settings;

}

// Create full text index
function ald_crp_activate() {
	global $wpdb;

    $wpdb->hide_errors();
    $wpdb->query('ALTER TABLE '.$wpdb->posts.' ENGINE = MYISAM;');
    $wpdb->query('ALTER TABLE '.$wpdb->posts.' ADD FULLTEXT crp_related (post_title, post_content);');
    $wpdb->query('ALTER TABLE '.$wpdb->posts.' ADD FULLTEXT crp_related_title (post_title);');
    $wpdb->query('ALTER TABLE '.$wpdb->posts.' ADD FULLTEXT crp_related_content (post_content);');
    $wpdb->show_errors();
}
if (function_exists('register_activation_hook')) {
	register_activation_hook(__FILE__,'ald_crp_activate');
}

// This function adds an Options page in WP Admin
if (is_admin() || strstr($_SERVER['PHP_SELF'], 'wp-admin/')) {
	require_once(ALD_crp_DIR . "/admin.inc.php");
}

// Add meta links
function crp_plugin_actions( $links, $file ) {
	$plugin = plugin_basename(__FILE__);
 
	// create link
	if ($file == $plugin) {
		$links[] = '<a href="' . admin_url( 'options-general.php?page=crp_options' ) . '">' . __('Settings', crp_LOCAL_NAME ) . '</a>';
	}
	return $links;
}
// Generate Post Excert
function truncate ($str, $length, $trailing=' ...')
{
/*
** $str -String to truncate
** $length - length to truncate
** $trailing - the trailing character, default: "..."
*/
	  // take off chars for the trailing
	  $length-=mb_strlen($trailing);
	  if (mb_strlen($str)> $length)
	  {
		 // string exceeded length, truncate and add trailing dots
		 return mb_substr($str,0,$length).$trailing;
	  }
	  else
	  {
		 // string was already short enough, return the string
		 $res = $str;
	  }
	  return $res;
}
function add_css(){
$crp_settings = crp_read_options();
$pluginurl = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
if ($crp_settings[related_post_style] == '1') {$cssfile ='wp_thumbie_horizontal.css';}
if ($crp_settings[related_post_style] == '2') {$cssfile ='wp_thumbie_verticle.css';}
?>
<!--Wp-Thumbie Style Sheet-->
<link rel="stylesheet" href="<?php echo $pluginurl.$cssfile; ?>" type="text/css" media="screen" />
<!--Wp-Thumbie Style Sheet-->
<?php
}


global $wp_version;
if ( version_compare( $wp_version, '2.8alpha', '>' ) )
	add_filter( 'plugin_row_meta', 'crp_plugin_actions', 10, 2 ); // only 2.8 and higher
else add_filter( 'plugin_action_links', 'crp_plugin_actions', 10, 2 );

add_action('wp_head','add_css'); // CSS for Related post 
?>