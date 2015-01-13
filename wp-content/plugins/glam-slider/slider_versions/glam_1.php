<?php 
function glam_global_data_processor( $slides, $glam_slider_curr,$out_echo,$set,$data=array() ){
	//If no Skin specified, consider Default
	$skin='default';
	if(isset($glam_slider_curr['stylesheet'])) $skin=$glam_slider_curr['stylesheet'];
	if(empty($skin))$skin='default';
	
	//Always include Default Skin
	require_once ( dirname( dirname(__FILE__) ) . '/css/skins/default/functions.php');
	//Include Skin function file
	if($skin!='default' and file_exists(dirname( dirname(__FILE__) ) . '/css/skins/'.$skin.'/functions.php'))require_once ( dirname( dirname(__FILE__) ) . '/css/skins/'.$skin.'/functions.php');
	
	//Skin specific data processor and html generation
	$data_processor_fn='glam_data_processor_'.$skin;
	if(!function_exists($data_processor_fn))$data_processor_fn='glam_data_processor_default';

	$r_array=$data_processor_fn($slides, $glam_slider_curr,$out_echo,$set,$data);
	return $r_array;	
}
function glam_global_posts_processor( $posts, $glam_slider_curr,$out_echo,$set,$data=array() ){
	//If no Skin specified, consider Default
	$skin='default';
	if(isset($glam_slider_curr['stylesheet'])) $skin=$glam_slider_curr['stylesheet'];
	if(empty($skin))$skin='default';
	
	//Always include Default Skin
	require_once ( dirname( dirname(__FILE__) ) . '/css/skins/default/functions.php');
	//Include Skin function file
	if($skin!='default' and file_exists(dirname( dirname(__FILE__) ) . '/css/skins/'.$skin.'/functions.php')) require_once ( dirname( dirname(__FILE__) ) . '/css/skins/'.$skin.'/functions.php');
	
	//Skin specific post processor and html generation
	$post_processor_fn='glam_post_processor_'.$skin;
	if(!function_exists($post_processor_fn))$post_processor_fn='glam_post_processor_default';
	$r_array=$post_processor_fn($posts, $glam_slider_curr,$out_echo,$set,$data);
	return $r_array;	
}

function get_global_glam_slider($slider_handle,$r_array,$glam_slider_curr,$set,$echo='1',$data=array() ){
	//If no Skin specified, consider Default
	$skin='default';
	if(isset($glam_slider_curr['stylesheet'])) $skin=$glam_slider_curr['stylesheet'];
	if(empty($skin))$skin='default';
	
	//Include CSS
	wp_enqueue_style( 'glam_'.$skin, glam_slider_plugin_url( 'css/skins/'.$skin.'/style.css' ),	false, GLAM_SLIDER_VER, 'all');
	
	//Always include Default Skin
	require_once ( dirname( dirname(__FILE__) ) . '/css/skins/default/functions.php');
	//Include Skin function file
	if($skin!='default' and file_exists(dirname( dirname(__FILE__) ) . '/css/skins/'.$skin.'/functions.php'))
	require_once ( dirname( dirname(__FILE__) ) . '/css/skins/'.$skin.'/functions.php');
	
	//Skin specific post processor and html generation
	$get_processor_fn='glam_slider_get_'.$skin;
	if(!function_exists($get_processor_fn))$get_processor_fn='glam_slider_get_default';
	$r_array=$get_processor_fn($slider_handle,$r_array,$glam_slider_curr,$set,$echo,$data);
	return $r_array;	
}

function glam_carousel_posts_on_slider($max_posts, $offset=0, $slider_id = '1',$out_echo = '1',$set='',$data=array() ){
    global $glam_slider,$default_glam_slider_settings; 
	$glam_slider_options='glam_slider_options'.$set;
    $glam_slider_curr=get_option($glam_slider_options);
	if(!isset($glam_slider_curr) or !is_array($glam_slider_curr) or empty($glam_slider_curr)){$glam_slider_curr=$glam_slider;$set='';}
	
	foreach($default_glam_slider_settings as $key=>$value){
		if(!isset($glam_slider_curr[$key])) $glam_slider_curr[$key]='';
	}
	
	global $wpdb, $table_prefix;
	$table_name = $table_prefix.GLAM_SLIDER_TABLE;
	$post_table = $table_prefix."posts";
	$post_meta_table = $table_prefix."postmeta";
	$rand = $glam_slider_curr['rand'];
	if(isset($rand) and $rand=='1'){
	  $orderby = 'RAND()';
	}
	else {
	  $orderby = 'a.slide_order ASC, a.date DESC';
	}
	$posts = $wpdb->get_results("SELECT * FROM 
	                             $table_name a 
								 LEFT OUTER JOIN $post_table b 
									ON a.post_id = b.ID 
								 WHERE (b.post_status = 'publish' OR (b.post_type='attachment' AND b.post_status = 'inherit')) 
								 AND a.slider_id = '$slider_id'  
	                             ORDER BY ".$orderby." LIMIT $offset, $max_posts", OBJECT);
	
	$r_array=glam_global_posts_processor( $posts, $glam_slider_curr, $out_echo, $set ,$data);
	return $r_array;
}

function get_glam_slider($slider_id='',$set='',$offset=0, $title='',$data=array()) {
	global $glam_slider,$default_glam_slider_settings;
	//Select Settings Set from Meta Box
	if(is_singular() and empty($set)) {
		global $post;
		$sel_set = get_post_meta($post->ID,'_glam_select_set',true);
		if(!empty($sel_set) and $sel_set!='1') $set=$sel_set;
	} 
 	$glam_slider_options='glam_slider_options'.$set;
    $glam_slider_curr=get_option($glam_slider_options);
	if(!isset($glam_slider_curr) or !is_array($glam_slider_curr) or empty($glam_slider_curr)){$glam_slider_curr=$glam_slider;$set='';}
	
	foreach($default_glam_slider_settings as $key=>$value){
		if(!isset($glam_slider_curr[$key])) $glam_slider_curr[$key]='';
	}
	
	$data=array();
	$data['title']=$title;
	 if($glam_slider['multiple_sliders'] == '1' and is_singular()){
		global $post;
		$post_id = $post->ID;
		if(glam_ss_slider_on_this_post($post_id)) $slider_id = get_glam_slider_for_the_post($post_id);
	 }
	if(empty($slider_id) or !isset($slider_id))  $slider_id = '1';
	if( !$offset or empty($offset) or !is_numeric($offset)  ) $offset=0;
	if(!empty($slider_id)){
		$data['slider_id']=$slider_id;
		$slider_handle='glam_slider_'.$slider_id;
		$data['slider_handle']=$slider_handle;
		$r_array = glam_carousel_posts_on_slider($glam_slider_curr['no_posts'], $offset, $slider_id, '0', $set, $data); 
		get_global_glam_slider($slider_handle,$r_array,$glam_slider_curr,$set,$echo='1',$data);
	} //end of not empty slider_id condition
}

//For displaying category specific posts in chronologically reverse order
function glam_carousel_posts_on_slider_category($max_posts='5', $catg_slug='', $offset=0, $out_echo = '1', $set='',$data=array()) {
    global $glam_slider,$default_glam_slider_settings;
	$glam_slider_options='glam_slider_options'.$set;
    $glam_slider_curr=get_option($glam_slider_options);
	if(!isset($glam_slider_curr) or !is_array($glam_slider_curr) or empty($glam_slider_curr)){$glam_slider_curr=$glam_slider;$set='';}
	
	foreach($default_glam_slider_settings as $key=>$value){
		if(!isset($glam_slider_curr[$key])) $glam_slider_curr[$key]='';
	}
	
	global $wpdb, $table_prefix;
	
	if (!empty($catg_slug)) {
		$category = get_category_by_slug($catg_slug); 
		$slider_cat = $category->term_id;
	}
	else {
		$category = get_the_category();
		$slider_cat = $category[0]->cat_ID;
	}
	
	$rand = $glam_slider_curr['rand'];
	if(isset($rand) and $rand=='1') $orderby = '&orderby=rand';
	else $orderby = '';
	
	//extract the posts
	$posts = get_posts('numberposts='.$max_posts.'&offset='.$offset.'&category='.$slider_cat.$orderby);
	
	$r_array=glam_global_posts_processor( $posts, $glam_slider_curr, $out_echo,$set,$data );
	return $r_array;
}

function get_glam_slider_category($catg_slug='', $set='', $offset=0,$title='',$data=array()) {
    global $glam_slider,$default_glam_slider_settings; 
	//Select Settings Set from Meta Box
	if(is_singular() and empty($set)) {
		global $post;
		$sel_set = get_post_meta($post->ID,'_glam_select_set',true);
		if(!empty($sel_set) and $sel_set!='1') $set=$sel_set;
	}	
 	$glam_slider_options='glam_slider_options'.$set;
    $glam_slider_curr=get_option($glam_slider_options);
	if(!isset($glam_slider_curr) or !is_array($glam_slider_curr) or empty($glam_slider_curr)){$glam_slider_curr=$glam_slider;$set='';}
	
	foreach($default_glam_slider_settings as $key=>$value){
		if(!isset($glam_slider_curr[$key])) $glam_slider_curr[$key]='';
	}
	
	$data=array();
	$data['title']=$title;
	if( !$offset or empty($offset) or !is_numeric($offset)  ) $offset=0;
	$slider_handle='glam_slider_'.$catg_slug;
	$data['slider_handle']=$slider_handle;
    $r_array = glam_carousel_posts_on_slider_category($glam_slider_curr['no_posts'], $catg_slug, $offset, '0', $set, $data); 
	get_global_glam_slider($slider_handle,$r_array,$glam_slider_curr,$set,$echo='1',$data);
} 

//For displaying recent posts in chronologically reverse order
function glam_carousel_posts_on_slider_recent($max_posts='5', $offset=0, $out_echo = '1', $set='',$data=array()) {
     global $glam_slider,$default_glam_slider_settings;
	$glam_slider_options='glam_slider_options'.$set;
    $glam_slider_curr=get_option($glam_slider_options);
	if(!isset($glam_slider_curr) or !is_array($glam_slider_curr) or empty($glam_slider_curr)){$glam_slider_curr=$glam_slider;$set='';}
	
	foreach($default_glam_slider_settings as $key=>$value){
		if(!isset($glam_slider_curr[$key])) $glam_slider_curr[$key]='';
	}
	
	$rand = $glam_slider_curr['rand'];
	if(isset($rand) and $rand=='1')	  $orderby = '&orderby=rand';
	else  $orderby = '';
	//extract posts data
	$posts = get_posts('numberposts='.$max_posts.'&offset='.$offset.$orderby);
	$r_array=glam_global_posts_processor( $posts, $glam_slider_curr, $out_echo,$set,$data );
	return $r_array;
}

function get_glam_slider_recent($set='',$offset=0,$title='',$data=array()) {
	global $glam_slider,$default_glam_slider_settings; 
	//Select Settings Set from Meta Box
	if(is_singular() and empty($set)) {
		global $post;
		$sel_set = get_post_meta($post->ID,'_glam_select_set',true);
		if(!empty($sel_set) and $sel_set!='1') $set=$sel_set;
	}
 	$glam_slider_options='glam_slider_options'.$set;
    $glam_slider_curr=get_option($glam_slider_options);
	if(!isset($glam_slider_curr) or !is_array($glam_slider_curr) or empty($glam_slider_curr)){$glam_slider_curr=$glam_slider;$set='';}
	
	foreach($default_glam_slider_settings as $key=>$value){
		if(!isset($glam_slider_curr[$key])) $glam_slider_curr[$key]='';
	}
	
	$data=array();
	$data['title']=$title;
	if( !$offset or empty($offset) or !is_numeric($offset)  ) $offset=0;
	$slider_handle='glam_slider_recent';
	$data['slider_handle']=$slider_handle;
	$r_array = glam_carousel_posts_on_slider_recent($glam_slider_curr['no_posts'], $offset, '0', $set,$data);
	get_global_glam_slider($slider_handle,$r_array,$glam_slider_curr,$set,$echo='1',$data);
} 

require_once (dirname (__FILE__) . '/shortcodes_1.php');
require_once (dirname (__FILE__) . '/widgets_1.php');

function glam_slider_enqueue_scripts() {
	wp_enqueue_script( 'jquery');
}
add_action( 'init', 'glam_slider_enqueue_scripts' );

//admin settings
function glam_slider_admin_scripts() {
global $glam_slider;
  if ( is_admin() ){ // admin actions
  // Settings page only
	if ( isset($_GET['page']) && ('glam-slider-admin' == $_GET['page'] or 'glam-slider-settings' == $_GET['page'] )  ) {
	wp_register_script('jquery', false, false, false, false);
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'glam_slider_admin_js', glam_slider_plugin_url( 'js/admin.js' ),
		array('jquery'), GLAM_SLIDER_VER, false);
	wp_enqueue_style( 'glam_slider_admin_css', glam_slider_plugin_url( 'css/admin.css' ),
		false, GLAM_SLIDER_VER, 'all');
	
	wp_enqueue_script( 'glam', glam_slider_plugin_url( 'js/glam.js' ),
		array('jquery'), GLAM_SLIDER_VER, false);
	wp_enqueue_script( 'easing', glam_slider_plugin_url( 'js/jquery.easing.js' ),
		false, GLAM_SLIDER_VER, false);
	wp_enqueue_style( 'glam_slider_admin_head_css', glam_slider_plugin_url( 'css/skins/'.$glam_slider['stylesheet'].'/style.css' ),false, GLAM_SLIDER_VER, 'all');
	wp_enqueue_script( 'jquery.bpopup.min', glam_slider_plugin_url( 'js/jquery.bpopup.min.js' ),'', GLAM_SLIDER_VER, false);
	}
  }
}

add_action( 'admin_init', 'glam_slider_admin_scripts' );

function glam_slider_admin_head() {
global $glam_slider;
if ( is_admin() ){ // admin actions
// Sliders & Settings page only
    if ( isset($_GET['page']) && ('glam-slider-admin' == $_GET['page'] or 'glam-slider-settings' == $_GET['page']) ) {
	  $sliders = glam_ss_get_sliders(); 
		global $glam_slider;
		$cntr='';
		if(isset($_GET['scounter'])) $cntr = $_GET['scounter'];
		$glam_slider_options='glam_slider_options'.$cntr;
		$glam_slider_curr=get_option($glam_slider_options);
		$active_tab=(isset($glam_slider_curr['active_tab']))?$glam_slider_curr['active_tab']:0;
		if ( isset($_GET['page']) && ('glam-slider-admin' == $_GET['page']) ){ if(isset($_POST['active_tab']) ) $active_tab=$_POST['active_tab'];else $active_tab = 0;}
		if(empty($active_tab)){$active_tab=0;}
	?>
		<script type="text/javascript">
            // <![CDATA[
        jQuery(document).ready(function() {
                jQuery(function() {
					jQuery("#slider_tabs").tabs({fx: { opacity: "toggle", duration: 300}, active: <?php echo $active_tab;?> }).addClass( "ui-tabs-vertical-left ui-helper-clearfix" );jQuery( "#slider_tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
				<?php 	if ( isset($_GET['page']) && (( 'glam-slider-settings' == $_GET['page']) or ('glam-slider-admin' == $_GET['page']) ) ) { ?>
					jQuery( "#slider_tabs" ).on( "tabsactivate", function( event, ui ) { jQuery( "#glam_activetab, .glam_activetab" ).val( jQuery( "#slider_tabs" ).tabs( "option", "active" ) ); });
				<?php 	}
				foreach($sliders as $slider){ ?>
                    jQuery("#sslider_sortable_<?php echo $slider['slider_id'];?>").sortable();
                    jQuery("#sslider_sortable_<?php echo $slider['slider_id'];?>").disableSelection();
			    <?php } ?>
		     jQuery( ".uploaded-images" ).sortable({ items: ".addedImg" });
                });
        });
		
        function confirmRemove()
        {
            var agree=confirm("This will remove selected Posts/Pages from Slider.");
            if (agree)
            return true ;
            else
            return false ;
        }
        function confirmRemoveAll()
        {
            var agree=confirm("Remove all Posts/Pages from Glam Slider??");
            if (agree)
            return true ;
            else
            return false ;
        }
        function confirmSliderDelete()
        {
            var agree=confirm("Delete this Slider??");
            if (agree)
            return true ;
            else
            return false ;
        }
        function slider_checkform ( form )
        {
          if (form.new_slider_name.value == "") {
            alert( "Please enter the New Slider name." );
            form.new_slider_name.focus();
            return false ;
          }
          return true ;
        }
        </script>
<?php
   } //Sliders page only
   
   // Settings page only
  if ( isset($_GET['page']) && 'glam-slider-settings' == $_GET['page']  ) {
		wp_enqueue_style( 'wp-color-picker' );
   		wp_enqueue_script( 'wp-color-picker' );
?>
<script type="text/javascript">
	// <![CDATA[
jQuery(document).ready(function() {
	jQuery('.wp-color-picker-field').wpColorPicker();
//		
	jQuery('#sldr_close').click(function () {
		jQuery('#sldr_message').fadeOut("slow");
	});
});
function confirmSettingsCreate()
        {
            var agree=confirm("Create New Settings Set??");
            if (agree)
            return true ;
            else
            return false ;
}
function confirmSettingsDelete()
        {
            var agree=confirm("Delete this Settings Set??");
            if (agree)
            return true ;
            else
            return false ;
}
</script>
	<style type="text/css">.color-picker-wrap {position: absolute;	display: none; background: #fff;border: 3px solid #ccc;	padding: 3px;z-index: 1000;}</style>
	<?php
   } //for glam slider option page  
 }//only for admin
//Below css will add the menu icon for Glam Slider admin menu
?>
<style type="text/css">#adminmenu #toplevel_page_glam-slider-admin div.wp-menu-image:before { content: "\f233"; }</style>
<?php
}
add_action('admin_head', 'glam_slider_admin_head');

//get inline css with style attribute attached/not attached
function glam_get_inline_css($set='',$echo='0'){
    global $glam_slider,$default_glam_slider_settings;
	$glam_slider_options='glam_slider_options'.$set;
    $glam_slider_curr=get_option($glam_slider_options);
	if(!isset($glam_slider_curr) or !is_array($glam_slider_curr) or empty($glam_slider_curr)){$glam_slider_curr=$glam_slider;$set='';}
	
	foreach($default_glam_slider_settings as $key=>$value){
		if(!isset($glam_slider_curr[$key])) $glam_slider_curr[$key]='';
	}
		
	//If no Skin specified, consider Default
	$skin='default';
	if(isset($glam_slider_curr['stylesheet'])) $skin=$glam_slider_curr['stylesheet'];
	if(empty($skin))$skin='default';
	
	//Always include Default Skin
	require_once ( dirname( dirname(__FILE__) ) . '/css/skins/default/functions.php');
	//Include Skin function file
	if($skin!='default' and file_exists(dirname( dirname(__FILE__) ) . '/css/skins/'.$skin.'/functions.php'))require_once ( dirname( dirname(__FILE__) ) . '/css/skins/'.$skin.'/functions.php');
	
	//Skin specific data processor and html generation
	$data_processor_fn='glam_data_processor_'.$skin;
	if(function_exists($data_processor_fn))$default=true;
	else $default=false;

	$glam_slider_css=array('glam_slider'=>'',
				'title_fstyle'=>'',
				'sldr_title'=>'',
				'glam_slider_instance'=>'',
				'glam_slide'=>'',
				'glam_slide_content'=>'',
				'glam_slideri_br'=>'',
				'glam_slideri'=>'',
				'glam_slider_h4'=>'',
				'glam_slider_h4_a'=>'',
				'glam_slider_span'=>'',
				'glam_slider_thumbnail'=>'',
				'glam_slider_eshortcode'=>'',
				'glam_slider_p_more'=>'',
				'glam_side_link'=>'',
				'glam_slides_wrap'=>'',
				'glam_items'=>'',
				'glam_meta'=>'',
				'glam_next'=>'',
				'glam_prev'=>'',
				'glam_nav'=>'',
				'glam_nav_a'=>'');
	if($default){
		$style_start= ($echo=='0') ? 'style="':'';
		$style_end= ($echo=='0') ? '"':'';
		
		$height = isset($glam_slider_curr['height']) ? $glam_slider_curr['height'] : '280' ;
		$padding = isset($glam_slider_curr['padding']) ? $glam_slider_curr['padding'] : '10' ;
		$swidth = isset($glam_slider_curr['swidth']) ? $glam_slider_curr['swidth'] : '120' ;
		$visible = isset($glam_slider_curr['visible']) ? $glam_slider_curr['visible'] : '1' ;
		$visible = ( $visible > 0 ) ? $visible : '1' ;
		
	
	//glam_slider
		if(isset($glam_slider_curr['width']) and $glam_slider_curr['width']!=0) $glam_slider_css['glam_slider']=$style_start.'width:'. ( $glam_slider_curr['width']  ) .'px;max-width:'. ( $glam_slider_curr['width']  ) .'px;'.$style_end;
		else $glam_slider_css['glam_slider']=$style_start.'width:100%;'.$style_end;
	
	//sldr_title
		$title_fontg=isset($glam_slider_curr['title_fontg'])?trim($glam_slider_curr['title_fontg']):'';
		if(!empty($title_fontg)) 	{
			wp_enqueue_style( 'glam_title', 'http://fonts.googleapis.com/css?family='.$title_fontg,array(),GLAM_SLIDER_VER);
			$title_fontg=glam_get_google_font($title_fontg);
			$title_fontg=$title_fontg.',';
		}
		if ($glam_slider_curr['title_fstyle'] == "bold" or $glam_slider_curr['title_fstyle'] == "bold italic" ){$slider_title_font = "bold";} else { $slider_title_font = "normal"; }
		if ($glam_slider_curr['title_fstyle'] == "italic" or $glam_slider_curr['title_fstyle'] == "bold italic" ){$slider_title_style = "italic";} else {$slider_title_style = "normal";}
		$sldr_title = $glam_slider_curr['title_text']; if(!empty($sldr_title)) { $slider_title_margin = "5px 0 10px 0"; } else {$slider_title_margin = "0";} 	
		$glam_slider_css['sldr_title']=$style_start.'font-family:'. $title_fontg . ' '.$glam_slider_curr['title_font'].';font-size:'.$glam_slider_curr['title_fsize'].'px;line-height:'.($glam_slider_curr['title_fsize'] + 5).'px;font-weight:'.$slider_title_font.';font-style:'.$slider_title_style.';color:'.$glam_slider_curr['title_fcolor'].';margin:'.$slider_title_margin.$style_end;
	
	//glam_slides_wrap
		if ($glam_slider_curr['bg'] == '1') { $glam_slideri_bg = "transparent";} else { $glam_slideri_bg = $glam_slider_curr['bg_color']; }
		$glam_slider_css['glam_slides_wrap']=$style_start.'background-color:'.$glam_slideri_bg.';border:'.$glam_slider_curr['border'].'px solid '.$glam_slider_curr['brcolor'].';'.$style_end;
	
	//glam_items
		$glam_slider_css['glam_items']=$style_start.'margin-left:'.$swidth.'px;'.$style_end;
	
	//glam_slider_instance
		$glam_slider_css['glam_slider_instance']=$style_start.'height:'.$height.'px;margin:'.$padding.'px;'.$style_end;
		
	//glam_slide
		$glam_slider_css['glam_slide']=$style_start.'height:'.$height.'px;'.$style_end;
		
	//glam_slideri
		$slideri_w=($glam_slider_curr['iwidth']/$visible) - (($glam_slider_curr['padding']/2) * ($visible - 1));
		$glam_slider_css['glam_slideri']=$style_start.'width:'. $slideri_w .'px;height:'. $glam_slider_curr['height'].'px;margin-left:'.( $padding/2 ).'px;margin-right:'.( $padding/2 ).'px;'.$style_end;
	
	//glam_slide_content
		$hex=isset($glam_slider_curr['content_bg'])? $glam_slider_curr['content_bg'] : '#ECECEC';
		$c_rgb_bg=glam_hex2rgb($hex);
		$r=isset($c_rgb_bg[0])? $c_rgb_bg[0] : '236';
		$g=isset($c_rgb_bg[1])? $c_rgb_bg[1] : '236';
		$b=isset($c_rgb_bg[2])? $c_rgb_bg[2] : '236';
		$c_o=isset($glam_slider_curr['content_opacity'])? $glam_slider_curr['content_opacity'] : '0.85';
		$content_bg='rgba('.$r.','.$g.','.$b.','.$c_o.')';
		$hex = str_replace("#", "", $hex);
		$colorstr='#AA'.$hex;
		$glam_slider_css['glam_slide_content']=$style_start.'background-color: '.$content_bg.';filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0,startColorstr=\''.$colorstr.'\',endColorstr=\''.$colorstr.'\');'.$style_end;

	//glam_slider_h4		
		$ptitle_fontg=isset($glam_slider_curr['ptitle_fontg'])?trim($glam_slider_curr['ptitle_fontg']):'';
		if(!empty($ptitle_fontg)) 	{
			wp_enqueue_style( 'glam_ptitle', 'http://fonts.googleapis.com/css?family='.$ptitle_fontg,array(),GLAM_SLIDER_VER);
			$ptitle_fontg=glam_get_google_font($ptitle_fontg);
			$ptitle_fontg=$ptitle_fontg.',';
		}
		if ($glam_slider_curr['ptitle_fstyle'] == "bold" or $glam_slider_curr['ptitle_fstyle'] == "bold italic" ){$ptitle_fweight = "bold";} else {$ptitle_fweight = "normal";}
		if ($glam_slider_curr['ptitle_fstyle'] == "italic" or $glam_slider_curr['ptitle_fstyle'] == "bold italic"){$ptitle_fstyle = "italic";} else {$ptitle_fstyle = "normal";}
		$glam_slider_css['glam_slider_h4']=$style_start.'clear:none;line-height:'. ($glam_slider_curr['ptitle_fsize'] + 5) .'px;font-family:'. $ptitle_fontg . ' ' . $glam_slider_curr['ptitle_font'].';font-size:'.$glam_slider_curr['ptitle_fsize'].'px;font-weight:'.$ptitle_fweight.';font-style:'.$ptitle_fstyle.';color:'.$glam_slider_curr['ptitle_fcolor'].';margin:0 0 5px 0;'.$style_end;
		
	//glam_slider_h4 a
		$glam_slider_css['glam_slider_h4_a']=$style_start.'font-family:'. $ptitle_fontg . ' ' . $glam_slider_curr['ptitle_font'].';font-size:'.$glam_slider_curr['ptitle_fsize'].'px;font-weight:'.$ptitle_fweight.';font-style:'.$ptitle_fstyle.';color:'.$glam_slider_curr['ptitle_fcolor'].';'.$style_end;
	
	//glam_slider_span	
		$content_fontg=isset($glam_slider_curr['content_fontg'])?trim($glam_slider_curr['content_fontg']):'';
		if(!empty($content_fontg)) 	{
			wp_enqueue_style( 'glam_content', 'http://fonts.googleapis.com/css?family='.$content_fontg,array(),GLAM_SLIDER_VER);
			$content_fontg=glam_get_google_font($content_fontg);
			$content_fontg=$content_fontg.',';
		}
		if ($glam_slider_curr['content_fstyle'] == "bold" or $glam_slider_curr['content_fstyle'] == "bold italic" ){$content_fweight= "bold";} else {$content_fweight= "normal";}
		if ($glam_slider_curr['content_fstyle']=="italic" or $glam_slider_curr['content_fstyle'] == "bold italic"){$content_fstyle= "italic";} else {$content_fstyle= "normal";}
		$glam_slider_css['glam_slider_span']=$style_start.'font-family:'. $content_fontg . ' '.$glam_slider_curr['content_font'].';font-size:'.$glam_slider_curr['content_fsize'].'px;line-height:'.($glam_slider_curr['content_fsize'] + 3).'px;font-weight:'.$content_fweight.';font-style:'.$content_fstyle.';color:'. $glam_slider_curr['content_fcolor'].';'.$style_end;
		
	//glam_slider_thumbnail
		$s_opacity=(isset($glam_slider_curr['s_opacity']))?$glam_slider_curr['s_opacity']:'0.5';
		$glam_slider_css['glam_slider_thumbnail']=$style_start.'height:'.$height.'px;border:'.$glam_slider_curr['img_border'].'px solid '.$glam_slider_curr['img_brcolor'].';opacity: '.$s_opacity.';-moz-opacity: '.$s_opacity.';filter: alpha(opacity='. ($s_opacity*100) .');'.$style_end;
	
	//glam_slider_p_more
		$glam_slider_css['glam_slider_p_more']=$style_start.'color:'.$glam_slider_curr['ptitle_fcolor'].';font-family:'. $content_fontg . ' '.$glam_slider_curr['content_font'].';font-size:'.$glam_slider_curr['content_fsize'].'px;'.$style_end;
	
	//glam_next
	      $nexturl='css/buttons/'.$glam_slider_curr['buttons'].'/next.png';
		$glam_slider_css['glam_next']=$style_start.'background-image:url('.glam_slider_plugin_url( $nexturl ) .');top:'.$glam_slider_curr['navtop'].'%;'.$style_end;
	
	//glam_prev
	    $prevurl='css/buttons/'.$glam_slider_curr['buttons'].'/prev.png';
		$glam_slider_css['glam_prev']=$style_start.'background-image:url('.glam_slider_plugin_url( $prevurl ) .');top:'.$glam_slider_curr['navtop'].'%;'.$style_end;
	
	//glam_nav
		$glam_slider_css['glam_nav']='';
		
	//glam_nav_a
		$buttons_url='css/buttons/'.$glam_slider_curr['buttons'].'/nav.png';
		$glam_slider_css['glam_nav_a']='background: transparent url('.glam_slider_plugin_url( $buttons_url ) .') no-repeat top left;';
	//glam_side_link
		$glam_slider_css['glam_side_link']=$style_start.'width:'.( $swidth + $padding ).'px;height:'.( $height + 2*$padding ).'px;'.$style_end;
	}
	return $glam_slider_css;
}

function glam_slider_css() {
global $glam_slider;
$css=$glam_slider['css'];
if($css and !empty($css)){?>
 <style type="text/css"><?php echo $css;?></style>
<?php }
}
add_action('wp_head', 'glam_slider_css');
add_action('admin_head', 'glam_slider_css');
function glam_custom_css_js() {
	global $glam_slider;
	$css=$glam_slider['css_js'];
	$line_breaks = array("\r\n", "\n", "\r");
	$css = str_replace($line_breaks, "", $css);
	if($css and !empty($css)){
		if( ( is_admin() and isset($_GET['page']) and 'glam-slider-settings' == $_GET['page']) or !is_admin() ){	?>
			<script type="text/javascript">jQuery(document).ready(function() { jQuery("head").append("<style type=\"text/css\"><?php echo $css;?></style>"); }) </script>
<?php 	}
	}
}
add_action('wp_footer', 'glam_custom_css_js');
add_action('admin_footer', 'glam_custom_css_js');
?>
