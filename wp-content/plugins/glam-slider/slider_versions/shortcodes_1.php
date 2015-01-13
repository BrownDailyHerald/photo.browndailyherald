<?php 
function return_global_glam_slider($slider_handle,$r_array,$glam_slider_curr,$set,$echo='0',$data=array()){
	$slider_html='';
	$slider_html=get_global_glam_slider($slider_handle,$r_array,$glam_slider_curr,$set,$echo,$data);
	return $slider_html;
}
function return_glam_slider($slider_id='',$set='',$offset=0,$data=array()) {
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
 
	if($glam_slider['multiple_sliders'] == '1' and is_singular()){
		global $post;
		$post_id = $post->ID;
		if(glam_ss_slider_on_this_post($post_id)) $slider_id = get_glam_slider_for_the_post($post_id);
	}
	if(empty($slider_id) or !isset($slider_id))  $slider_id = '1';
	if( !$offset or empty($offset) or !is_numeric($offset)  ) $offset=0;
	$slider_html='';
	if(!empty($slider_id)){
		$data['slider_id']=$slider_id;
		$slider_handle='glam_slider_'.$slider_id;
		$data['slider_handle']=$slider_handle;
		$r_array = glam_carousel_posts_on_slider($glam_slider_curr['no_posts'], $offset, $slider_id, $echo = '0', $set, $data); 
		$slider_html=return_global_glam_slider($slider_handle,$r_array,$glam_slider_curr,$set,$echo='0',$data);
	} //end of not empty slider_id condition
	
	return $slider_html;
}

function glam_slider_simple_shortcode($atts) {
	extract(shortcode_atts(array(
		'id' => '',
		'set' => '',
		'offset' => '',
	), $atts));
	$data=array();
	return return_glam_slider($id,$set,$offset,$data);
}
add_shortcode('glamslider', 'glam_slider_simple_shortcode');

//Category shortcode
function return_glam_slider_category($catg_slug='',$set='',$offset=0, $data=array()) {
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
	if( !$offset or empty($offset) or !is_numeric($offset)  ) $offset=0;
    $slider_handle='glam_slider_'.$catg_slug;
	$data['slider_handle']=$slider_handle;
	$r_array = glam_carousel_posts_on_slider_category($glam_slider_curr['no_posts'], $catg_slug, $offset, '0', $set,$data); 
	//get slider 
	$slider_html=return_global_glam_slider($slider_handle,$r_array,$glam_slider_curr,$set,$echo='0',$data);
	
	return $slider_html;
}

function glam_slider_category_shortcode($atts) {
	extract(shortcode_atts(array(
		'catg_slug' => '',
		'set' => '',
		'offset' => '',
	), $atts));
	$data=array();
	return return_glam_slider_category($catg_slug,$set,$offset,$data);
}
add_shortcode('glamcategory', 'glam_slider_category_shortcode');

//Recent Posts Shortcode
function return_glam_slider_recent($set='',$offset=0, $data=array()) {
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
	if( !$offset or empty($offset) or !is_numeric($offset)  ) $offset=0;
	$slider_handle='glam_slider_recent';
	$data['slider_handle']=$slider_handle;
	$r_array = glam_carousel_posts_on_slider_recent($glam_slider_curr['no_posts'], $offset, '0', $set,$data); 
	//get slider 
	$slider_html=return_global_glam_slider($slider_handle,$r_array,$glam_slider_curr,$set,$echo='0',$data);
	
	return $slider_html;
}

function glam_slider_recent_shortcode($atts) {
	extract(shortcode_atts(array(
		'set' => '',
		'offset' => '',
	), $atts));
	$data=array();
	return return_glam_slider_recent($set,$offset,$data);
}
add_shortcode('glamrecent', 'glam_slider_recent_shortcode');
?>
