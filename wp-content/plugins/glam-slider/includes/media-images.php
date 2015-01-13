<?php
//For media files
function glam_slider_media_lib_edit($form_fields, $post){
global $wp_version;
if ( version_compare( $wp_version, '3.5', '<' ) ) : // Using WordPress less than 3.5
	global $glam_slider;
	if (current_user_can( $glam_slider['user_level'] )) {
		$remove_post_type_arr=$glam_slider['remove_metabox'];
		if(!isset($remove_post_type_arr) or !is_array($remove_post_type_arr) ) $remove_post_type_arr=array();
		if(!in_array('attachment',$remove_post_type_arr)){
			if ( substr($post->post_mime_type, 0, 5) == 'image') {
				$post_id = $post->ID;
				$extra = "";

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
				
					  
			  $form_fields['glam-slider'] = array(
					  'label'      => __('Check the box and select the slider','glam-slider'),
					  'input'      => 'html',
					  'html'       => "<input type='checkbox' style='margin-top:6px;' name='attachments[".$post->ID."][glam-slider]' value='glam-slider' ".$extra." /> &nbsp; <strong>".__( 'Add this Image to ', 'glam-slider' )."</strong>",
					  'value'      => 'glam-slider'
				   );
			  
			  $sname_html='';
		 
			  foreach ($sliders as $slider) { 
				 if(in_array($slider['slider_id'],$post_slider_arr)){$selected = 'selected';} else{$selected='';}
				 $sname_html =$sname_html.'<option value="'.$slider['slider_id'].'" '.$selected.'>'.$slider['slider_name'].'</option>';
			  } 
			  $form_fields['glam_slider_name[]'] = array(
					  'label'      => __(''),
					  'input'      => 'html',
					  'html'       => '<select name="attachments['.$post->ID.'][glam_slider_name][]" multiple="multiple" size="3">'.$sname_html.'</select>',
					  'value'      => ''
				   );
			 
			 $glam_link_attr=get_post_meta($post_id, 'glam_link_attr', true);
			 $glam_sslider_link= get_post_meta($post_id, 'glam_slide_redirect_url', true);  
			 $glam_sslider_nolink=get_post_meta($post_id, 'glam_sslider_nolink', true);
			 if($glam_sslider_nolink=='1'){$checked= "checked";} else {$checked= "";}
			 $form_fields['glam_link_attr'] = array(
					  'label'      => __('Slide Link (anchor) attributes','glam-slider'),
					  'input'      => 'html',
					  'html'       => "<input type='text' style='clear:left;' class='text urlfield' name='attachments[".$post->ID."][glam_link_attr]' value='" . esc_attr($glam_link_attr) . "' /><br /><small>".__( '(e.g. target="_blank" rel="external nofollow")', 'glam-slider' )."</small>",
					  'value'      => $glam_link_attr
				   );
			 $form_fields['glam_sslider_link'] = array(
					  'label'      => __('Glam Slide Link URL','glam-slider'),
					  'input'      => 'html',
					  'html'       => "<input type='text' style='clear:left;' class='text urlfield' name='attachments[".$post->ID."][glam_sslider_link]' value='" . esc_attr($glam_sslider_link) . "' /><br /><small>".__( '(If left empty, it will be by default linked to attachment permalink.)', 'glam-slider' )."</small>",
					  'value'      => $glam_sslider_link
				   );
			 $form_fields['glam_sslider_nolink'] = array(
					  'label'      => __('Do not link this slide to any page(url)','glam-slider'),
					  'input'      => 'html',
					  'html'       => "<input type='checkbox' name='attachments[".$post->ID."][glam_sslider_nolink]' value='1' ".$checked." />",
					  'value'      => 'glam-slider'
				   );
		  }
		  else {
			 unset( $form_fields['glam-slider'] );
			 unset( $form_fields['glam_slider_name[]'] );
			 unset( $form_fields['glam_sslider_link'] );
			 unset( $form_fields['glam_sslider_nolink'] );
			 unset( $form_fields['glam_link_attr'] );
		  }
		} //attachment post type
	} //current user can
endif; //less than WP 3.5
return $form_fields;
}

add_filter('attachment_fields_to_edit', 'glam_slider_media_lib_edit', 10, 2);

function glam_slider_media_lib_save($post, $attachment){
global $wp_version;
if ( version_compare( $wp_version, '3.5', '<' ) ) : // Using WordPress less than 3.5
	global $glam_slider;
	if (current_user_can( $glam_slider['user_level'] )) {
		$remove_post_type_arr=$glam_slider['remove_metabox'];
		if(!isset($remove_post_type_arr) or !is_array($remove_post_type_arr) ) $remove_post_type_arr=array();
		if(!in_array('attachment',$remove_post_type_arr)){
			global $wpdb, $table_prefix;
			$table_name = $table_prefix.GLAM_SLIDER_TABLE;
			$post_id=$post['ID'];
			
			if( !isset($attachment['placid-slider']) and  is_post_on_any_placid_slider($post_id) ){
				$sql = "DELETE FROM $table_name where post_id = '$post_id'";
				 $wpdb->query($sql);
			}
			
			if(isset($attachment['glam-slider']) and !isset($attachment['glam_slider_name'])) {
			  $slider_id = '1';
			  if(is_post_on_any_glam_slider($post_id)){
				 $sql = "DELETE FROM $table_name where post_id = '$post_id'";
				 $wpdb->query($sql);
			  }
			  
			  if(isset($attachment['glam-slider']) and $attachment['glam-slider'] == "glam-slider" and !glam_slider($post_id,$slider_id)) {
				$dt = date('Y-m-d H:i:s');
				$sql = "INSERT INTO $table_name (post_id, date, slider_id) VALUES ('$post_id', '$dt', '$slider_id')";
				$wpdb->query($sql);
			  }
			}
			if(isset($attachment['glam-slider']) and $attachment['glam-slider'] == "glam-slider" and isset($attachment['glam_slider_name'])){
			  $slider_id_arr = $attachment['glam_slider_name'];
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
			
			$glam_link_attr = get_post_meta($post_id,'glam_link_attr',true);
			$link_attr=htmlentities($_POST['glam_link_attr'],ENT_QUOTES);
			if($glam_link_attr != $link_attr) {
			  update_post_meta($post_id, 'glam_link_attr', $link_attr);	
			}
		
			$glam_sslider_link = get_post_meta($post_id,'glam_slide_redirect_url',true);
			$link=$attachment['glam_sslider_link'];
			if($glam_sslider_link != $link) {
			  update_post_meta($post_id, 'glam_slide_redirect_url', $link);	
			}
			
			$glam_sslider_nolink = get_post_meta($post_id,'glam_sslider_nolink',true);
			if($glam_sslider_nolink != $attachment['glam_sslider_nolink']) {
			  update_post_meta($post_id, 'glam_sslider_nolink', $attachment['glam_sslider_nolink']);	
			}
		} //attachment post type
	} //current user can
endif; //less than WP 3.5
return $post;
} 

add_filter('attachment_fields_to_save', 'glam_slider_media_lib_save', 10, 2);
?>