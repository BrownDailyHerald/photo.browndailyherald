<?php
/*
Plugin Name: Glam Slider
Plugin URI: http://slidervilla.com/glam/
Description: Glam Slider adds a horizontal content and image slideshow with customizable background and slide intervals to any location of your blog.
Version: 1.1.1	
Author: SliderVilla
Author URI: http://slidervilla.com/
Wordpress version supported: 3.5 and above
*/
/*  Copyright 2010-2014  Slider Villa  (email : tedeshpa@gmail.com)
*/
//defined global variables and constants here
global $glam_slider,$default_glam_slider_settings,$glam_db_version;
$glam_slider = get_option('glam_slider_options');
$glam_db_version='1.1.1'; //current version of glam slider database
$default_glam_slider_settings = array('speed'=>'4', 
        'time'=>'6',
	'no_posts'=>'12',
	'visible'=>'1',
	'bg_color'=>'#ffffff', 
	'height'=>'380',
	'width'=>'960',
	'iwidth'=>'700',
	'swidth'=>'110',
	's_opacity'=>'0.4',
	'padding'=>'10',
	'border'=>'1',
	'brcolor'=>'#cccccc',
	'prev_next'=>'0',
	'goto_slide'=>'1',
	'title_text'=>'Featured Articles',
	'title_from'=>'0',
	'title_font'=>'cursive',
	'title_fontg'=>'Squada+One',
	'title_fsize'=>'22',
	'title_fstyle'=>'normal',
	'title_fcolor'=>'#bbbbbb',
	'ptitle_font'=>'\'Century Gothic\',\'Avant Garde\',sans-serif',
	'ptitle_fontg'=>'Oswald',
	'ptitle_fsize'=>'16',
	'ptitle_fstyle'=>'normal',
	'ptitle_fcolor'=>'#ffffff',
	'img_border'=>'0',
	'img_brcolor'=>'#bbbbbb',
	'show_content'=>'1',
	'content_font'=>'Verdana,Geneva,sans-serif',
	'content_fontg'=>'Open+Sans',
	'content_fsize'=>'12',
	'content_fstyle'=>'normal',
	'content_fcolor'=>'#ffffff',
	'content_from'=>'content',
	'content_chars'=>'',
	'content_bg'=>'#e00b56',
	'content_opacity'=>'0.75',
	'content_tran'=>'0',
	'bg'=>'0',
	'image_only'=>'0',
	'allowable_tags'=>'',
	'more'=>'Read More',
	'a_attr'=>'',
	'img_pick'=>array('1','slider_thumbnail','1','1','1','1'), //use custom field/key, name of the key, use post Featured image, pick the image attachment, attachment order,scan images
	'user_level'=>'edit_others_posts',
	'crop'=>'0',
	'easing'=>'swing',
	'autostep'=>'1',
	'multiple_sliders'=>'1',
	'content_limit'=>'30',
	'stylesheet'=>'default',
	'rand'=>'0',
	'fields'=>'',
	'support'=>'1',
	'fouc'=>'0',
	'buttons'=>'default',
	'navtop'=>'40',
	'navnum'=>'1',
	'css'=>'',
	'new'=>'1',
	'popup'=>'1',
	'cpost_slug'=>'slidervilla',
	'custom_post'=>'0',
	'preview'=>'2',
	'slider_id'=>'1',
	'catg_slug'=>'',
	'setname'=>'Set',
	'disable_preview'=>'0',
	'remove_metabox'=>array(),
	'pphoto'=>'0',
	'css_js'=>'',
	'timthumb'=>'0',
	'tribe_events_fix'=>'0',
	'active_tab'=>'0',
	'noscript'=>'This page is having a slideshow that uses Javascript. Your browser either doesn\'t support Javascript or you have it turned off. To see this page as it is meant to appear please use a Javascript enabled browser.'
			              );
define('GLAM_SLIDER_TABLE','glam_slider'); //Slider TABLE NAME
define('GLAM_SLIDER_META','glam_slider_meta'); //Meta TABLE NAME
define('GLAM_SLIDER_POST_META','glam_slider_postmeta'); //Meta TABLE NAME
define('GLAM_SLIDER_VER','1.1.1',false);//Current Version of Glam Slider
if ( ! defined( 'GLAM_SLIDER_PLUGIN_BASENAME' ) )
	define( 'GLAM_SLIDER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
if ( ! defined( 'GLAM_SLIDER_CSS_DIR' ) ){
	define( 'GLAM_SLIDER_CSS_DIR', WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'/css/skins/' );
}
if ( ! defined( 'GLAM_SLIDER_CSS_OUTER' ) )
	define( 'GLAM_SLIDER_CSS_OUTER', WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'/css/' );
// Create Text Domain For Translations
load_plugin_textdomain('glam-slider', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');

function install_glam_slider() {
	global $wpdb, $table_prefix,$glam_db_version;
	$installed_ver = get_option( "glam_db_version" );
	if( $installed_ver != $glam_db_version ) {
	$table_name = $table_prefix.GLAM_SLIDER_TABLE;
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql = "CREATE TABLE $table_name (
					id int(5) NOT NULL AUTO_INCREMENT,
					post_id int(11) NOT NULL,
					date datetime NOT NULL,
					slider_id int(5) NOT NULL DEFAULT '1',
					slide_order int(5) NOT NULL DEFAULT '0',
					UNIQUE KEY id(id)
				);";
		$rs = $wpdb->query($sql);
	}

   	$meta_table_name = $table_prefix.GLAM_SLIDER_META;
	if($wpdb->get_var("show tables like '$meta_table_name'") != $meta_table_name) {
		$sql = "CREATE TABLE $meta_table_name (
					slider_id int(5) NOT NULL AUTO_INCREMENT,
					slider_name varchar(100) NOT NULL default '',
					UNIQUE KEY slider_id(slider_id)
				);";
		$rs2 = $wpdb->query($sql);
		
		$sql = "INSERT INTO $meta_table_name (slider_id,slider_name) VALUES('1','Glam Slider');";
		$rs3 = $wpdb->query($sql);
	}
	
	$slider_postmeta = $table_prefix.GLAM_SLIDER_POST_META;
	if($wpdb->get_var("show tables like '$slider_postmeta'") != $slider_postmeta) {
		$sql = "CREATE TABLE $slider_postmeta (
					post_id int(11) NOT NULL,
					slider_id int(5) NOT NULL default '1',
					UNIQUE KEY post_id(post_id)
				);";
		$rs4 = $wpdb->query($sql);
	}
   // Glam Slider Settings and Options
   $default_slider = array();
   global $default_glam_slider_settings;
   $default_slider = $default_glam_slider_settings;
   
   	      	   $default_scounter='1';
	   $scounter=get_option('glam_slider_scounter');
	   if(!isset($scounter) or $scounter=='' or empty($scounter)){
	      update_option('glam_slider_scounter',$default_scounter);
		  $scounter=$default_scounter;
	   }
	   
	   for($i=1;$i<=$scounter;$i++){
	       if ($i==1){
		    $glam_slider_options='glam_slider_options';
		   }
		   else{
		    $glam_slider_options='glam_slider_options'.$i;
		   }
		   $glam_slider_curr=get_option($glam_slider_options);
	   				 
		   if(!$glam_slider_curr and $i==1) {
			 $glam_slider_curr = array();
		   }
		
		   if($glam_slider_curr or $i==1) {
			   foreach($default_slider as $key=>$value) {
				  if(!isset($glam_slider_curr[$key])) {
					 $glam_slider_curr[$key] = $value;
				  }
			   }
			   delete_option($glam_slider_options);	 
			   update_option($glam_slider_options,$glam_slider_curr); 
			   update_option( "glam_db_version", $glam_db_version );
			   }
		   } //end for loop
	}//end of if db version chnage
}
register_activation_hook( __FILE__, 'install_glam_slider' );
/* Added for auto update - start */
function glam_update_db_check() {
    global $glam_db_version;
    if (get_option('glam_db_version') != $glam_db_version) {
        install_glam_slider();
    }
}
add_action('plugins_loaded', 'glam_update_db_check');
/* Added for auto update - end */

require_once (dirname (__FILE__) . '/includes/glam-slider-functions.php');
require_once (dirname (__FILE__) . '/includes/sslider-get-the-image-functions.php');

//This adds the post to the slider
function glam_add_to_slider($post_id) {
global $glam_slider;

 if(isset($_POST['glam-sldr-verify']) and current_user_can( $glam_slider['user_level'] ) ) {
	global $wpdb, $table_prefix, $post;
	$table_name = $table_prefix.GLAM_SLIDER_TABLE;
	
	if( !isset($_POST['glam-slider']) and  is_post_on_any_glam_slider($post_id) ){
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		 $wpdb->query($sql);
	}
	
	if(isset($_POST['glam-slider']) and !isset($_POST['glam_slider_name'])) {
	  $slider_id = '1';
	  if(is_post_on_any_glam_slider($post_id)){
		 $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		 $wpdb->query($sql);
	  }
	  
	  if(isset($_POST['glam-slider']) and $_POST['glam-slider'] == "glam-slider" and !glam_slider($post_id,$slider_id)) {
		$dt = date('Y-m-d H:i:s');
		$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES ('$post_id', '$dt', '$slider_id')";
		$wpdb->query($sql);
	  }
	}
	if(isset($_POST['glam-slider']) and $_POST['glam-slider'] == "glam-slider" and isset($_POST['glam_slider_name'])){
	  $slider_id_arr = $_POST['glam_slider_name'];
	  $post_sliders_data = glam_ss_get_post_sliders($post_id);
	  
	  foreach($post_sliders_data as $post_slider_data){
		if(!in_array($post_slider_data['slider_id'],$slider_id_arr)) {
		  $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		  $wpdb->query($sql);
		}
	  }

		foreach($slider_id_arr as $slider_id) {
			if(!glam_slider($post_id,$slider_id)) {
				$dt = date('Y-m-d H:i:s');
				$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES ('$post_id', '$dt', '$slider_id')";
				$wpdb->query($sql);
			}
		}
	}
	
	$table_name = $table_prefix.GLAM_SLIDER_POST_META;
	if(isset($_POST['glam_display_slider']) and !isset($_POST['glam_display_slider_name'])) {
	  $slider_id = '1';
	}
	if(isset($_POST['glam_display_slider']) and isset($_POST['glam_display_slider_name'])){
	  $slider_id = $_POST['glam_display_slider_name'];
	}
	if(isset($_POST['glam_display_slider'])){	
		  if(!glam_ss_post_on_slider($post_id,$slider_id)) {
			$sql = "DELETE FROM $table_name where post_id = '$post_id'";
			$wpdb->query($sql);
			$sql = "INSERT INTO $table_name (post_id, slider_id) VALUES ('$post_id', '$slider_id')";
			$wpdb->query($sql);
		  }
	}
	$thumbnail_key = $glam_slider['img_pick'][1];
	$glam_sslider_thumbnail = get_post_meta($post_id,$thumbnail_key,true);
	$post_slider_thumbnail=$_POST['glam_sslider_thumbnail'];
	if($glam_sslider_thumbnail != $post_slider_thumbnail ) {
	  update_post_meta($post_id, $thumbnail_key, $_POST['glam_sslider_thumbnail']);	
	}
	
	$glam_link_attr = get_post_meta($post_id,'glam_link_attr',true);
	$link_attr=html_entity_decode($_POST['glam_link_attr'],ENT_QUOTES);
	if($glam_link_attr != $link_attr) {
	  update_post_meta($post_id, 'glam_link_attr', $link_attr);	
	}
	
	$glam_sslider_link = get_post_meta($post_id,'glam_slide_redirect_url',true);
	$link=$_POST['glam_sslider_link'];
	if($glam_sslider_link != $link) {
	  update_post_meta($post_id, 'glam_slide_redirect_url', $link);	
	}
	
	$glam_sslider_nolink = get_post_meta($post_id,'glam_sslider_nolink',true);
	$post_glam_sslider_nolink = $_POST['glam_sslider_nolink'];
	if($glam_sslider_nolink != $post_glam_sslider_nolink) {
	  update_post_meta($post_id, 'glam_sslider_nolink', $_POST['glam_sslider_nolink']);	
	}
	/* Added for embed shortcode - start */
	$glam_disable_image = get_post_meta($post_id,'_glam_disable_image',true);
	$post_glam_disable_image = $_POST['glam_disable_image'];
	if($glam_disable_image != $post_glam_disable_image) {
	  update_post_meta($post_id, '_glam_disable_image', $post_glam_disable_image);	
	}
	$glam_sslider_eshortcode = get_post_meta($post_id,'_glam_embed_shortcode',true);
	$post_glam_sslider_eshortcode = $_POST['glam_sslider_eshortcode'];
	if($glam_sslider_eshortcode != $post_glam_sslider_eshortcode) {
	  update_post_meta($post_id, '_glam_embed_shortcode', $post_glam_sslider_eshortcode);	
	}
	/* Added for embed shortcode -end */
	$glam_select_set = get_post_meta($post_id,'_glam_select_set',true);
	$post_glam_select_set = $_POST['glam_select_set'];
	if($glam_select_set != $post_glam_select_set and $post_glam_select_set!='0') {
	  update_post_meta($post_id, '_glam_select_set', $post_glam_select_set);	
	}

  } //glam-sldr-verify
}

//Removes the post from the slider, if you uncheck the checkbox from the edit post screen
function glam_remove_from_slider($post_id) {
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.GLAM_SLIDER_TABLE;
	
	// authorization
	if (!current_user_can('edit_post', $post_id))
		return $post_id;
	// origination and intention
	if (!wp_verify_nonce($_POST['glam-sldr-verify'], 'GlamSlider'))
		return $post_id;
	
    if(empty($_POST['glam-slider']) and is_post_on_any_glam_slider($post_id)) {
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		$wpdb->query($sql);
	}
	
	$display_slider = $_POST['glam_display_slider'];
	$table_name = $table_prefix.GLAM_SLIDER_POST_META;
	if(empty($display_slider) and glam_ss_slider_on_this_post($post_id)){
	  $sql = "DELETE FROM $table_name where post_id = '$post_id'";
		    $wpdb->query($sql);
	}
} 
  
function glam_delete_from_slider_table($post_id){
    global $wpdb, $table_prefix;
	$table_name = $table_prefix.GLAM_SLIDER_TABLE;
    if(is_post_on_any_glam_slider($post_id)) {
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		$wpdb->query($sql);
	}
	$table_name = $table_prefix.GLAM_SLIDER_POST_META;
    if(glam_ss_slider_on_this_post($post_id)) {
		$sql = "DELETE FROM $table_name where post_id = '$post_id'";
		$wpdb->query($sql);
	}
}

// Slider checkbox on the admin page

function glam_slider_edit_custom_box(){
   glam_add_to_slider_checkbox();
}

function glam_slider_add_custom_box() {
 global $glam_slider;
 if (current_user_can( $glam_slider['user_level'] )) {
	if( function_exists( 'add_meta_box' ) ) {
	    $post_types=get_post_types(); 
		if (isset ($glam_slider['remove_metabox'])) $remove_post_type_arr=$glam_slider['remove_metabox'];
		if(!isset($remove_post_type_arr) or !is_array($remove_post_type_arr) ) $remove_post_type_arr=array();
		foreach($post_types as $post_type) {
			if(!in_array($post_type,$remove_post_type_arr)){
				add_meta_box( 'glam_slider_box', __( 'Glam Slider' , 'glam-slider'), 'glam_slider_edit_custom_box', $post_type, 'advanced' );
			}
		}
		//add_meta_box( $id,   $title,     $callback,   $page, $context, $priority ); 
	} 
 }
}
/* Use the admin_menu action to define the custom boxes */
add_action('admin_menu', 'glam_slider_add_custom_box');

function glam_add_to_slider_checkbox() {
	global $post, $glam_slider;
	if (current_user_can( $glam_slider['user_level'] )) {
		$extra = "";
		
		$post_id = $post->ID;
		
		if(isset($post->ID)) {
			$post_id = $post->ID;
			if(is_post_on_any_glam_slider($post_id)) { $extra = 'checked="checked"'; }
		} 
		
		$post_slider_arr = array();
		
		$post_sliders = glam_ss_get_post_sliders($post_id);
		if($post_sliders) {
			foreach($post_sliders as $post_slider){
			   $post_slider_arr[] = $post_slider['slider_id'];
			}
		}
		
		$sliders = glam_ss_get_sliders();
		$thumbnail_key = $glam_slider['img_pick'][1];
		$glam_sslider_thumbnail= get_post_meta($post_id, $thumbnail_key, true); 
		$glam_sslider_link= get_post_meta($post_id, 'glam_slide_redirect_url', true);  
		$glam_sslider_nolink=get_post_meta($post_id, 'glam_sslider_nolink', true);
		$glam_link_attr=get_post_meta($post_id, 'glam_link_attr', true);
		$glam_disable_image=get_post_meta($post_id, '_glam_disable_image', true);
		$glam_embed_shortcode=get_post_meta($post_id, '_glam_embed_shortcode', true);
		$glam_select_set=get_post_meta($post_id, '_glam_select_set', true);
?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			jQuery("#glam_basic").css({"background":"#222222","color":"#ffffff"});
			jQuery("#glam_basic").on("click", function(){ 
				jQuery("#glam_basic_tab").fadeIn("fast");
				jQuery("#glam_advaced_tab").fadeOut("fast");
				jQuery(this).css({"background":"#222222","color":"#ffffff"});
				jQuery("#glam_advanced").css({"background":"buttonface","color":"#222222"});
			});
			jQuery("#glam_advanced").on("click", function(){
				jQuery("#glam_basic_tab").fadeOut("fast");
				jQuery("#glam_advaced_tab").fadeIn("fast");
				jQuery(this).css({"background":"#222222","color":"#ffffff"});
				jQuery("#glam_basic").css({"background":"buttonface","color":"#222222"});
				
			});
		}); 
		</script>
		
		<div style="border-bottom: 1px solid #ccc;padding-bottom: 0;padding-left: 10px;">
		<button type="button" id="glam_basic" style="padding:5px 30px 5px 30px;margin: 0;cursor:pointer;border:0;outline:none;">Basic</button>
		<button type="button" id="glam_advanced" style="padding:5px 30px 5px 30px;margin:0 0 0 10px;cursor:pointer;border:0;outline:none">Advanced</button>
		</div>
		<div id="glam_basic_tab">
		<div class="slider_checkbox">
		<table class="form-table">
				
				<tr valign="top">
				<th scope="row"><input type="checkbox" class="sldr_post" name="glam-slider" value="glam-slider" <?php echo $extra;?> />
				<label for="glam-slider"><?php _e('Add this post/page to','glam-slider'); ?> </label></th>
				<td><select name="glam_slider_name[]" multiple="multiple" size="3" >
                <?php foreach ($sliders as $slider) { ?>
                  <option value="<?php echo $slider['slider_id'];?>" <?php if(in_array($slider['slider_id'],$post_slider_arr)){echo 'selected';} ?>><?php echo $slider['slider_name'];?></option>
                <?php } ?>
                </select>
				<input type="hidden" name="glam-sldr-verify" id="glam-sldr-verify" value="<?php echo wp_create_nonce('GlamSlider');?>" />
				</td>
				</tr>
				<tr valign="top">
			<th scope="row"><label for="glam_sslider_link"><?php _e('Slide Link URL ','glam-slider'); ?></label></th>
			<td><input type="text" name="glam_sslider_link" class="glam_sslider_link" value="<?php echo $glam_sslider_link;?>" size="50" /><br /><small><?php _e('If left empty, it will be by default linked to the permalink.','glam-slider'); ?></small> </td>
			</tr>
			<tr valign="top">
			<th scope="row"><label for="glam_sslider_nolink"><?php _e('Do not link this slide to any page(url)','glam-slider'); ?> </label></th>
			<td><input type="checkbox" name="glam_sslider_nolink" class="glam_sslider_nolink" value="1" <?php if($glam_sslider_nolink=='1'){echo "checked";}?>  /></td>
			</tr>
			</table>
			</div>
		</div>
		<div id="glam_advaced_tab" style="display:none;">
		<?php
		$scounter=get_option('glam_slider_scounter');
		 $settingset_html='<option value="0" selected >Select the Settings</option>';
		  for($i=1;$i<=$scounter;$i++) { 
			 if($i==$glam_select_set){$selected = 'selected';} else{$selected='';}
			   if($i==1){
			     $settings='Default Settings';
				 $settingset_html =$settingset_html.'<option value="1" '.$selected.'>'.$settings.'</option>';
			   }
			   else{
				  if($settings_set=get_option('glam_slider_options'.$i))
					$settingset_html =$settingset_html.'<option value="'.$i.'" '.$selected.'>'.$settings_set['setname'].' (ID '.$i.')</option>';
			   }
		  } 
		?>
		<div class="slider_checkbox">
		<table class="form-table">
		
         <?php if($glam_slider['multiple_sliders'] == '1') {?>
                <tr valign="top">
				<th scope="row">				
				<label for="glam_display_slider"><?php _e('Display ','glam-slider'); ?></label>
				<select name="glam_display_slider_name">
                <?php foreach ($sliders as $slider) { ?>
                  <option value="<?php echo $slider['slider_id'];?>" <?php if(glam_ss_post_on_slider($post_id,$slider['slider_id'])){echo 'selected';} ?>><?php echo $slider['slider_name'];?></option>
                <?php } ?>
                </select> 
				<?php _e('on this Post/Page','glam-slider'); ?></th>
				<td><input type="checkbox" class="sldr_post" name="glam_display_slider" value="1" <?php if(glam_ss_slider_on_this_post($post_id)){echo "checked";}?> /> 
				<?php _e('(Add the ','glam-slider'); ?><a href="http://guides.slidervilla.com/glam-slider/template-tags/simple-template-tag/" target="_blank"><?php _e('Glam Slider template tag','glam-slider'); ?></a> <?php _e('manually on your page.php/single.php or another page template file)','glam-slider'); ?></td>
				</tr>

				<tr valign="top">
                <th scope="row"><label for="glam_setting_set"><?php _e('Select Settings to Apply ','glam-slider'); ?></label></th>
                <td><select id="glam_select_set" name="glam_select_set"><?php echo $settingset_html;?></select></td>
		</tr>
                
          <?php } ?>
	    </div>
        <div>
			<tr valign="top">
			<th scope="row"><label for="glam_sslider_thumbnail"><?php _e('Custom Thumbnail Image(url)','glam-slider'); ?></label></th>
			<td><input type="text" name="glam_sslider_thumbnail" class="glam_sslider_thumbnail" value="<?php echo $glam_sslider_thumbnail;?>" size="50" /></td>
			</tr>
			
			<tr valign="top">
			<th scope="row"><label for="glam_link_attr"><?php _e('Slide Link (anchor) attributes ','glam-slider'); ?></label></th>
			<td><input type="text" name="glam_link_attr" class="glam_link_attr" value="<?php echo htmlentities($glam_link_attr,ENT_QUOTES);?>" size="50" /><br /><small><?php _e('e.g. target="_blank" rel="external nofollow"','glam-slider'); ?></small></td>
			</tr>
			<!-- Added for disable thumbnail image - Start -->
			<tr valign="top">
			<th scope="row"><label for="glam_disable_image"><?php _e('Disable Thumbnail Image','glam-slider'); ?> </label></th>
			<td><input type="checkbox" name="glam_disable_image" value="1" <?php if($glam_disable_image=='1'){echo "checked";}?>  /> </td>
			</tr>
			<!-- Added for disable thumbnail image - end -->

			<!-- Added for embed shortcode - Start -->
			<tr valign="top">
			<th scope="row"><label for="embed_shortcode"><?php _e('Embed Shortcode','glam-slider'); ?> </label><br><br><div style="font-weight:normal;border:1px dashed #ccc;padding:5px;color:#666;line-height:20px;font-size:13px;">You can embed any type of shortcode e.g video shortcode or button shortcode which you want to be overlaid on the slide.</div></th>
			<td><textarea rows="4" cols="50" name="glam_sslider_eshortcode"><?php echo htmlentities( $glam_embed_shortcode, ENT_QUOTES);?></textarea></td>
			</tr>
			<!-- Added for video for embed shortcode - end -->

			</table>
		</div>
	</div>		
				
<?php }
}

//CSS for the checkbox on the admin page
function glam_slider_checkbox_css() {
?><style type="text/css" media="screen">.slider_checkbox{margin: 5px 0 10px 0;padding:3px;font-weight:bold;}.slider_checkbox input,.slider_checkbox select{font-weight:bold;}.slider_checkbox label,.slider_checkbox input,.slider_checkbox select{vertical-align:top;}</style>
<?php
}

add_action('publish_post', 'glam_add_to_slider');
add_action('publish_page', 'glam_add_to_slider');
add_action('edit_post', 'glam_add_to_slider');
add_action('publish_post', 'glam_remove_from_slider');
add_action('edit_post', 'glam_remove_from_slider');
add_action('deleted_post','glam_delete_from_slider_table');

add_action('edit_attachment', 'glam_add_to_slider');
add_action('delete_attachment','glam_delete_from_slider_table');

function glam_slider_plugin_url( $path = '' ) {
	global $wp_version;
	if ( version_compare( $wp_version, '2.8', '<' ) ) { // Using WordPress 2.7
		$folder = dirname( plugin_basename( __FILE__ ) );
		if ( '.' != $folder )
			$path = path_join( ltrim( $folder, '/' ), $path );

		return plugins_url( $path );
	}
	return plugins_url( $path, __FILE__ );
}

function glam_get_string_limit($output, $max_char)
{
    $output = str_replace(']]>', ']]&gt;', $output);
    $output = strip_tags($output);

  	if ((strlen($output)>$max_char) && ($espacio = strpos($output, " ", $max_char )))
	{
        $output = substr($output, 0, $espacio).'...';
		return $output;
   }
   else
   {
      return $output;
   }
}

function glam_slider_get_first_image($post) {
	$first_img = '';
	ob_start();
	ob_end_clean();
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
	$first_img = $matches [1] [0];
	return $first_img;
}
add_filter( 'plugin_action_links', 'glam_sslider_plugin_action_links', 10, 2 );

function glam_sslider_plugin_action_links( $links, $file ) {
	if ( $file != GLAM_SLIDER_PLUGIN_BASENAME )
		return $links;

	$url = glam_sslider_admin_url( array( 'page' => 'glam-slider-settings' ) );

	$settings_link = '<a href="' . esc_attr( $url ) . '">'
		. esc_html( __( 'Settings') ) . '</a>';

	array_unshift( $links, $settings_link );

	return $links;
}

//New Custom Post Type
if( $glam_slider['custom_post'] == '1' and !post_type_exists('slidervilla') ){
	add_action( 'init', 'glam_post_type', 11 );
	function glam_post_type() {
		global $glam_slider;
			$labels = array(
			'name' => _x('SliderVilla Slides', 'post type general name'),
			'singular_name' => _x('SliderVilla Slide', 'post type singular name'),
			'add_new' => _x('Add New', 'glam'),
			'add_new_item' => __('Add New SliderVilla Slide'),
			'edit_item' => __('Edit SliderVilla Slide'),
			'new_item' => __('New SliderVilla Slide'),
			'all_items' => __('All SliderVilla Slides'),
			'view_item' => __('View SliderVilla Slide'),
			'search_items' => __('Search SliderVilla Slides'),
			'not_found' =>  __('No SliderVilla slides found'),
			'not_found_in_trash' => __('No SliderVilla slides found in Trash'), 
			'parent_item_colon' => '',
			'menu_name' => 'SliderVilla Slides'

			);
			$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_menu' => true, 
			'query_var' => true,
			'rewrite' => array('slug' => $glam_slider['cpost_slug'],'with_front' => false),
			'capability_type' => 'post',
			'has_archive' => true, 
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title','editor','thumbnail','excerpt','custom-fields')
			); 
			register_post_type('slidervilla',$args);
	}

	//add filter to ensure the text SliderVilla, or slidervilla, is displayed when user updates a slidervilla 
	add_filter('post_updated_messages', 'glam_updated_messages');
	function glam_updated_messages( $messages ) {
	  global $post, $post_ID;

	  $messages['glam'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('SliderVilla Slide updated. <a href="%s">View SliderVilla slide</a>'), esc_url( get_permalink($post_ID) ) ),
		2 => __('Custom field updated.'),
		3 => __('Custom field deleted.'),
		4 => __('SliderVilla Slide updated.'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('SliderVilla Slide restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('SliderVilla Slide published. <a href="%s">View SliderVilla slide</a>'), esc_url( get_permalink($post_ID) ) ),
		7 => __('Glam saved.'),
		8 => sprintf( __('SliderVilla Slide submitted. <a target="_blank" href="%s">Preview SliderVilla slide</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		9 => sprintf( __('SliderVilla Slides scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview SliderVilla slide</a>'),
		  // translators: Publish box date format, see http://php.net/date
		  date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
		10 => sprintf( __('SliderVilla Slide draft updated. <a target="_blank" href="%s">Preview SliderVilla slide</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	  );

	  return $messages;
	}
} //if custom_post is true

require_once (dirname (__FILE__) . '/slider_versions/glam_1.php');
require_once (dirname (__FILE__) . '/settings/settings.php');
require_once (dirname (__FILE__) . '/includes/media-images.php');

// Load the update-notification class
add_action('init', 'glam_update_notification');
function glam_update_notification(){
    require_once (dirname (__FILE__) . '/includes/upgrade.php');
    $glam_upgrade_remote_path = 'http://support.slidervilla.com/sv-updates/glam.php';
    new glam_update_class ( GLAM_SLIDER_VER, $glam_upgrade_remote_path, GLAM_SLIDER_PLUGIN_BASENAME );
}
?>
