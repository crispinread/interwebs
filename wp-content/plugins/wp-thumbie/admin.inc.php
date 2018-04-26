<?php
/**********************************************************************
*					Admin Page										*
*********************************************************************/
if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

if (!defined('CRP_LOCAL_NAME')) define('CRP_LOCAL_NAME', 'better-search');

// Pre-2.6 compatibility
if ( !defined('WP_CONTENT_URL') )
	define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if ( !defined('WP_CONTENT_DIR') )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
// Guess the location
$crp_path = WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__));
$crp_url = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__));

//Thumbnail Image Cache Folder Name
//echo $crp_path.'/cache';

function crp_options() {

    global $wpdb;
    $poststable = $wpdb->posts;

    $crp_settings = crp_read_options();

	if($_POST['crp_save']){
		$crp_settings[title] = ($_POST['title']);
		$crp_settings[limit] = ((is_numeric($_POST['limit'])) ? ($_POST['limit']) : 5);
		$crp_settings[exclude_cat_slugs] = ($_POST['exclude_cat_slugs']);
		$crp_settings[add_to_content] = (($_POST['add_to_content']) ? true : false);
		$crp_settings[add_to_page] = (($_POST['add_to_page']) ? true : false);
		$crp_settings[add_to_feed] = (($_POST['add_to_feed']) ? true : false);
		$crp_settings[match_content] = (($_POST['match_content']) ? true : false);
		$crp_settings[exclude_pages] = (($_POST['exclude_pages']) ? true : false);
		$crp_settings[blank_output] = (($_POST['blank_output'] == 'blank' ) ? true : false);
		/* Parameters for Image Manipulation */
		$crp_settings[width] = ((is_numeric($_POST['width'])) ? ($_POST['width']) : 100);
		$crp_settings[height] = ((is_numeric($_POST['height'])) ? ($_POST['height']) : 100);
		$crp_settings[post_text_char] = ((is_numeric($_POST['post_text_char'])) ? ($_POST['post_text_char']) : 100);
		$crp_settings[related_post_style]= ($_POST['related_post_style']);
		$crp_settings[display_post_text]=(($_POST['display_post_text']) ? true : false);
		
		$exclude_categories_slugs = explode(", ",$crp_settings[exclude_cat_slugs]);
		
		$exclude_categories = '';
		foreach ($exclude_categories_slugs as $exclude_categories_slug) {
			$catObj = get_category_by_slug($exclude_categories_slug);
			$exclude_categories .= $catObj->term_id . ',';
		}
		$crp_settings[exclude_categories] = substr($exclude_categories, 0, -2);

		update_option('ald_crp_settings', $crp_settings);
		
		$str = '<div id="message" class="updated fade"><p>'. __('Options saved successfully.',CRP_LOCAL_NAME) .'</p></div>';
		echo $str;
	}
	
	if ($_POST['crp_default']){
		delete_option('ald_crp_settings');
		$crp_settings = crp_default_options();
		update_option('ald_crp_settings', $crp_settings);
		
		$str = '<div id="message" class="updated fade"><p>'. __('Options set to Default.',CRP_LOCAL_NAME) .'</p></div>';
		echo $str;
	}
	if ($_POST['crp_recreate']){
		$sql = "ALTER TABLE $poststable DROP INDEX crp_related";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $poststable DROP INDEX crp_related_title";
		$wpdb->query($sql);
		
		$sql = "ALTER TABLE $poststable DROP INDEX crp_related_content";
		$wpdb->query($sql);
		
		ald_crp_activate();
		
		$str = '<div id="message" class="updated fade"><p>'. __('Index recreated',CRP_LOCAL_NAME) .'</p></div>';
		echo $str;
	}
?>

<div class="wrap">
  <h2>Wp-Thumbie (Thumbnail Related Posts)</h2>
 <!-- <div style="border: #ccc 1px solid; padding: 10px">
    <fieldset class="options">
    <legend>
    <h3>
      <?php _e('Cache Folder',CRP_LOCAL_NAME); ?>
    </h3>
    </legend>
    <p>
    <label>
     <?php //_e('Thumbnail Image Cache Folder Size: ',CRP_LOCAL_NAME); ?>
     <?php //$dirsize = GetFolderSize('cache'); echo $dirsize[size];//Cache Directory file size ?>
     </label>
   </p>
    </fieldset>
  </div> -->
  <?php php_gd_test(); ?>
  <?php //cache_file_perm(); ?>
  <form method="post" id="crp_options" name="crp_options" style="border: #ccc 1px solid; padding: 10px" onsubmit="return checkForm()">
    <fieldset class="options">
    <legend>
    <h3>
      <?php _e('Options:',CRP_LOCAL_NAME); ?>
    </h3>
    </legend>
    <p>
      <label>
      <?php _e('Number of thumbnail related posts to display: ',CRP_LOCAL_NAME); ?>
      <input type="textbox" name="limit" id="limit" value="<?php echo attribute_escape(stripslashes($crp_settings[limit])); ?>">
      </label>
    </p>
    <!-- User Input for Image size -->
    <p>
      <label>
      <?php _e('Image Thumbnail width in px: ',CRP_LOCAL_NAME); ?>
      <input type="textbox" name="width" id="width" value="<?php echo attribute_escape(stripslashes($crp_settings[width])); ?>">
      </label>
    </p>
    <p>
      <label>
      <?php _e('Image Thumbnail height in px: ',CRP_LOCAL_NAME); ?>
      <input type="textbox" name="height" id="height" value="<?php echo attribute_escape(stripslashes($crp_settings[height])); ?>">
      </label>
    </p>
    <p>
      <label>
      <?php _e('Title of related posts: ',CRP_LOCAL_NAME); ?>
      <input type="textbox" name="title" id="title" value="<?php echo attribute_escape(stripslashes($crp_settings[title])); ?>">
      </label>
    </p>
    <p><?php _e('Exclude Categories: ',CRP_LOCAL_NAME); ?></p>
	<div style="position:relative;text-align:left">
		<table id="MYCUSTOMFLOATER" class="myCustomFloater" style="position:absolute;top:50px;left:0;background-color:#cecece;display:none;visibility:hidden">
		<tr><td><!--
				please see: http://chrisholland.blogspot.com/2004/09/geekstuff-css-display-inline-block.html
				to explain why i'm using a table here.
				You could replace the table/tr/td with a DIV, but you'd have to specify it's width and height
				-->
			<div class="myCustomFloaterContent">
			you should never be seeing this
			</div>
		</td></tr>
		</table>
		<textarea class="wickEnabled:MYCUSTOMFLOATER" cols="50" rows="3" wrap="virtual" name="exclude_cat_slugs"><?php echo (stripslashes($crp_settings[exclude_cat_slugs])); ?></textarea>
	</div>
	<p><?php _e('When there are no posts, what should be shown?',CRP_LOCAL_NAME); ?><br />
		<label>
		<input type="radio" name="blank_output" value="blank" id="blank_output_0" <?php if ($crp_settings['blank_output']) echo 'checked="checked"' ?> />
		<?php _e('Blank Output',CRP_LOCAL_NAME); ?></label>
		<br />
		<label>
		<input type="radio" name="blank_output" value="noposts" id="blank_output_1" <?php if (!$crp_settings['blank_output']) echo 'checked="checked"' ?> />
		<?php _e('Display "No Related Posts"',CRP_LOCAL_NAME); ?></label>
		<br />
	</p>
	<!-- Image Related Post Style user input -->
	<p><?php _e('Related Post Display Style',CRP_LOCAL_NAME); ?><br />
	<!-- <label>
	<input type="radio" name="related_post_style" value="1" id="format_output_0" <?php //if ($crp_settings['related_post_style'] == '1' ) echo 'checked="checked"' ?> />
	<?php //_e('Related Post in Horizontal Format',CRP_LOCAL_NAME); ?></label> 
	<br /> -->
	<label>
	<input type="radio" name="related_post_style" value="2" id="format_output_1" <?php if($crp_settings['related_post_style'] == '2') echo 'checked="checked"' ?> />
	<?php _e('Related Post in Verticle Format',CRP_LOCAL_NAME); ?></label>
	<br />
	<label>
	<input type="radio" name="related_post_style" value="3" id="format_output_3" <?php if($crp_settings['related_post_style'] == '3') echo 'checked="checked"' ?> />
		<?php _e('Related Post in Raw Format (<code>&lt;ul&gt; &lt;li&gt;</code>)',CRP_LOCAL_NAME); ?></label>
		<br />
	</p>
    <p>
      <label>
      <input type="checkbox" name="display_post_text" id="display_post_text" onclick="document.getElementById('info').style.visibility = this.checked ? 'visible' : 'hidden'" <?php if ($crp_settings[display_post_text]) echo 'checked="checked"' ?> />
      <?php _e('Display Post Excerpt',CRP_LOCAL_NAME); ?>
      </label>
    </p>
    <div id="info" <?php if (!$crp_settings[display_post_text]) echo 'style="visibility:hidden"' ?> >
    <p>
      <label>
      <?php _e('Number of Characters to Display for Post Text: ',CRP_LOCAL_NAME); ?>
      <input type="textbox" name="post_text_char" id="post_text_char" value="<?php echo attribute_escape(stripslashes($crp_settings[post_text_char])); ?>">
      </label>
     </p>
     </div>
    <p>
      <label>
      <input type="checkbox" name="add_to_content" id="add_to_content" <?php if ($crp_settings[add_to_content]) echo 'checked="checked"' ?> />
      <?php _e('Add related posts to the post content on single posts. <br />If you choose to disable this, please add <code>&lt;?php if(function_exists(\'wp_thumbie\')) wp_thumbie(); ?&gt;</code> to your template file where you want it displayed',CRP_LOCAL_NAME); ?>
      </label>
    </p>
    <p>
      <label>
      <input type="checkbox" name="add_to_page" id="add_to_page" <?php if ($crp_settings[add_to_page]) echo 'checked="checked"' ?> />
      <?php _e('Add related posts to pages. <br />If you choose to disable this, please add <code>&lt;?php if(function_exists(\'wp_thumbie\')) wp_thumbie(); ?&gt;</code> to your template file where you want it displayed',CRP_LOCAL_NAME); ?>
      </label>
    </p>
    <p>
      <label>
      <input type="checkbox" name="add_to_feed" id="add_to_feed" <?php if ($crp_settings[add_to_feed]) echo 'checked="checked"' ?> />
      <?php _e('Add related posts to feed (Style may not be applied properly to feeds)',CRP_LOCAL_NAME); ?>
      </label>
    </p>
    <p>
      <label>
      <input type="checkbox" name="match_content" id="match_content" <?php if ($crp_settings[match_content]) echo 'checked="checked"' ?> />
      <?php _e('Find related posts based on content as well as title. If unchecked, only posts titles are used. (I recommend using a caching plugin if you enable this)',CRP_LOCAL_NAME); ?>
      </label>
    </p>
    <p>
      <label>
      <input type="checkbox" name="exclude_pages" id="exclude_pages" <?php if ($crp_settings[exclude_pages]) echo 'checked="checked"' ?> />
      <?php _e('Exclude Pages in Related Posts',CRP_LOCAL_NAME); ?>
      </label>
    </p>
    <p>
      <input type="submit" name="crp_save" id="crp_save" value="Save Options" style="border:#0C0 1px solid" />
      <input name="crp_default" type="submit" id="crp_default" value="Default Options" style="border:#F00 1px solid" onclick="if (!confirm('<?php _e('Do you want to set options to Default?',CRP_LOCAL_NAME); ?>')) return false;" />
      <input name="crp_recreate" type="submit" id="crp_recreate" value="Recreate index" style="border:#00c 1px solid" onclick="if (!confirm('<?php _e('Are you sure you want to recreate the index?',CRP_LOCAL_NAME); ?>')) return false;" />
    </p>
    </fieldset>
  </form>
    <div style="border: #ccc 1px solid; padding: 10px">
    <fieldset class="credits">
    <legend>
    <h3>
      <?php _e('Credits',CRP_LOCAL_NAME); ?>
    </h3>
    </legend>
    <p>
      <?php _e('Wp-Thumbie WordPress plugin is based on ',CRP_LOCAL_NAME); ?>
      <a href="http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/">Contextual Related Posts</a>.
      <?php _e(' A big thanks to Ajay Dsouza for allowing us to use his plugin. Wp-Thumbie uses ',CRP_LOCAL_NAME); ?>
      <a href="http://code.google.com/p/timthumb/">TimThumb</a>
      <?php _e('php script for resizing images on fly.',CRP_LOCAL_NAME); ?>
    </p>
    </fieldset>
   </div>
</div>
<?php

}
//Php GD Library Test
function php_gd_test(){
if (function_exists("gd_info")) {
	//echo '<span style="color: #00AA00; font-weight: bold;">supported</span> by your server!</p>';
	//$gd = gd_info();     
	//foreach ($gd as $k => $v) {
		//echo '<div style="width: 340px; border-bottom: 1px solid #DDDDDD; padding: 2px;">';
		//echo '<span style="float: left;width: 300px;">' . $k . '</span> ';
		//if ($v)
			//echo '<span style="color: #00AA00; font-weight: bold;">Yes</span>';
		//else
			//echo '<span style="color: #EE0000; font-weight: bold;">No</span>';
		//echo '<div style="clear:both;"><!-- --></div></div>';
} 
else {
	echo '<p style="color: #444444; font-size: 115%;">Php GD Library is ';
	echo '<div style="margin: 10px;">';
	echo '<span style="color: #EE0000; font-weight: bold;">not supported</span> by your server! Wp-Tumbie will not work properly. Contact your Webhost.</p>';
}
echo '</div>';
}

function cache_file_perm(){

echo substr(sprintf('%o', fileperms('')), -4); 
}

//Code to get Cache directory file size
function GetFolderSize($dir,$exclusions="") {
	global $crp_url;
	$crp_url .= '/'.$dir;
	$dir = $crp_url;
  //gltr_debug("====>CALC: $dir");
	$res = array("num"=>0,"size"=>0);
  if (file_exists($dir) && is_dir($dir) && is_readable($dir)) {
  echo "inside test";
  	$files = glob($dir . '/*');
    if (is_array($files)){
      foreach($files as $path){
          if ($exclusions != "" && strpos($path,$exclusions)!==false) {
            //gltr_debug("$dir: EXCLUDING====>$item");
          	continue;
    }
          if (is_dir($path)){
          	//gltr_debug("====>Found dir: $path");
          	$rres = gltr_files_stats($path, $exclusions);
            $res["size"] += $rres["size"];
            $res["num"] += $rres["num"];
          }else if (file_exists($path) && is_file($path))
            $res["size"] += filesize($path);
            $res["num"]++;
  }
      
      }
    }
  return $res;

// open the directory, if the script cannot open the directory then return folderSize = 0
/*$dir_handle = opendir($dirname);
if(!$dir_handle) return 0;
   // traversal for every entry in the directory
   while($file = readdir($dir_handle)){
   // ignore '.' and '..' directory
      if($file  !=  "."  &&  $file  !=  "..")  {
         // if entry is directory then go recursive !
           if(is_dir($dirname."/".$file)){
              $folderSize += GetFolderSize($dirname.'/'.$file);
                // if file then accumulate the size
             } else {
               $folderSize += filesize($dirname."/".$file);
            }
         }
      }
closedir($dir_handle);  // chose the directory
        // return $dirname folder size
        return $folderSize ; */
}



function crp_adminmenu() {
	if (function_exists('current_user_can')) {
		// In WordPress 2.x
		if (current_user_can('manage_options')) {
			$crp_is_admin = true;
		}
	} else {
		// In WordPress 1.x
		global $user_ID;
		if (user_can_edit_user($user_ID, 0)) {
			$crp_is_admin = true;
		}
	}

	if ((function_exists('add_options_page'))&&($crp_is_admin)) {
		$plugin_page = add_options_page(__("Wp-Thumbie", CRP_LOCAL_NAME), __("Wp-Thumbie", CRP_LOCAL_NAME), 9, 'crp_options', 'crp_options');
		add_action( 'admin_head-'. $plugin_page, 'crp_adminhead' );
	}
	
}
add_action('admin_menu', 'crp_adminmenu');

function crp_adminhead() {
	global $crp_url;

?>
<link rel="stylesheet" type="text/css" href="<?php echo $crp_url ?>/wick/wick.css" />
<script type="text/javascript" language="JavaScript">
function checkForm() {
answer = true;
if (siw && siw.selectingSomething)
	answer = false;
return answer;
}//
</script>
<script type="text/javascript" src="<?php echo $crp_url ?>/wick/sample_data.js.php"></script>
<script type="text/javascript" src="<?php echo $crp_url ?>/wick/wick.js"></script>
<?php }

?>