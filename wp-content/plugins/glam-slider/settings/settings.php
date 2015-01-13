<?php // Hook for adding admin menus
if ( is_admin() ){ // admin actions
  add_action('admin_menu', 'glam_slider_settings');
  add_action( 'admin_init', 'register_glam_settings' ); 
} 
// function for adding settings page to wp-admin
function glam_slider_settings() {
    // Add a new submenu under Options:
	add_menu_page( 'Glam Slider', 'Glam Slider', 'manage_options','glam-slider-admin', 'glam_slider_create_multiple_sliders');
	add_submenu_page('glam-slider-admin', 'Glam Sliders', 'Sliders', 'manage_options', 'glam-slider-admin', 'glam_slider_create_multiple_sliders');
	add_submenu_page('glam-slider-admin', 'Glam Slider Settings', 'Settings', 'manage_options', 'glam-slider-settings', 'glam_slider_settings_page');
	add_submenu_page('glam-slider-admin', 'Glam Slider License Key', 'License', 'manage_options', 'glam-slider-license-key', 'glam_slider_license');
}
require_once (dirname (__FILE__) . '/sliders.php');
require_once (dirname (__FILE__) . '/license.php');

//Create Set & Export Settings
function glam_process_set_requests(){
	global $default_glam_slider_settings;
	$scounter=get_option('glam_slider_scounter');
	
	$cntr='';
	if(isset($_GET['scounter'])) $cntr = $_GET['scounter'];
	
	if(isset($_POST['create_set'])){
		if ($_POST['create_set']=='Create New Settings Set') {
		  $scounter++;
		  update_option('glam_slider_scounter',$scounter);
		  $options='glam_slider_options'.$scounter;
		  update_option($options,$default_glam_slider_settings);
		  $current_url = admin_url('admin.php?page=glam-slider-settings');
		  $current_url = add_query_arg('scounter',$scounter,$current_url);
		  wp_redirect( $current_url );
		  exit;
		}
	}

	//Export Settings
	if(isset($_POST['export'])){
		if ($_POST['export']=='Export') {
			@ob_end_clean();
			
			// required for IE, otherwise Content-Disposition may be ignored
			if(ini_get('zlib.output_compression'))
			ini_set('zlib.output_compression', 'Off');
			
			header('Content-Type: ' . "text/x-csv");
			header('Content-Disposition: attachment; filename="glam-settings-set-'.$cntr.'.csv"');
			header("Content-Transfer-Encoding: binary");
			header('Accept-Ranges: bytes');

			/* The three lines below basically make the
			download non-cacheable */
			header("Cache-control: private");
			header('Pragma: private');
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

			$exportTXT='';$i=0;
			$slider_options='glam_slider_options'.$cntr;
			$slider_curr=get_option($slider_options);
			foreach($slider_curr as $key=>$value){
				if($i>0) $exportTXT.="\n";
				if(!is_array($value)){
					$exportTXT.=$key.",".$value;
				}
				else {
					$exportTXT.=$key.',';
					$j=0;
					if($value) {
						foreach($value as $v){
							if($j>0) $exportTXT.="|";
							$exportTXT.=$v;
							$j++;
						}
					}
				}
				$i++;
			}
			$exportTXT.="\n";
			$exportTXT.="slider_name,glam";
			print($exportTXT); 
			exit();
		}
	}	
}
add_action('load-glam-slider_page_glam-slider-settings','glam_process_set_requests');

// This function displays the page content for the Glam Slider Options submenu
function glam_slider_settings_page() {
global $glam_slider,$default_glam_slider_settings;
$scounter=get_option('glam_slider_scounter');
if (isset($_GET['scounter']))$cntr = $_GET['scounter'];
else $cntr = '';

$new_settings_msg='';
/* Include settings file of each skin - strat */
$directory = GLAM_SLIDER_CSS_DIR;
if ($handle = opendir($directory)) {
    while (false !== ($file = readdir($handle))) { 
     if($file != '.' and $file != '..') { 
     	if($file!='sample')
		require_once ( dirname( dirname(__FILE__) ) . '/css/skins/'.$file.'/settings.php');
   } }
    closedir($handle);
}

/* Include settings file of each skin- end */
//Reset Settings
if (isset ($_POST['glam_reset_settings_submit'])) {
	if ( $_POST['glam_reset_settings']!='n' ) {
	  $glam_reset_settings=$_POST['glam_reset_settings'];
	  $options='glam_slider_options'.$cntr;
	  $optionsvalue=get_option($options);
	  if( $glam_reset_settings == 'g' ){
		$new_settings_value=$default_glam_slider_settings;
		$new_settings_value['setname']=isset($optionsvalue['setname'])?$optionsvalue['setname']:'Set';
		update_option($options,$new_settings_value);
	  }
	  elseif(!is_numeric($glam_reset_settings)){
		$skin=$glam_reset_settings;
		$new_settings_value=$default_glam_slider_settings;
		$skin_defaults_str='default_settings_'.$skin;
		global ${$skin_defaults_str};
		if(count(${$skin_defaults_str})>0){
			foreach(${$skin_defaults_str} as $key=>$value){
				$new_settings_value[$key]=$value;	
			}
			$new_settings_value['stylesheet']=$skin;
		}
		if(!isset($optionsvalue['setname']) or $optionsvalue['setname'] =='')
			$optionsvalue['setname']=$default_glam_slider_settings['setname'];
		$new_settings_value['setname']=$optionsvalue['setname'];		
		update_option($options,$new_settings_value);
	  }
	  else{
		if( $glam_reset_settings == '1' ){
			$new_settings_value=get_option('glam_slider_options');
			$new_settings_value['setname']=isset($optionsvalue['setname'])?$optionsvalue['setname']:'Set';
			update_option($options,	$new_settings_value );
		}
		else{
			$new_option_name='glam_slider_options'.$glam_reset_settings;
			$new_settings_value=get_option($new_option_name);
			$new_settings_value['setname']=$optionsvalue['setname'];
			update_option($options,	$new_settings_value );
		}
	  }
	}
}

//Import Settings
if (isset ($_POST['import'])) {
	if ($_POST['import']=='Import') {
		global $wpdb;
		$imported_settings_message='';
		$csv_mimetypes = array('text/csv','text/x-csv','text/plain','application/csv','text/comma-separated-values','application/excel','application/vnd.ms-excel','application/vnd.msexcel','text/anytext','application/octet-stream','application/txt');
		if ($_FILES['settings_file']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['settings_file']['tmp_name']) && in_array($_FILES['settings_file']['type'], $csv_mimetypes) ) { 
			$imported_settings=file_get_contents($_FILES['settings_file']['tmp_name']); 
			$settings_arr=explode("\n",$imported_settings);
			$slider_settings=array();
			foreach($settings_arr as $settings_field){
				$s=explode(',',$settings_field);
				$inner=explode('|',$s[1]);
				if(count($inner)>1)	$slider_settings[$s[0]]=$inner;
				else $slider_settings[$s[0]]=$s[1];
			}
			$options='glam_slider_options'.$cntr;
			
			if( $slider_settings['slider_name'] == 'glam' )	{
				update_option($options,$slider_settings);
				$new_settings_msg='<div id="message" class="updated fade" style="clear:left;"><h3>'.__('Settings imported successfully ','glam-slider').'</h3></div>';
				$imported_settings_message='<div style="clear:left;color:#006E2E;"><h3>'.__('Settings imported successfully ','glam-slider').'</h3></div>';
			}
			else {
				$new_settings_msg=$imported_settings_message='<div id="message" class="error fade" style="clear:left;"><h3>'.__('Settings imported do not match to Glam Slider Settings. Please check the file.','glam-slider').'</h3></div>';
				$imported_settings_message='<div style="clear:left;color:#ff0000;"><h3>'.__('Settings imported do not match to Glam Slider Settings. Please check the file.','glam-slider').'</h3></div>';
			}
		}
		else{
			$new_settings_msg=$imported_settings_message='<div id="message" class="error fade" style="clear:left;"><h3>'.__('Error in File, Settings not imported. Please check the file being imported. ','glam-slider').'</h3></div>';
			$imported_settings_message='<div style="clear:left;color:#ff0000;"><h3>'.__('Error in File, Settings not imported. Please check the file being imported. ','glam-slider').'</h3></div>';
		}
	}
}

//Delete Set
if (isset ($_POST['delete_set'])) {
	if ($_POST['delete_set']=='Delete this Set' and isset($cntr) and !empty($cntr)) {
	  $options='glam_slider_options'.$cntr;
	  delete_option($options);
	  $cntr='';
	}
}

$group='glam-slider-group'.$cntr;
$glam_slider_options='glam_slider_options'.$cntr;
$glam_slider_curr=get_option($glam_slider_options);
if(!isset($cntr) or empty($cntr)){$curr = 'Default';}
else{$curr = $cntr;}
foreach($default_glam_slider_settings as $key=>$value){
	if(!isset($glam_slider_curr[$key])) $glam_slider_curr[$key]='';
}
?>

<div class="wrap" style="clear:both;">
<form style="float:right;margin:10px 20px" action="" method="post">
<?php if(isset($cntr) and !empty($cntr)){ ?>
<input type="submit" class="button-primary" value="Delete this Set" name="delete_set"  onclick="return confirmSettingsDelete()" />
<?php } ?>
</form>
<h2 class="top_heading"><?php _e('Glam Slider Settings: ','glam-slider'); echo '<span>'.$curr.'</span>'; ?> </h2>
<div class="svilla_cl"></div>
<?php echo $new_settings_msg;?>
<?php 
if ($glam_slider_curr['disable_preview'] != '1'){
?>
<div id="settings_preview"><h2 class="heading"><?php _e('Preview','glam-slider'); ?></h2> 
<?php 
if ($glam_slider_curr['preview'] == "0")
	get_glam_slider($glam_slider_curr['slider_id'],$cntr);
elseif($glam_slider_curr['preview'] == "1")
	get_glam_slider_category($glam_slider_curr['catg_slug'],$cntr);
else
	get_glam_slider_recent($cntr);
?></div>
<?php } ?>

<?php echo $new_settings_msg;?>

<div id="glam_settings" >
<form method="post" action="options.php" id="glam_slider_form">
<?php settings_fields($group); ?>

<?php
if(!isset($cntr) or empty($cntr)){}
else{?>
	<table class="form-table">
		<tr valign="top">
		<th scope="row"><h3><?php _e('Setting Set Name','glam-slider'); ?></h3></th>
		<td><h3><input type="text" name="<?php echo $glam_slider_options;?>[setname]" id="glam_slider_setname" class="regular-text" value="<?php echo $glam_slider_curr['setname']; ?>" /></h3></td>
		</tr>
	</table>
<?php }
?>

<div id="slider_tabs">
        <ul class="ui-tabs">
            <li class="green"><a href="#basic">Basic</a></li>
            <li class="blue"><a href="#slider_content">Slider Content</a></li>
            <li class="pink"><a href="#slider_nav">Navigation</a></li>
	    <li class="orange"><a href="#preview">Preview</a></li>
	    <li class="asbestos"><a href="#cssvalues">Generated CSS</a></li>
        </ul>

<div id="basic">
<div class="sub_settings toggle_settings">
<h2 class="sub-heading"><?php _e('Basic Settings','glam-slider'); ?><img src="<?php echo glam_slider_plugin_url( 'images/close.png' ); ?>" id="minmax_img" class="toggle_img"></h2> 
<p><?php _e('Customize the looks of the Slider box wrapping the content slides from here','glam-slider'); ?></p> 

<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Select Skin','glam-slider'); ?> </th>
<td><select name="<?php echo $glam_slider_options;?>[stylesheet]" id="glam_stylesheet" onchange="return checkskin(this.value);">
<?php 
$directory = GLAM_SLIDER_CSS_DIR;
if ($handle = opendir($directory)) {
    while (false !== ($file = readdir($handle))) { 
     if($file != '.' and $file != '..') { ?>
      <option value="<?php echo $file;?>" <?php if ($glam_slider_curr['stylesheet'] == $file){ echo "selected";}?> ><?php echo $file;?></option>
 <?php  } }
    closedir($handle);
}
?>
</select>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Slide Easing Effect','glam-slider'); ?></th>
<td><select name="<?php echo $glam_slider_options;?>[easing]" >
<option value="swing" <?php if ($glam_slider_curr['easing'] == "swing"){ echo "selected";}?> ><?php _e('swing','glam-slider'); ?></option>
<option value="easeInQuad" <?php if ($glam_slider_curr['easing'] == "easeInQuad"){ echo "selected";}?> ><?php _e('easeInQuad','glam-slider'); ?></option>
<option value="easeOutQuad" <?php if ($glam_slider_curr['easing'] == "easeOutQuad"){ echo "selected";}?> ><?php _e('easeOutQuad','glam-slider'); ?></option>
<option value="easeInOutQuad" <?php if ($glam_slider_curr['easing'] == "easeInOutQuad"){ echo "selected";}?> ><?php _e('easeInOutQuad','glam-slider'); ?></option>
<option value="easeInCubic" <?php if ($glam_slider_curr['easing'] == "easeInCubic"){ echo "selected";}?> ><?php _e('easeInCubic','glam-slider'); ?></option>
<option value="easeOutCubic" <?php if ($glam_slider_curr['easing'] == "easeOutCubic"){ echo "selected";}?> ><?php _e('easeOutCubic','glam-slider'); ?></option>
<option value="easeInOutCubic" <?php if ($glam_slider_curr['easing'] == "easeInOutCubic"){ echo "selected";}?> ><?php _e('easeInOutCubic','glam-slider'); ?></option>
<option value="easeInQuart" <?php if ($glam_slider_curr['easing'] == "easeInQuart"){ echo "selected";}?> ><?php _e('easeInQuart','glam-slider'); ?></option>
<option value="easeOutQuart" <?php if ($glam_slider_curr['easing'] == "easeOutQuart"){ echo "selected";}?> ><?php _e('easeOutQuart','glam-slider'); ?></option>
<option value="easeInOutQuart" <?php if ($glam_slider_curr['easing'] == "easeInOutQuart"){ echo "selected";}?> ><?php _e('easeInOutQuart','glam-slider'); ?></option>
<option value="easeInQuint" <?php if ($glam_slider_curr['easing'] == "easeInQuint"){ echo "selected";}?> ><?php _e('easeInQuint','glam-slider'); ?></option>
<option value="easeOutQuint" <?php if ($glam_slider_curr['easing'] == "easeOutQuint"){ echo "selected";}?> ><?php _e('easeOutQuint','glam-slider'); ?></option>
<option value="easeInOutQuint" <?php if ($glam_slider_curr['easing'] == "easeInOutQuint"){ echo "selected";}?> ><?php _e('easeInOutQuint','glam-slider'); ?></option>
<option value="easeInSine" <?php if ($glam_slider_curr['easing'] == "easeInSine"){ echo "selected";}?> ><?php _e('easeInSine','glam-slider'); ?></option>
<option value="easeOutSine" <?php if ($glam_slider_curr['easing'] == "easeOutSine"){ echo "selected";}?> ><?php _e('easeOutSine','glam-slider'); ?></option>
<option value="easeInOutSine" <?php if ($glam_slider_curr['easing'] == "easeInOutSine"){ echo "selected";}?> ><?php _e('easeInOutSine','glam-slider'); ?></option>
<option value="easeInExpo" <?php if ($glam_slider_curr['easing'] == "easeInExpo"){ echo "selected";}?> ><?php _e('easeInExpo','glam-slider'); ?></option>
<option value="easeOutExpo" <?php if ($glam_slider_curr['easing'] == "easeOutExpo"){ echo "selected";}?> ><?php _e('easeOutExpo','glam-slider'); ?></option>
<option value="easeInOutExpo" <?php if ($glam_slider_curr['easing'] == "easeInOutExpo"){ echo "selected";}?> ><?php _e('easeInOutExpo','glam-slider'); ?></option>
<option value="easeInCirc" <?php if ($glam_slider_curr['easing'] == "easeInCirc"){ echo "selected";}?> ><?php _e('easeInCirc','glam-slider'); ?></option>
<option value="easeOutCirc" <?php if ($glam_slider_curr['easing'] == "easeOutCirc"){ echo "selected";}?> ><?php _e('easeOutCirc','glam-slider'); ?></option>
<option value="easeInOutCirc" <?php if ($glam_slider_curr['easing'] == "easeInOutCirc"){ echo "selected";}?> ><?php _e('easeInOutCirc','glam-slider'); ?></option>
<option value="easeInElastic" <?php if ($glam_slider_curr['easing'] == "easeInElastic"){ echo "selected";}?> ><?php _e('easeInElastic','glam-slider'); ?></option>
<option value="easeOutElastic" <?php if ($glam_slider_curr['easing'] == "easeOutElastic"){ echo "selected";}?> ><?php _e('easeOutElastic','glam-slider'); ?></option>
<option value="easeInOutElastic" <?php if ($glam_slider_curr['easing'] == "easeInOutElastic"){ echo "selected";}?> ><?php _e('easeInOutElastic','glam-slider'); ?></option>
<option value="easeInBack" <?php if ($glam_slider_curr['easing'] == "easeInBack"){ echo "selected";}?> ><?php _e('easeInBack','glam-slider'); ?></option>
<option value="easeOutBack" <?php if ($glam_slider_curr['easing'] == "easeOutBack"){ echo "selected";}?> ><?php _e('easeOutBack','glam-slider'); ?></option>
<option value="easeInOutBack" <?php if ($glam_slider_curr['easing'] == "easeInOutBack"){ echo "selected";}?> ><?php _e('easeInOutBack','glam-slider'); ?></option>
<option value="easeInBounce" <?php if ($glam_slider_curr['easing'] == "easeInBounce"){ echo "selected";}?> ><?php _e('easeInBounce','glam-slider'); ?></option>
<option value="easeOutBounce" <?php if ($glam_slider_curr['easing'] == "easeOutBounce"){ echo "selected";}?> ><?php _e('easeOutBounce','glam-slider'); ?></option>
<option value="easeInOutBounce" <?php if ($glam_slider_curr['easing'] == "easeInOutBounce"){ echo "selected";}?> ><?php _e('easeInOutBounce','glam-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Speed of Transition','glam-slider'); ?></th>
<td><input type="number" min="1" name="<?php echo $glam_slider_options;?>[speed]" id="glam_slider_speed" class="small-text" value="<?php echo $glam_slider_curr['speed']; ?>" />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('The duration of Slide Animation in milliseconds. Lower value indicates fast animation. Enter numeric values like 5 or 7.','glam-slider'); ?>
	</div>
</span>
</td>
</tr>
<?php 
	if($glam_slider_curr['autostep'] == 1) $showchk = "showSelected";
	else $showchk = "";
      if($glam_slider_curr['autostep'] == 0) $hidechk = "hideSelected";
	else $hidechk = "";
?>
<tr valign="top"> 
<th scope="row"><?php _e('Auto Sliding','glam-slider'); ?></th> 
<td>
<div class="showHideSwitch">
	<input type="hidden" name="<?php echo $glam_slider_options;?>[autostep]" class="showHideSwitch-checkbox" value="<?php echo $glam_slider_curr['autostep'];?>"  >
	<label class="label_show <?php echo $hidechk;?> switchHide" >OFF</label><label  class="label_hide <?php echo $showchk;?> switchShow" >ON</label>
</div>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Time between Transitions','glam-slider'); ?></th>
<td><input type="number" min="1" name="<?php echo $glam_slider_options;?>[time]" id="glam_slider_time" class="small-text" value="<?php echo $glam_slider_curr['time']; ?>" />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Enter number of secs you want the slider to stop before sliding to next slide, valid only if autosliding is enabled.','glam-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Number of Posts to be shown in the Glam Slider','glam-slider'); ?></th>
<td><input type="number" min="1" name="<?php echo $glam_slider_options;?>[no_posts]" id="glam_slider_no_posts" class="small-text" value="<?php echo $glam_slider_curr['no_posts']; ?>" /></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Number of Items Visible in One Set','glam-slider'); ?></th>
<td><input type="number" min="1" name="<?php echo $glam_slider_options;?>[visible]" id="glam_slider_visible" class="small-text" value="<?php echo $glam_slider_curr['visible']; ?>" /></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Maximum Slider Width','glam-slider'); ?></th>
<td><input type="number" min="1" name="<?php echo $glam_slider_options;?>[width]" id="glam_slider_width" class="small-text" value="<?php echo $glam_slider_curr['width']; ?>" />&nbsp;<?php _e('px','glam-slider'); ?></small></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Maximum Slider Height','glam-slider'); ?></th>
<td><input type="number" min="1" name="<?php echo $glam_slider_options;?>[height]" id="glam_slider_height" class="small-text" value="<?php echo $glam_slider_curr['height']; ?>" />&nbsp;<?php _e('px','glam-slider'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Center Slide/s Width','glam-slider'); ?></th>
<td><input type="number" min="1" name="<?php echo $glam_slider_options;?>[iwidth]" id="glam_slider_iwidth" class="small-text" value="<?php echo $glam_slider_curr['iwidth']; ?>" />&nbsp;<?php _e('px','glam-slider'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Side Slides\' Width','glam-slider'); ?></th>
<td><input type="number" min="1" name="<?php echo $glam_slider_options;?>[swidth]" id="glam_slider_swidth" class="small-text" value="<?php echo $glam_slider_curr['swidth']; ?>" />&nbsp;<?php _e('px','glam-slider'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Side Slides\' Opacity','glam-slider'); ?></th>
<td><input type="number" max="1" min="0" step="0.05" name="<?php echo $glam_slider_options;?>[s_opacity]" id="glam_slider_s_opacity" class="small-text" value="<?php echo $glam_slider_curr['s_opacity']; ?>" />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Enter decimal value between 0 and 1, both inclusive. Lower value indicates the side slides are more fait and transparent.','glam-slider'); ?>
	</div>
</span>
</td>
</tr>


<tr valign="top">
<th scope="row"><?php _e('Spacing between the slides','glam-slider'); ?></th>
<td><input type="number" min="0" name="<?php echo $glam_slider_options;?>[padding]" id="glam_slider_padding" class="small-text" value="<?php echo $glam_slider_curr['padding']; ?>" />&nbsp;<?php _e('px','glam-slider'); ?>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Padding on top and side of slides','glam-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Slider Background Color','glam-slider'); ?></th>
<td>
<input type="text"  name="<?php echo $glam_slider_options;?>[bg_color]" id="glam_slider_bg_color" value="<?php echo $glam_slider_curr['bg_color']; ?>" class="wp-color-picker-field" data-default-color="#000000" /></br></br>
<label for="glam_slider_bg"><input name="<?php echo $glam_slider_options;?>[bg]" type="checkbox" id="glam_slider_bg" value="1" <?php checked('1', $glam_slider_curr['bg']); ?>  /><?php _e(' Use Transparent Background','glam-slider'); ?></label> </td>
</tr>
 
<tr valign="top">
<th scope="row"><?php _e('Slider Border Thickness','glam-slider'); ?></th>
<td><input type="number" min="0" name="<?php echo $glam_slider_options;?>[border]" id="glam_slider_border" class="small-text" value="<?php echo $glam_slider_curr['border']; ?>" />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('put 0 if no border is required','glam-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Slider Border Color','glam-slider'); ?></th>
<td>
<input type="text" name="<?php echo $glam_slider_options;?>[brcolor]" id="glam_slider_brcolor" value="<?php echo $glam_slider_curr['brcolor']; ?>" class="wp-color-picker-field" data-default-color="#000000" />
</td>
</tr>

</table>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</div>

<div class="sub_settings_m toggle_settings">
<h2 class="sub-heading"><?php _e('Miscellaneous','glam-slider'); ?><img src="<?php echo glam_slider_plugin_url( 'images/close.png' ); ?>" id="minmax_img" class="toggle_img"></h2> 

<table class="form-table">

<tr valign="top">
<th scope="row"><?php _e('Retain these html tags','glam-slider'); ?></th>
<td><input type="text" name="<?php echo $glam_slider_options;?>[allowable_tags]" class="regular-text code" value="<?php echo $glam_slider_curr['allowable_tags']; ?>" /></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Continue Reading Text','glam-slider'); ?></th>
<td><input type="text" name="<?php echo $glam_slider_options;?>[more]" class="regular-text code" value="<?php echo $glam_slider_curr['more']; ?>" /></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Slide Link (\'a\' element) attributes  ','glam-slider'); ?></th>
<td><input type="text" name="<?php echo $glam_slider_options;?>[a_attr]" class="regular-text code" value="<?php echo htmlentities( $glam_slider_curr['a_attr'] , ENT_QUOTES); ?>" />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('eg. target="_blank" rel="external nofollow"','glam-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Use PrettyPhoto (Lightbox) for Slide Images','glam-slider'); ?></th>
<td><input name="<?php echo $glam_slider_options;?>[pphoto]" type="checkbox" value="1" <?php checked('1', $glam_slider_curr['pphoto']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('If checked, when user clicks the slide image, it will appear in a modal lightbox','glam-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Custom fields to display for post/pages','glam-slider'); ?></th>
<td><textarea name="<?php echo $glam_slider_options;?>[fields]"  rows="5" class="regular-text code"><?php echo $glam_slider_curr['fields']; ?></textarea><span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Separate different fields using commas eg. description,customfield2','glam-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Randomize Slides in Slider','glam-slider'); ?></th>
<td><input name="<?php echo $glam_slider_options;?>[rand]" type="checkbox" value="1" <?php checked('1', $glam_slider_curr['rand']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('check this if you want the slides added to appear in random order.','glam-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Enable FOUC','glam-slider'); ?></th>
<td><input name="<?php echo $glam_slider_options;?>[fouc]" type="checkbox" value="1" <?php checked('1', $glam_slider_curr['fouc']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('check this if you would not want to disable Flash of Unstyled Content in the slider when the page is loaded.','glam-slider'); ?>
	</div>
</span>
</td>
</tr>

<?php if(!isset($cntr) or empty($cntr)){?>

<tr valign="top">
<th scope="row"><?php _e('Minimum User Level to add Post to the Slider','glam-slider'); ?></th>
<td><select name="<?php echo $glam_slider_options;?>[user_level]" >
<option value="manage_options" <?php if ($glam_slider_curr['user_level'] == "manage_options"){ echo "selected";}?> ><?php _e('Administrator','glam-slider'); ?></option>
<option value="edit_others_posts" <?php if ($glam_slider_curr['user_level'] == "edit_others_posts"){ echo "selected";}?> ><?php _e('Editor and Admininstrator','glam-slider'); ?></option>
<option value="publish_posts" <?php if ($glam_slider_curr['user_level'] == "publish_posts"){ echo "selected";}?> ><?php _e('Author, Editor and Admininstrator','glam-slider'); ?></option>
<option value="edit_posts" <?php if ($glam_slider_curr['user_level'] == "edit_posts"){ echo "selected";}?> ><?php _e('Contributor, Author, Editor and Admininstrator','glam-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Text to display in the JavaScript disabled browser','glam-slider'); ?></th>
<td><input type="text" name="<?php echo $glam_slider_options;?>[noscript]" class="regular-text code" value="<?php echo $glam_slider_curr['noscript']; ?>" /></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Multiple Slider Feature','glam-slider'); ?></th>
<td><label for="glam_slider_multiple"> 
<input name="<?php echo $glam_slider_options;?>[multiple_sliders]" type="checkbox" id="glam_slider_multiple" value="1" <?php checked("1", $glam_slider_curr['multiple_sliders']); ?> /> 
 <?php _e('Enable Multiple Slider Function on Edit Post/Page','glam-slider'); ?></label></td>
</tr>

<?php if($glam_slider_curr['custom_post'] == 1) $showchk = "showSelected";
	else $showchk = "";
      if($glam_slider_curr['custom_post'] == 0) $hidechk = "hideSelected";
	else $hidechk = "";
?>
<tr valign="top"> 
<th scope="row"><?php _e('Create "SliderVilla Slides" Custom Post Type','glam-slider'); ?></th> 
<td>
<div class="showHideSwitch">
	<input type="hidden" name="<?php echo $glam_slider_options;?>[custom_post]" class="showHideSwitch-checkbox" value="<?php echo $glam_slider_curr['custom_post'];?>"  >
	<label class="label_show <?php echo $hidechk;?> switchHide">No</label><label  class="label_hide <?php echo $showchk;?> switchShow">Yes</label>
</div>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Custom Post Type Slug','glam-slider'); ?></th>
<td><input type="text" name="<?php echo $glam_slider_options;?>[cpost_slug]" id="glam_slider_cpost_slug" value="<?php echo $glam_slider_curr['cpost_slug']; ?>" />
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Remove Glam Slider Metabox on','glam-slider'); ?></th>
<td>
<select name="<?php echo $glam_slider_options;?>[remove_metabox][]" multiple="multiple" size="3" style="min-height:6em;">
<?php 
$args=array(
  'public'   => true
); 
$output = 'objects'; // names or objects, note names is the default
$post_types=get_post_types($args,$output); $remove_post_type_arr=$glam_slider_curr['remove_metabox'];
if(!isset($remove_post_type_arr) or !is_array($remove_post_type_arr) ) $remove_post_type_arr=array();
		foreach($post_types as $post_type) { ?>
                  <option value="<?php echo $post_type->name;?>" <?php if(in_array($post_type->name,$remove_post_type_arr)){echo 'selected';} ?>><?php echo $post_type->labels->name;?></option>
                <?php } ?>
</select>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('You can select single/multiple post types using Ctrl+Mouse Click. To deselect a single post type, use Ctrl+Mouse Click','glam-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Custom Styles','glam-slider'); ?></th>
<td><textarea name="<?php echo $glam_slider_options;?>[css]"  rows="5" class="regular-text code"><?php echo $glam_slider_curr['css']; ?></textarea>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('custom css styles that you would want to be applied to the slider elements.','glam-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Custom Styles load thru JS','glam-slider'); ?></th>
<td><textarea name="<?php echo $glam_slider_options;?>[css_js]"  rows="5" class="regular-text code"><?php echo $glam_slider_curr['css_js']; ?></textarea>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('Custom css loading thru jQuery on document load that you would want to be applied to the slider elements. Use this field only if necessary!','glam-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Show Slider Details on Admin Page','glam-slider'); ?></th>
<td><select name="<?php echo $glam_slider_options;?>[support]" >
<option value="1" <?php if ($glam_slider_curr['support'] == "1"){ echo "selected";}?> ><?php _e('Yes','glam-slider'); ?></option>
<option value="0" <?php if ($glam_slider_curr['support'] == "0"){ echo "selected";}?> ><?php _e('No','glam-slider'); ?></option>
</select>
</td>
</tr>
<?php } ?>

</table>
</div>
</div> <!--Basic Tab Ends-->

<div id="slider_content">
<div class="sub_settings toggle_settings">
<h2 class="sub-heading"><?php _e('Slider Title','glam-slider'); ?><img src="<?php echo glam_slider_plugin_url( 'images/close.png' ); ?>" id="minmax_img" class="toggle_img"></h2> 
<p><?php _e('Customize the looks of the main title of the Slideshow from here','glam-slider'); ?></p> 
<table class="form-table">

<tr valign="top">
<th scope="row"><?php _e('Default Title Text','glam-slider'); ?></th>
<td><input type="text" name="<?php echo $glam_slider_options;?>[title_text]" id="glam_slider_title_text" value="<?php echo htmlentities($glam_slider_curr['title_text'], ENT_QUOTES); ?>" /></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Pick Slider Title From','glam-slider'); ?></th>
<td><select name="<?php echo $glam_slider_options;?>[title_from]" >
<option value="0" <?php if ($glam_slider_curr['title_from'] == "0"){ echo "selected";}?> ><?php _e('Default Title Text','glam-slider'); ?></option>
<option value="1" <?php if ($glam_slider_curr['title_from'] == "1"){ echo "selected";}?> ><?php _e('Slider Name','glam-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font','glam-slider'); ?></th>
<td><select name="<?php echo $glam_slider_options;?>[title_font]" id="glam_slider_title_font" >
<option value="Arial,Helvetica,sans-serif" <?php if ($glam_slider_curr['title_font'] == "Arial,Helvetica,sans-serif"){ echo "selected";}?> >Arial,Helvetica,sans-serif</option>
<option value="Verdana,Geneva,sans-serif" <?php if ($glam_slider_curr['title_font'] == "Verdana,Geneva,sans-serif"){ echo "selected";}?> >Verdana,Geneva,sans-serif</option>
<option value="Tahoma,Geneva,sans-serif" <?php if ($glam_slider_curr['title_font'] == "Tahoma,Geneva,sans-serif"){ echo "selected";}?> >Tahoma,Geneva,sans-serif</option>
<option value="Trebuchet MS,sans-serif" <?php if ($glam_slider_curr['title_font'] == "Trebuchet MS,sans-serif"){ echo "selected";}?> >Trebuchet MS,sans-serif</option>
<option value="'Century Gothic','Avant Garde',sans-serif" <?php if ($glam_slider_curr['title_font'] == "'Century Gothic','Avant Garde',sans-serif"){ echo "selected";}?> >'Century Gothic','Avant Garde',sans-serif</option>
<option value="'Arial Narrow',sans-serif" <?php if ($glam_slider_curr['title_font'] == "'Arial Narrow',sans-serif"){ echo "selected";}?> >'Arial Narrow',sans-serif</option>
<option value="'Arial Black',sans-serif" <?php if ($glam_slider_curr['title_font'] == "'Arial Black',sans-serif"){ echo "selected";}?> >'Arial Black',sans-serif</option>
<option value="'Gills Sans MT','Gills Sans',sans-serif" <?php if ($glam_slider_curr['title_font'] == "'Gills Sans MT','Gills Sans',sans-serif"){ echo "selected";} ?> >'Gills Sans MT','Gills Sans',sans-serif</option>
<option value="'Times New Roman',Times,serif" <?php if ($glam_slider_curr['title_font'] == "'Times New Roman',Times,serif"){ echo "selected";}?> >'Times New Roman',Times,serif</option>
<option value="Georgia,serif" <?php if ($glam_slider_curr['title_font'] == "Georgia,serif"){ echo "selected";}?> >Georgia,serif</option>
<option value="Garamond,serif" <?php if ($glam_slider_curr['title_font'] == "Garamond,serif"){ echo "selected";}?> >Garamond,serif</option>
<option value="'Century Schoolbook','New Century Schoolbook',serif" <?php if ($glam_slider_curr['title_font'] == "'Century Schoolbook','New Century Schoolbook',serif"){ echo "selected";}?> >'Century Schoolbook','New Century Schoolbook',serif</option>
<option value="'Bookman Old Style',Bookman,serif" <?php if ($glam_slider_curr['title_font'] == "'Bookman Old Style',Bookman,serif"){ echo "selected";}?> >'Bookman Old Style',Bookman,serif</option>
<option value="'Comic Sans MS',cursive" <?php if ($glam_slider_curr['title_font'] == "'Comic Sans MS',cursive"){ echo "selected";}?> >'Comic Sans MS',cursive</option>
<option value="'Courier New',Courier,monospace" <?php if ($glam_slider_curr['title_font'] == "'Courier New',Courier,monospace"){ echo "selected";}?> >'Courier New',Courier,monospace</option>
<option value="'Copperplate Gothic Bold',Copperplate,fantasy" <?php if ($glam_slider_curr['title_font'] == "'Copperplate Gothic Bold',Copperplate,fantasy"){ echo "selected";}?> >'Copperplate Gothic Bold',Copperplate,fantasy</option>
<option value="Impact,fantasy" <?php if ($glam_slider_curr['title_font'] == "Impact,fantasy"){ echo "selected";}?> >Impact,fantasy</option>
<option value="sans-serif" <?php if ($glam_slider_curr['title_font'] == "sans-serif"){ echo "selected";}?> >sans-serif</option>
<option value="serif" <?php if ($glam_slider_curr['title_font'] == "serif"){ echo "selected";}?> >serif</option>
<option value="cursive" <?php if ($glam_slider_curr['title_font'] == "cursive"){ echo "selected";}?> >cursive</option>
<option value="monospace" <?php if ($glam_slider_curr['title_font'] == "monospace"){ echo "selected";}?> >monospace</option>
<option value="fantasy" <?php if ($glam_slider_curr['title_font'] == "fantasy"){ echo "selected";}?> >fantasy</option>
</select>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('This value will be fallback font if Google web font value is specified below','glam-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Google Web Font','glam-slider'); ?></th>
<td><input type="text" name="<?php echo $glam_slider_options;?>[title_fontg]" id="glam_slider_title_fontg" value="<?php echo htmlentities($glam_slider_curr['title_fontg'], ENT_QUOTES); ?>" />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('eg. enter value like Open+Sans or Oswald or Open+Sans+Condensed:300 etc.','glam-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Color','glam-slider'); ?></th>
<td>
<input type="text" name="<?php echo $glam_slider_options;?>[title_fcolor]" id="glam_slider_title_fcolor" value="<?php echo $glam_slider_curr['title_fcolor']; ?>" class="wp-color-picker-field" data-default-color="#000000" />
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Size','glam-slider'); ?></th>
<td><input type="number" min="0" name="<?php echo $glam_slider_options;?>[title_fsize]" id="glam_slider_title_fsize" class="small-text" value="<?php echo $glam_slider_curr['title_fsize']; ?>" />&nbsp;<?php _e('px','glam-slider'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Style','glam-slider'); ?></th>
<td><select name="<?php echo $glam_slider_options;?>[title_fstyle]" id="glam_slider_title_fstyle" >
<option value="bold" <?php if ($glam_slider_curr['title_fstyle'] == "bold"){ echo "selected";}?> ><?php _e('Bold','glam-slider'); ?></option>
<option value="bold italic" <?php if ($glam_slider_curr['title_fstyle'] == "bold italic"){ echo "selected";}?> ><?php _e('Bold Italic','glam-slider'); ?></option>
<option value="italic" <?php if ($glam_slider_curr['title_fstyle'] == "italic"){ echo "selected";}?> ><?php _e('Italic','glam-slider'); ?></option>
<option value="normal" <?php if ($glam_slider_curr['title_fstyle'] == "normal"){ echo "selected";}?> ><?php _e('Normal','glam-slider'); ?></option>
</select>
</td>
</tr>
</table>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</div>

<div class="sub_settings_m toggle_settings">
<h2 class="sub-heading"><?php _e('Content Box','glam-slider'); ?><img src="<?php echo glam_slider_plugin_url( 'images/close.png' ); ?>" id="minmax_img" class="toggle_img"></h2>  
<table class="form-table">

<tr valign="top">
<th scope="row"><?php _e('Background color','glam-slider'); ?></th>
<td>
<input type="text" name="<?php echo $glam_slider_options;?>[content_bg]" id="glam_slider_content_bg" value="<?php echo $glam_slider_curr['content_bg']; ?>" class="wp-color-picker-field" data-default-color="#000000" />
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Background Opacity','glam-slider'); ?></th>
<td><input type="number" min="0" max="1" step="0.05" name="<?php echo $glam_slider_options;?>[content_opacity]" id="glam_slider_content_opacity" class="small-text" value="<?php echo $glam_slider_curr['content_opacity']; ?>" /></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Show content on hover','glam-slider'); ?></th>
<td><select name="<?php echo $glam_slider_options;?>[content_tran]" id="glam_slider_content_tran" >
<option value="0" <?php if ($glam_slider_curr['content_tran'] == "0"){ echo "selected";}?> ><?php _e('No','glam-slider'); ?></option>
<option value="1" <?php if ($glam_slider_curr['content_tran'] == "1"){ echo "selected";}?> ><?php _e('Yes','glam-slider'); ?></option>
</select></td>
</tr>

</table>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</div>


<div class="sub_settings_m toggle_settings">
<h2 class="sub-heading"><?php _e('Post Title','glam-slider'); ?><img src="<?php echo glam_slider_plugin_url( 'images/close.png' ); ?>" id="minmax_img" class="toggle_img"></h2> 
<p><?php _e('Customize the looks of the title of each of the sliding post here','glam-slider'); ?></p> 
<table class="form-table">

<tr valign="top">
<th scope="row"><?php _e('Font','glam-slider'); ?></th>
<td><select name="<?php echo $glam_slider_options;?>[ptitle_font]" id="glam_slider_ptitle_font" >
<option value="Arial,Helvetica,sans-serif" <?php if ($glam_slider_curr['ptitle_font'] == "Arial,Helvetica,sans-serif"){ echo "selected";}?> >Arial,Helvetica,sans-serif</option>
<option value="Verdana,Geneva,sans-serif" <?php if ($glam_slider_curr['ptitle_font'] == "Verdana,Geneva,sans-serif"){ echo "selected";}?> >Verdana,Geneva,sans-serif</option>
<option value="Tahoma,Geneva,sans-serif" <?php if ($glam_slider_curr['ptitle_font'] == "Tahoma,Geneva,sans-serif"){ echo "selected";}?> >Tahoma,Geneva,sans-serif</option>
<option value="Trebuchet MS,sans-serif" <?php if ($glam_slider_curr['ptitle_font'] == "Trebuchet MS,sans-serif"){ echo "selected";}?> >Trebuchet MS,sans-serif</option>
<option value="'Century Gothic','Avant Garde',sans-serif" <?php if ($glam_slider_curr['ptitle_font'] == "'Century Gothic','Avant Garde',sans-serif"){ echo "selected";}?> >'Century Gothic','Avant Garde',sans-serif</option>
<option value="'Arial Narrow',sans-serif" <?php if ($glam_slider_curr['ptitle_font'] == "'Arial Narrow',sans-serif"){ echo "selected";}?> >'Arial Narrow',sans-serif</option>
<option value="'Arial Black',sans-serif" <?php if ($glam_slider_curr['ptitle_font'] == "'Arial Black',sans-serif"){ echo "selected";}?> >'Arial Black',sans-serif</option>
<option value="'Gills Sans MT','Gills Sans',sans-serif" <?php if ($glam_slider_curr['ptitle_font'] == "'Gills Sans MT','Gills Sans',sans-serif"){ echo "selected";} ?> >'Gills Sans MT','Gills Sans',sans-serif</option>
<option value="'Times New Roman',Times,serif" <?php if ($glam_slider_curr['ptitle_font'] == "'Times New Roman',Times,serif"){ echo "selected";}?> >'Times New Roman',Times,serif</option>
<option value="Georgia,serif" <?php if ($glam_slider_curr['ptitle_font'] == "Georgia,serif"){ echo "selected";}?> >Georgia,serif</option>
<option value="Garamond,serif" <?php if ($glam_slider_curr['ptitle_font'] == "Garamond,serif"){ echo "selected";}?> >Garamond,serif</option>
<option value="'Century Schoolbook','New Century Schoolbook',serif" <?php if ($glam_slider_curr['ptitle_font'] == "'Century Schoolbook','New Century Schoolbook',serif"){ echo "selected";}?> >'Century Schoolbook','New Century Schoolbook',serif</option>
<option value="'Bookman Old Style',Bookman,serif" <?php if ($glam_slider_curr['ptitle_font'] == "'Bookman Old Style',Bookman,serif"){ echo "selected";}?> >'Bookman Old Style',Bookman,serif</option>
<option value="'Comic Sans MS',cursive" <?php if ($glam_slider_curr['ptitle_font'] == "'Comic Sans MS',cursive"){ echo "selected";}?> >'Comic Sans MS',cursive</option>
<option value="'Courier New',Courier,monospace" <?php if ($glam_slider_curr['ptitle_font'] == "'Courier New',Courier,monospace"){ echo "selected";}?> >'Courier New',Courier,monospace</option>
<option value="'Copperplate Gothic Bold',Copperplate,fantasy" <?php if ($glam_slider_curr['ptitle_font'] == "'Copperplate Gothic Bold',Copperplate,fantasy"){ echo "selected";}?> >'Copperplate Gothic Bold',Copperplate,fantasy</option>
<option value="Impact,fantasy" <?php if ($glam_slider_curr['ptitle_font'] == "Impact,fantasy"){ echo "selected";}?> >Impact,fantasy</option>
<option value="sans-serif" <?php if ($glam_slider_curr['ptitle_font'] == "sans-serif"){ echo "selected";}?> >sans-serif</option>
<option value="serif" <?php if ($glam_slider_curr['ptitle_font'] == "serif"){ echo "selected";}?> >serif</option>
<option value="cursive" <?php if ($glam_slider_curr['ptitle_font'] == "cursive"){ echo "selected";}?> >cursive</option>
<option value="monospace" <?php if ($glam_slider_curr['ptitle_font'] == "monospace"){ echo "selected";}?> >monospace</option>
<option value="fantasy" <?php if ($glam_slider_curr['ptitle_font'] == "fantasy"){ echo "selected";}?> >fantasy</option>
</select>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('This value will be fallback font if Google web font value is specified below','glam-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Google Web Font','glam-slider'); ?></th>
<td><input type="text" name="<?php echo $glam_slider_options;?>[ptitle_fontg]" id="glam_slider_ptitle_fontg" value="<?php echo htmlentities($glam_slider_curr['ptitle_fontg'], ENT_QUOTES); ?>" />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('eg. enter value like Open+Sans or Oswald or Open+Sans+Condensed:300 etc.','glam-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Color','glam-slider'); ?></th>
<td><input type="text" name="<?php echo $glam_slider_options;?>[ptitle_fcolor]" id="glam_slider_ptitle_fcolor" value="<?php echo $glam_slider_curr['ptitle_fcolor']; ?>" class="wp-color-picker-field" data-default-color="#000000" />
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Size','glam-slider'); ?></th>
<td><input type="number" min="0" name="<?php echo $glam_slider_options;?>[ptitle_fsize]" id="glam_slider_ptitle_fsize" class="small-text" value="<?php echo $glam_slider_curr['ptitle_fsize']; ?>" />&nbsp;<?php _e('px','glam-slider'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Style','glam-slider'); ?></th>
<td><select name="<?php echo $glam_slider_options;?>[ptitle_fstyle]" id="glam_slider_ptitle_fstyle" >
<option value="bold" <?php if ($glam_slider_curr['ptitle_fstyle'] == "bold"){ echo "selected";}?> ><?php _e('Bold','glam-slider'); ?></option>
<option value="bold italic" <?php if ($glam_slider_curr['ptitle_fstyle'] == "bold italic"){ echo "selected";}?> ><?php _e('Bold Italic','glam-slider'); ?></option>
<option value="italic" <?php if ($glam_slider_curr['ptitle_fstyle'] == "italic"){ echo "selected";}?> ><?php _e('Italic','glam-slider'); ?></option>
<option value="normal" <?php if ($glam_slider_curr['ptitle_fstyle'] == "normal"){ echo "selected";}?> ><?php _e('Normal','glam-slider'); ?></option>
</select>
</td>
</tr>
</table>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</div>

<div class="sub_settings_m toggle_settings">
<h2 class="sub-heading"><?php _e('Thumbnail Image','glam-slider'); ?><img src="<?php echo glam_slider_plugin_url( 'images/close.png' ); ?>" id="minmax_img" class="toggle_img"></h2> 
<p><?php _e('Customize the looks of the thumbnail image for each of the sliding post here','glam-slider'); ?></p> 
<table class="form-table">


<?php 
$glam_slider_curr['img_pick'][0]=(isset($glam_slider_curr['img_pick'][0]))?$glam_slider_curr['img_pick'][0]:'';
$glam_slider_curr['img_pick'][2]=(isset($glam_slider_curr['img_pick'][2]))?$glam_slider_curr['img_pick'][2]:'';
$glam_slider_curr['img_pick'][3]=(isset($glam_slider_curr['img_pick'][3]))?$glam_slider_curr['img_pick'][3]:'';
$glam_slider_curr['img_pick'][5]=(isset($glam_slider_curr['img_pick'][5]))?$glam_slider_curr['img_pick'][5]:'';
?>

<tr valign="top"> 
<th scope="row"><?php _e('Image Pick Preferences','glam-slider'); ?> <small><?php _e('(The first one is having priority over second, the second having priority on third and so on)','glam-slider'); ?></small></th> 
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Image Pick Sequence','glam-slider'); ?> <small><?php _e('(The first one is having priority over second, the second having priority on third and so on)','glam-slider'); ?></small> </span></legend> 
<input name="<?php echo $glam_slider_options;?>[img_pick][0]" type="checkbox" value="1" <?php checked('1', $glam_slider_curr['img_pick'][0]); ?>  /> <?php _e('Use Custom Field/Key','glam-slider'); ?> <br/><br/>
<?php _e('Name of the Custom Field/Key','glam-slider'); ?><br/>
<input type="text" name="<?php echo $glam_slider_options;?>[img_pick][1]" class="text" value="<?php echo $glam_slider_curr['img_pick'][1]; ?>" /> 
<br/><br/>
<input name="<?php echo $glam_slider_options;?>[img_pick][2]" type="checkbox" value="1" <?php checked('1', $glam_slider_curr['img_pick'][2]); ?>  /> <?php _e('Use Featured Post/Thumbnail (Wordpress 3.0 +  feature)','glam-slider'); ?>&nbsp; <br/><br/>
<input name="<?php echo $glam_slider_options;?>[img_pick][3]" type="checkbox" value="1" <?php checked('1', $glam_slider_curr['img_pick'][3]); ?>  /> <?php _e('Consider Images attached to the post','glam-slider'); ?> <br/><br/>
<?php _e('Order of the Image attachment to pick','glam-slider'); ?>
<input type="text" name="<?php echo $glam_slider_options;?>[img_pick][4]" class="small-text" value="<?php echo $glam_slider_curr['img_pick'][4]; ?>" />  &nbsp; <br /><br />
<input name="<?php echo $glam_slider_options;?>[img_pick][5]" type="checkbox" value="1" <?php checked('1', $glam_slider_curr['img_pick'][5]); ?>  /> <?php _e('Scan images from the post, in case there is no attached image to the post','glam-slider'); ?>&nbsp; 
</fieldset></td> 
</tr> 

<tr valign="top">
<th scope="row"><?php _e('Wordpress Image Extract Size','glam-slider'); ?>
</th>
<td><select name="<?php echo $glam_slider_options;?>[crop]" id="glam_slider_img_crop" >
<option value="0" <?php if ($glam_slider_curr['crop'] == "0"){ echo "selected";}?> ><?php _e('Full','glam-slider'); ?></option>
<option value="1" <?php if ($glam_slider_curr['crop'] == "1"){ echo "selected";}?> ><?php _e('Large','glam-slider'); ?></option>
<option value="2" <?php if ($glam_slider_curr['crop'] == "2"){ echo "selected";}?> ><?php _e('Medium','glam-slider'); ?></option>
<option value="3" <?php if ($glam_slider_curr['crop'] == "3"){ echo "selected";}?> ><?php _e('Thumbnail','glam-slider'); ?></option>
</select>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('This is for fast page load, in case you choose \'Custom Size\' setting from below, you would not like to extract \'full\' size image from the media library. In this case you can use, \'medium\' or \'thumbnail\' image. This is because, for every image upload to the media gallery WordPress creates four sizes of the same image. So you can choose which to load in the slider and then specify the actual size.','glam-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Border Thickness','glam-slider'); ?></th>
<td><input type="number" min="0" name="<?php echo $glam_slider_options;?>[img_border]" id="glam_slider_img_border" class="small-text" value="<?php echo $glam_slider_curr['img_border']; ?>" />&nbsp;<?php _e('px  (put 0 if no border is required)','glam-slider'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Border Color','glam-slider'); ?></th>
<td><input type="text" name="<?php echo $glam_slider_options;?>[img_brcolor]" id="glam_slider_img_brcolor" value="<?php echo $glam_slider_curr['img_brcolor']; ?>" class="wp-color-picker-field" data-default-color="#000000" />
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Image Cropping (using timthumb)','glam-slider'); ?></th>
<td><select name="<?php echo $glam_slider_options;?>[timthumb]" >
<option value="0" <?php if ($glam_slider_curr['timthumb'] == "0"){ echo "selected";}?> ><?php _e('Enabled','glam-slider'); ?></option>
<option value="1" <?php if ($glam_slider_curr['timthumb'] == "1"){ echo "selected";}?> ><?php _e('Disabled','glam-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Make pure Image Slider','glam-slider'); ?></th>
<td><input name="<?php echo $glam_slider_options;?>[image_only]" type="checkbox" value="1" <?php checked('1', $glam_slider_curr['image_only']); ?>  />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('check this to convert Glam Slider to Image Slider with no content','glam-slider'); ?>
	</div>
</span>
</td>
</tr>
</table>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</div>

<div class="sub_settings_m toggle_settings">
<h2 class="sub-heading"><?php _e('Slide Content','glam-slider'); ?><img src="<?php echo glam_slider_plugin_url( 'images/close.png' ); ?>" id="minmax_img" class="toggle_img"></h2> 
<p><?php _e('Customize the looks of the content of each of the sliding post here','glam-slider'); ?></p> 
<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Content/description Below Title','glam-slider'); ?></th>
<td>
<?php 
	if($glam_slider_curr['show_content'] == 1) $showchk = "showSelected";
	else $showchk = "";
      if($glam_slider_curr['show_content'] == 0) $hidechk = "hideSelected";
	else $hidechk = "";
?>
<div class="showHideSwitch">
	<input type="hidden" name="<?php echo $glam_slider_options;?>[show_content]" class="showHideSwitch-checkbox" value="<?php echo $glam_slider_curr['show_content'];?>"  >
	<label class="label_show <?php echo $hidechk;?> switchHide" >OFF</label><label  class="label_hide <?php echo $showchk;?> switchShow" >ON</label>
</div>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Font','glam-slider'); ?></th>
<td><select name="<?php echo $glam_slider_options;?>[content_font]" id="glam_slider_content_font" >
<option value="Arial,Helvetica,sans-serif" <?php if ($glam_slider_curr['content_font'] == "Arial,Helvetica,sans-serif"){ echo "selected";}?> >Arial,Helvetica,sans-serif</option>
<option value="Verdana,Geneva,sans-serif" <?php if ($glam_slider_curr['content_font'] == "Verdana,Geneva,sans-serif"){ echo "selected";}?> >Verdana,Geneva,sans-serif</option>
<option value="Tahoma,Geneva,sans-serif" <?php if ($glam_slider_curr['content_font'] == "Tahoma,Geneva,sans-serif"){ echo "selected";}?> >Tahoma,Geneva,sans-serif</option>
<option value="Trebuchet MS,sans-serif" <?php if ($glam_slider_curr['content_font'] == "Trebuchet MS,sans-serif"){ echo "selected";}?> >Trebuchet MS,sans-serif</option>
<option value="'Century Gothic','Avant Garde',sans-serif" <?php if ($glam_slider_curr['content_font'] == "'Century Gothic','Avant Garde',sans-serif"){ echo "selected";}?> >'Century Gothic','Avant Garde',sans-serif</option>
<option value="'Arial Narrow',sans-serif" <?php if ($glam_slider_curr['content_font'] == "'Arial Narrow',sans-serif"){ echo "selected";}?> >'Arial Narrow',sans-serif</option>
<option value="'Arial Black',sans-serif" <?php if ($glam_slider_curr['content_font'] == "'Arial Black',sans-serif"){ echo "selected";}?> >'Arial Black',sans-serif</option>
<option value="'Gills Sans MT','Gills Sans',sans-serif" <?php if ($glam_slider_curr['content_font'] == "'Gills Sans MT','Gills Sans',sans-serif"){ echo "selected";} ?> >'Gills Sans MT','Gills Sans',sans-serif</option>
<option value="'Times New Roman',Times,serif" <?php if ($glam_slider_curr['content_font'] == "'Times New Roman',Times,serif"){ echo "selected";}?> >'Times New Roman',Times,serif</option>
<option value="Georgia,serif" <?php if ($glam_slider_curr['content_font'] == "Georgia,serif"){ echo "selected";}?> >Georgia,serif</option>
<option value="Garamond,serif" <?php if ($glam_slider_curr['content_font'] == "Garamond,serif"){ echo "selected";}?> >Garamond,serif</option>
<option value="'Century Schoolbook','New Century Schoolbook',serif" <?php if ($glam_slider_curr['content_font'] == "'Century Schoolbook','New Century Schoolbook',serif"){ echo "selected";}?> >'Century Schoolbook','New Century Schoolbook',serif</option>
<option value="'Bookman Old Style',Bookman,serif" <?php if ($glam_slider_curr['content_font'] == "'Bookman Old Style',Bookman,serif"){ echo "selected";}?> >'Bookman Old Style',Bookman,serif</option>
<option value="'Comic Sans MS',cursive" <?php if ($glam_slider_curr['content_font'] == "'Comic Sans MS',cursive"){ echo "selected";}?> >'Comic Sans MS',cursive</option>
<option value="'Courier New',Courier,monospace" <?php if ($glam_slider_curr['content_font'] == "'Courier New',Courier,monospace"){ echo "selected";}?> >'Courier New',Courier,monospace</option>
<option value="'Copperplate Gothic Bold',Copperplate,fantasy" <?php if ($glam_slider_curr['content_font'] == "'Copperplate Gothic Bold',Copperplate,fantasy"){ echo "selected";}?> >'Copperplate Gothic Bold',Copperplate,fantasy</option>
<option value="Impact,fantasy" <?php if ($glam_slider_curr['content_font'] == "Impact,fantasy"){ echo "selected";}?> >Impact,fantasy</option>
</select>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('This value will be fallback font if Google web font value is specified below','glam-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Google Web Font','glam-slider'); ?></th>
<td><input type="text" name="<?php echo $glam_slider_options;?>[content_fontg]" id="glam_slider_content_fontg" value="<?php echo htmlentities($glam_slider_curr['content_fontg'], ENT_QUOTES); ?>" />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('eg. enter value like Open+Sans or Oswald or Open+Sans+Condensed:300 etc.','glam-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Color','glam-slider'); ?></th>
<td><input type="text" name="<?php echo $glam_slider_options;?>[content_fcolor]" id="glam_slider_content_fcolor" value="<?php echo $glam_slider_curr['content_fcolor']; ?>" class="wp-color-picker-field" data-default-color="#000000" />
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Size','glam-slider'); ?></th>
<td><input type="number" min="0" name="<?php echo $glam_slider_options;?>[content_fsize]" id="glam_slider_content_fsize" class="small-text" value="<?php echo $glam_slider_curr['content_fsize']; ?>" />&nbsp;<?php _e('px','glam-slider'); ?></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Font Style','glam-slider'); ?></th>
<td><select name="<?php echo $glam_slider_options;?>[content_fstyle]" id="glam_slider_content_fstyle" >
<option value="bold" <?php if ($glam_slider_curr['content_fstyle'] == "bold"){ echo "selected";}?> ><?php _e('Bold','glam-slider'); ?></option>
<option value="bold italic" <?php if ($glam_slider_curr['content_fstyle'] == "bold italic"){ echo "selected";}?> ><?php _e('Bold Italic','glam-slider'); ?></option>
<option value="italic" <?php if ($glam_slider_curr['content_fstyle'] == "italic"){ echo "selected";}?> ><?php _e('Italic','glam-slider'); ?></option>
<option value="normal" <?php if ($glam_slider_curr['content_fstyle'] == "normal"){ echo "selected";}?> ><?php _e('Normal','glam-slider'); ?></option>
</select>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Pick content From','glam-slider'); ?></th>
<td><select name="<?php echo $glam_slider_options;?>[content_from]" id="glam_slider_content_from" >
<option value="slider_content" <?php if ($glam_slider_curr['content_from'] == "slider_content"){ echo "selected";}?> ><?php _e('Slider Content Custom field','glam-slider'); ?></option>
<option value="excerpt" <?php if ($glam_slider_curr['content_from'] == "excerpt"){ echo "selected";}?> ><?php _e('Post Excerpt','glam-slider'); ?></option>
<option value="content" <?php if ($glam_slider_curr['content_from'] == "content"){ echo "selected";}?> ><?php _e('From Content','glam-slider'); ?></option>
</select>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Maximum content size (in words)','glam-slider'); ?></th>
<td><input type="number" min="0" name="<?php echo $glam_slider_options;?>[content_limit]" id="glam_slider_content_limit" class="small-text" value="<?php echo $glam_slider_curr['content_limit']; ?>" />&nbsp;<?php _e('words','glam-slider'); ?>
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('if specified will override the \'Maximum Content Size in Chracters\' setting below','glam-slider'); ?>
	</div>
</span>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Maximum content size (in characters)','glam-slider'); ?></th>
<td><input type="number" min="0" name="<?php echo $glam_slider_options;?>[content_chars]" id="glam_slider_content_chars" class="small-text" value="<?php echo $glam_slider_curr['content_chars']; ?>" />&nbsp;<?php _e('characters','glam-slider'); ?> </td>
</tr>

</table>

</div>
</div> <!-- slider_content tab ends-->

<div id="slider_nav">
<div class="sub_settings toggle_settings">
<h2 class="sub-heading"><?php _e('Navigational Arrows','glam-slider'); ?><img src="<?php echo glam_slider_plugin_url( 'images/close.png' ); ?>" id="minmax_img" class="toggle_img"></h2> 

<table class="form-table">
<tr valign="top"> 
<th scope="row"><?php _e('Hide Prev/Next navigation arrows','glam-slider'); ?></th> 
<td><label for="glam_slider_prev_next"> 
<input name="<?php echo $glam_slider_options;?>[prev_next]" type="checkbox" id="glam_slider_prev_next" value="1" <?php checked("1", $glam_slider_curr['prev_next']); ?> /> 
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Prev-Next Arrows Folder','glam-slider'); ?></th>
<td style="background: #ddd;">
<?php 
$directory = GLAM_SLIDER_CSS_OUTER.'/buttons/';
if ($handle = opendir($directory)) {
    while (false !== ($file = readdir($handle))) { 
     if($file != '.' and $file != '..') { 
     $nexturl='css/buttons/'.$file.'/next.png';?>
<div class="arrows"><img src="<?php echo glam_slider_plugin_url($nexturl);?>"/>
<input name="<?php echo $glam_slider_options;?>[buttons]" type="radio" id="glam_slider_buttons" class="arrows_input" value="<?php echo $file;?>" <?php if($glam_slider_curr['buttons'] == $file)  echo ' checked="checked"';?> /></div>
 <?php  } }
    closedir($handle);
}
?>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Navigation Arrow Distance from Top of the Slider','glam-slider'); ?></th>
<td><input type="number" min="0" name="<?php echo $glam_slider_options;?>[navtop]" id="glam_slider_navtop" class="small-text" value="<?php echo $glam_slider_curr['navtop']; ?>" />%</td>
</tr>

</table>

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</div>

<div>
<div class="sub_settings_m toggle_settings">
<h2 class="sub-heading"><?php _e('Navigational Buttons','glam-slider'); ?><img src="<?php echo glam_slider_plugin_url( 'images/close.png' ); ?>" id="minmax_img" class="toggle_img"></h2> 

<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Show Navigation Buttons','glam-slider'); ?></th>
<td><select name="<?php echo $glam_slider_options;?>[navnum]" >
<option value="0" <?php if ($glam_slider_curr['navnum'] == "0"){ echo "selected";}?> ><?php _e('No','glam-slider'); ?></option>
<option value="1" <?php if ($glam_slider_curr['navnum'] == "1"){ echo "selected";}?> ><?php _e('Bottom of Slider','glam-slider'); ?></option>
<option value="2" <?php if ($glam_slider_curr['navnum'] == "2"){ echo "selected";}?> ><?php _e('Top of Slider','glam-slider'); ?></option>
</select>
</td>
</tr>
</table>

</div></div>

</div><!-- slider_nav tab ends-->

<div id="preview">
<div class="sub_settings toggle_settings">
<h2 class="sub-heading"><?php _e('Preview on Settings Panel','glam-slider'); ?><img src="<?php echo glam_slider_plugin_url( 'images/close.png' ); ?>" id="minmax_img" class="toggle_img"></h2> 

<table class="form-table">

<tr valign="top"> 
<th scope="row"><label for="glam_slider_disable_preview"><?php _e('Disable Preview Section','glam-slider'); ?></label></th> 
<td> 
<input name="<?php echo $glam_slider_options;?>[disable_preview]" type="checkbox" id="glam_slider_disable_preview" value="1" <?php checked("1", $glam_slider_curr['disable_preview']); ?> />
<span class="moreInfo">
	&nbsp; <span class="trigger"> ? </span>
	<div class="tooltip">
	<?php _e('If disabled, the \'Preview\' of Slider on this Settings page will be removed.','glam-slider'); ?>
	</div>
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e('Glam Template Tag for Preview','glam-slider'); ?></th>
<td><select name="<?php echo $glam_slider_options;?>[preview]"  id="glam_slider_preview" onchange="checkpreview(this.value);">
<option value="2" <?php if ($glam_slider_curr['preview'] == "2"){ echo "selected";}?> ><?php _e('Recent Posts Slider','glam-slider'); ?></option>
<option value="1" <?php if ($glam_slider_curr['preview'] == "1"){ echo "selected";}?> ><?php _e('Category Slider','glam-slider'); ?></option>
<option value="0" <?php if ($glam_slider_curr['preview'] == "0"){ echo "selected";}?> ><?php _e('Custom Slider','glam-slider'); ?></option>
</select>
</td>
</tr>
<?php 
/* Added for category and slider name selection */
//category slug
$categories = get_categories();
$scat_html='<option value="" selected >Select the Category</option>';

foreach ($categories as $category) { 
 if($category->slug==$glam_slider_curr['catg_slug']){$selected = 'selected';} else{$selected='';}
 $scat_html =$scat_html.'<option value="'.$category->slug.'" '.$selected.'>'. $category->name .'</option>';
} 
//slider names
global $glam_slider;
if($glam_slider['multiple_sliders'] == '1') {	
			$slider_id = $glam_slider_curr['slider_id'];	
			$sliders = glam_ss_get_sliders();
			$sname_html='<option value="0" selected >Select the Slider</option>';
	 		
		  foreach ($sliders as $slider) { 
			 if($slider['slider_id']==$slider_id){$selected = 'selected';} else{$selected='';}
			 $sname_html =$sname_html.'<option value="'.$slider['slider_id'].'" '.$selected.'>'.$slider['slider_name'].'</option>';
		  } 
}
?>
<tr valign="top" class="glam_slider_params"> 
<th scope="row"><?php _e('Preview Slider Params','glam-slider'); ?></th> 
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Preview Slider Params','glam-slider'); ?></span></legend> 
<label for="<?php echo $glam_slider_options;?>[slider_id]" class="glam_sid"><?php _e('Select Slider Name','glam-slider'); ?></label>
<select id="glam_slider_id" name="<?php echo $glam_slider_options;?>[slider_id]" class="glam_sid"><?php echo $sname_html;?></select>

<label for="<?php echo $glam_slider_options;?>[catg_slug]" class="glam_catslug"><?php _e('Select Category','glam-slider'); ?></label>
<select id="glam_slider_catslug" name="<?php echo $glam_slider_options;?>[catg_slug]" class="glam_catslug"><?php echo $scat_html;?></select>
</fieldset></td> 
</tr> 
</table>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</div>

<div class="sub_settings_m toggle_settings">
<h2 class="sub-heading"><?php _e('Shortcode','glam-slider'); ?><img src="<?php echo glam_slider_plugin_url( 'images/close.png' ); ?>" id="minmax_img" class="toggle_img"></h2> 
<p><?php _e('Paste the below shortcode on Page/Post Edit Panel to get the slider as shown in the above Preview','glam-slider'); ?></p><br />
<?php if($cntr=='') $s_set='1'; else $s_set=$cntr;
if ($glam_slider_curr['preview'] == "0")
	$preview = '[glamslider id="'.$glam_slider_curr['slider_id'].'" set="'.$s_set.'"]';
elseif($glam_slider_curr['preview'] == "1")
	$preview = '[glamcategory catg_slug="'.$glam_slider_curr['catg_slug'].'" set="'.$s_set.'"]';
else
	$preview = '[glamrecent set="'.$s_set.'"]';

echo "<p>".$preview."</p>";
?>
</div>

<div class="sub_settings_m toggle_settings">
<h2 class="sub-heading"><?php _e('Template Tag','glam-slider'); ?><img src="<?php echo glam_slider_plugin_url( 'images/close.png' ); ?>" id="minmax_img" class="toggle_img"></h2> 
<p><?php _e('Paste the below template tag in your theme template file like index.php or page.php at required location to get the slider as shown in the above Preview','glam-slider'); ?></p><br />
<?php 
if ($glam_slider_curr['preview'] == "0")
	echo '<code>&lt;?php if(function_exists("get_glam_slider")){get_glam_slider($slider_id="'.$glam_slider_curr['slider_id'].'",$set="'.$s_set.'");}?&gt;</code>';
elseif($glam_slider_curr['preview'] == "1")
	echo '<code>&lt;?php if(function_exists("get_glam_slider_category")){get_glam_slider_category($catg_slug="'.$glam_slider_curr['catg_slug'].'",$set="'.$s_set.'");}?&gt;</code>';
else
	echo '<code>&lt;?php if(function_exists("get_glam_slider_recent")){get_glam_slider_recent($set="'.$s_set.'");}?&gt;</code>';
?>
</div>

</div><!-- preview tab ends-->


<div id="cssvalues">
<div class="sub_settings toggle_settings">
<h2 class="sub-heading"><?php _e('CSS Generated thru these settings','glam-slider'); ?><img src="<?php echo glam_slider_plugin_url( 'images/close.png' ); ?>" id="minmax_img" class="toggle_img"></h2> 
<p><?php _e('Save Changes for the settings first and then view this data. You can use this CSS in your \'custom\' stylesheets if you use other than \'default\' value for the Stylesheet folder.','glam-slider'); ?></p> 
<?php $glam_slider_css = glam_get_inline_css($cntr,$echo='1'); ?>
<div style="font-family:monospace;font-size:13px;background:#ddd;word-wrap: break-word;padding:10px;">
.glam_slider_set<?php echo $cntr;?>{<?php echo $glam_slider_css['glam_slider'];?>} <br />
.glam_slider_set<?php echo $cntr;?> .sldr_title{<?php echo $glam_slider_css['sldr_title'];?>} <br />
.glam_slider_set<?php echo $cntr;?> .glam_slides_wrap{<?php echo $glam_slider_css['glam_slides_wrap'];?>} <br />
.glam_slider_set<?php echo $cntr;?> .glam_items{<?php echo $glam_slider_css['glam_items'];?>} <br />
.glam_slider_set<?php echo $cntr;?> .glam_slider_instance{<?php echo $glam_slider_css['glam_slider_instance'];?>} <br />
.glam_slider_set<?php echo $cntr;?> .glam_slide{<?php echo $glam_slider_css['glam_slide'];?>} <br />
.glam_slider_set<?php echo $cntr;?> .glam_slideri{<?php echo $glam_slider_css['glam_slideri'];?>} <br />
.glam_slider_set<?php echo $cntr;?> .glam_slide_content{<?php echo $glam_slider_css['glam_slide_content'];?>} <br />
.glam_slider_set<?php echo $cntr;?> .glam_slider_thumbnail{<?php echo $glam_slider_css['glam_slider_thumbnail'];?>} <br />
.glam_slider_set<?php echo $cntr;?> .glam_slideri h4{<?php echo $glam_slider_css['glam_slider_h4'];?>} <br />
.glam_slider_set<?php echo $cntr;?> .glam_slideri h4 a{<?php echo $glam_slider_css['glam_slider_h4_a'];?>} <br />
.glam_slider_set<?php echo $cntr;?> .glam_slideri span{<?php echo $glam_slider_css['glam_slider_span'];?>} <br />
.glam_slider_set<?php echo $cntr;?> .glam_slideri p.more{<?php echo $glam_slider_css['glam_slider_p_more'];?>} <br />
.glam_slider_set<?php echo $cntr;?> .glam_next{<?php echo $glam_slider_css['glam_next'];?>} <br />
.glam_slider_set<?php echo $cntr;?> .glam_prev{<?php echo $glam_slider_css['glam_prev'];?>} <br />
.glam_slider_set<?php echo $cntr;?> .glam_nav{<?php echo $glam_slider_css['glam_nav'];?>} <br />
.glam_slider_set<?php echo $cntr;?> .glam_nav a{<?php echo $glam_slider_css['glam_nav_a'];?>} <br />
.glam_slider_set<?php echo $cntr;?> .glam_side_link{<?php echo $glam_slider_css['glam_side_link'];?>} <br />

</div>
</div>
</div> <!--#cssvalues-->

<div class="svilla_cl"></div><div class="svilla_cr"></div>

</div> <!--end of tabs -->

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
<input type="hidden" name="<?php echo $glam_slider_options;?>[active_tab]" id="glam_activetab" value="<?php echo $glam_slider_curr['active_tab']; ?>" />
<input type="hidden" name="<?php echo $glam_slider_options;?>[new]" id="glam_new_set" value="0" />
<input type="hidden" name="<?php echo $glam_slider_options;?>[popup]" id="glampopup" value="<?php echo $glam_slider_curr['popup']; ?>" />
<input type="hidden" name="oldnew" id="oldnew" value="<?php echo $glam_slider_curr['new']; ?>" />
<input type="hidden" name="hidden_preview" id="hidden_preview" value="<?php echo $glam_slider_curr['preview']; ?>" />
<input type="hidden" name="hidden_category" id="hidden_category" value="<?php echo $glam_slider_curr['catg_slug']; ?>" />
<input type="hidden" name="hidden_sliderid" id="hidden_sliderid" value="<?php echo $glam_slider_curr['slider_id']; ?>" />
</form>
<div id="saveResult"></div>
<!--Form to reset Settings set-->
<form style="float:left;" action="" method="post">
<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Reset Settings to','glam-slider'); ?></th>
<td><select name="glam_reset_settings" id="glam_slider_reset_settings" >
<option value="n" selected ><?php _e('None','glam-slider'); ?></option>
<option value="g" ><?php _e('Global Default','glam-slider'); ?></option>
<?php 
$directory = GLAM_SLIDER_CSS_DIR;
if ($handle = opendir($directory)) {
    while (false !== ($file = readdir($handle))) { 
     if($file != '.' and $file != '..') { 
	if($file!="default" and $file!="sample")     
	{?>
      <option value="<?php echo $file;?>"><?php echo "'".$file."' skin";?></option>
 <?php  } } }
    closedir($handle);
}
?>
<?php 
for($i=1;$i<=$scounter;$i++){
	if ($i==1){
	  echo '<option value="'.$i.'" >'.__('Default Settings Set','glam-slider').'</option>';
	}
	else {
	  if($settings_set=get_option('glam_slider_options'.$i)){
		echo '<option value="'.$i.'" >'. (isset($settings_set['setname'])? ($settings_set['setname']) : '' ) .' (ID '.$i.')</option>';
	  }
	}
}
?>

</select>
</td>
</tr>
</table>

<p class="submit">
<input name="glam_reset_settings_submit" type="submit" class="button-primary" value="<?php _e('Reset Settings') ?>" />
</p>
</form>

<div class="svilla_cl"></div>

<div style="border:1px solid #ccc;padding:10px;background:#fff;margin:0;" id="import">
<?php if (isset ($imported_settings_message))echo $imported_settings_message;?>
<h3><?php _e('Import Settings Set by uploading a Settings File','glam-slider'); ?></h3>
<form style="margin-right:10px;font-size:14px;" action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
<input type="file" name="settings_file" id="settings_file" style="font-size:13px;width:50%;padding:0 5px;" />
<input type="submit" value="Import" name="import"  onclick="return confirmSettingsImport()" title="<?php _e('Import Settings from a file','glam-slider'); ?>" class="button-primary" />
</form>
</div>

</div> <!--end of float left -->
<script type="text/javascript">
<?php 
	$directory = GLAM_SLIDER_CSS_DIR;
	if ($handle = opendir($directory)) {
	    while (false !== ($file = readdir($handle))) { 
	     if($file != '.' and $file != '..') { 
			$default_settings_str='default_settings_'.$file;
	       		global ${$default_settings_str};
			echo 'var '.$default_settings_str.' = '.json_encode(${$default_settings_str}).';';
		} }
	    closedir($handle);
	}
?>
function checkskin(skin){ 
	var skin_array=window['default_settings_'+skin];			
	for (var key in skin_array) {
	       var html_element='#glam_slider_'+key;
	       jQuery(html_element).val(skin_array[key]);
	}
}

jQuery(document).ready(function($) {
<?php if(isset($_GET['settings-updated'])) { if($_GET['settings-updated'] == 'true' and $glam_slider_curr['popup'] == '1' ) { 
?>
jQuery('#saveResult').html("<div id='popup'><div class='modal_shortcode'>Quick Embed Shortcode</div><span class='button b-close'><span>X</span></span></div>");
				jQuery('#popup').append('<div class="modal_preview"><?php echo $preview;?></div>');				
				jQuery('#popup').bPopup({
		    			opacity: 0.6,
					position: ['35%', '35%'],
		    			positionStyle: 'fixed', //'fixed' or 'absolute'			
					onClose: function() { return true; }
				});

<?php }} ?>

/* Added for settings page tabs minimize and maximize - start */
jQuery(this).find(".sub-heading").on("click", function(){
	var wrap=jQuery(this).parent('.toggle_settings'),
	tabcontent=wrap.find("p, table, code, div");
	tabcontent.toggle();
	var imgclass=wrap.find(".toggle_img");
	if (tabcontent.css('display') == 'none') {
		imgclass.attr("src", imgclass.attr("src").replace("<?php echo glam_slider_plugin_url( 'images/close.png' ); ?>", "<?php echo glam_slider_plugin_url( 'images/info.png' ); ?>"));
	} else {
		imgclass.attr("src", imgclass.attr("src").replace("<?php echo glam_slider_plugin_url( 'images/info.png' ); ?>", "<?php echo glam_slider_plugin_url( 'images/close.png' ); ?>"));
	}
});
/* Added for settings page tabs minimize and maximize - end */

});

</script>
<div id="poststuff" class="metabox-holder has-right-sidebar" style="float: left;max-width: 270px;">
<div class="postbox" style="margin:0 0 10px 0;"> 
	<h3 class="hndle"><span></span><?php _e('Quick Embed Shortcode','glam-slider'); ?></h3> 
	<div class="inside" id="shortcodeview">
	<?php if($cntr=='') $s_set='1'; else $s_set=$cntr;
if ($glam_slider_curr['preview'] == "0")
	echo '[glamslider id="'.$glam_slider_curr['slider_id'].'" set="'.$s_set.'"]';
elseif($glam_slider_curr['preview'] == "1")
	echo '[glamcategory catg_slug="'.$glam_slider_curr['catg_slug'].'" set="'.$s_set.'"]';
else
	echo '[glamrecent set="'.$s_set.'"]';
?>
</div></div>

<div class="postbox" style="margin:10px 0;"> 
	<h3 class="hndle"><span></span><?php _e('Quick Embed Template Tag','glam-slider'); ?></h3> 
	<div class="inside">
	<?php 
	if ($glam_slider_curr['preview'] == "0")
		echo '<code>&lt;?php if( function_exists( "get_glam_slider" ) ){ get_glam_slider( $slider_id="'.$glam_slider_curr['slider_id'].'",$set="'.$s_set.'") ;}?&gt;</code>';
	elseif($glam_slider_curr['preview'] == "1")
		echo '<code>&lt;?php if( function_exists( "get_glam_slider_category" ) ){ get_glam_slider_category( $catg_slug="'.$glam_slider_curr['catg_slug'].'",$set="'.$s_set.'") ;}?&gt;</code>';
	else
		echo '<code>&lt;?php if( function_exists( "get_glam_slider_recent" ) ){ get_glam_slider_recent( $set="'.$s_set.'") ;}?&gt;</code>';
?>
</div></div>
<?php $url = glam_sslider_admin_url( array( 'page' => 'glam-slider-admin' ) );?>
<form style="margin-right:10px;font-size:14px;width:100%;" action="" method="post">
<a href="<?php echo $url; ?>" title="<?php _e('Go to Sliders page where you can re-order the slide posts, delete the slides from the slider etc.','glam-slider'); ?>" class="svilla_button svilla_gray_button"><?php _e('Go to Sliders Admin','glam-slider'); ?></a>
<input type="submit" class="svilla_button" value="Create New Settings Set" name="create_set"  onclick="return confirmSettingsCreate()" /> <br />
<input type="submit" value="Export" name="export" title="<?php _e('Export this Settings Set to a file','glam-slider'); ?>" class="svilla_button" />
<a href="#import" title="<?php _e('Go to Import Settings Form','glam-slider'); ?>" class="svilla_button">Import</a>
<div class="svilla_cl"></div>
</form>
<div class="svilla_cl"></div>

<div class="postbox" style="margin:10px 0;"> 
			  <h3 class="hndle"><span></span><?php _e('Available Settings Sets','glam-slider'); ?></h3> 
			  <div class="inside">
<?php 
for($i=1;$i<=$scounter;$i++){
   if ($i==1){
      echo '<h4><a href="'.glam_sslider_admin_url( array( 'page' => 'glam-slider-settings' ) ).'" title="(Settings Set ID '.$i.')">Default Settings (ID '.$i.')</a></h4>';
   }
   else {
      if($settings_set=get_option('glam_slider_options'.$i)){
		echo '<h4><a href="'.glam_sslider_admin_url( array( 'page' => 'glam-slider-settings' ) ).'&scounter='.$i.'" title="(Settings Set ID '.$i.')">'. (isset($settings_set['setname'])? ($settings_set['setname']) : '' ) .' (ID '.$i.')</a></h4>';
	  }
   }
}
?>
</div></div>

<?php if ($glam_slider['support'] == "1"){ ?>
	<div class="postbox"> 
		<div style="background:#eee;line-height:200%"><a style="text-decoration:none;font-weight:bold;font-size:100%;color:#990000" href="http://guides.slidervilla.com/glam-slider/" title="Click here to read how to use the plugin and frequently asked questions about the plugin" target="_blank"> ==> Usage Guide and General FAQs</a></div>
	</div>
          
	<div class="postbox"> 
	  <h3 class="hndle"><span><?php _e('About this Plugin:','glam-slider'); ?></span></h3> 
	  <div class="inside">
		<ul>
		<li><a href="http://slidervilla.com/glam/" title="<?php _e('Glam Slider Homepage','glam-slider'); ?>
" ><?php _e('Plugin Homepage','glam-slider'); ?></a></li>
		<li><a href="http://support.slidervilla.com/" title="<?php _e('Support Forum','glam-slider'); ?>
" ><?php _e('Support Forum','glam-slider'); ?></a></li>
		<li><a href="http://guides.slidervilla.com/glam-slider/" title="<?php _e('Usage Guide','glam-slider'); ?>
" ><?php _e('Usage Guide','glam-slider'); ?></a></li>
		<li><strong>Current Version: <?php echo GLAM_SLIDER_VER;?></strong></li>
		</ul> 
	  </div> 
	</div> 
<?php } ?>
                 
</div> <!--end of poststuff --> 

<div style="clear:left;"></div>
<div style="clear:right;"></div>

</div> <!--end of float wrap -->
<?php	
}
function register_glam_settings() { // whitelist options
  $scounter=get_option('glam_slider_scounter');
  for($i=1;$i<=$scounter;$i++){
	   if ($i==1){
		  register_setting( 'glam-slider-group', 'glam_slider_options' );
	   }
	   else {
	      $group='glam-slider-group'.$i;
		  $options='glam_slider_options'.$i;
		  register_setting( $group, $options );
	   }
  }
  register_setting( 'glam-slider-license-info', 'glam_license_key' );
}
?>
