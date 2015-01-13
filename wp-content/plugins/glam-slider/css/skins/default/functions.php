<?php 
function glam_post_processor_default($posts, $glam_slider_curr,$out_echo,$set,$data=array()){
	$skin='default'; 
		global $glam_slider;
	$glam_slider_css = glam_get_inline_css($set);
	$html = '';
	$glam_sldr_j = $i = 0;
	$page_close='';
	
	$timthumb='1';
	if($glam_slider_curr['timthumb']=='1'){
		$timthumb='0';
	}
	
	$visible = isset($glam_slider_curr['visible']) ? $glam_slider_curr['visible'] : '1' ;
	$visible = ( $visible > 0 ) ? $visible : '1' ;
	
	foreach($posts as $post) {
		$id = $post->post_id;
		$post_id = $post->ID;
		$post_title = stripslashes($post->post_title);
		$post_title = str_replace('"', '', $post_title);
		//filter hook
		$post_title=apply_filters('glam_post_title',$post_title,$post_id,$glam_slider_curr,$glam_slider_css);
		$slider_content = $post->post_content;
		
		$glam_slide_redirect_url = get_post_meta($post_id, 'glam_slide_redirect_url', true);
		$glam_sslider_nolink = get_post_meta($post_id,'glam_sslider_nolink',true);
		trim($glam_slide_redirect_url);
		if(!empty($glam_slide_redirect_url) and isset($glam_slide_redirect_url)) {
		   $permalink = $glam_slide_redirect_url;
		}
		else{
		   $permalink = get_permalink($post_id);
		}
		if($glam_sslider_nolink=='1'){
		  $permalink='';
		}
		
		if($i%$glam_slider_curr['visible'] == 0){
		$html .= '<div class="glam_slide" '.$glam_slider_css['glam_slide'].'>
			<!-- glam_slide -->';
			$glam_sldr_j++;
		}
		
		$html .= '<div class="glam_slideri" '.$glam_slider_css['glam_slideri'].'>
			<!-- glam_slideri -->';
		
		if($glam_slider_curr['show_content']=='1'){
			if ($glam_slider_curr['content_from'] == "slider_content") {
				$slider_content = get_post_meta($post_id, 'slider_content', true);
			}
			if ($glam_slider_curr['content_from'] == "excerpt") {
				$slider_content = $post->post_excerpt;
			}

			$slider_content = strip_shortcodes( $slider_content );

			$slider_content = stripslashes($slider_content);
			$slider_content = str_replace(']]>', ']]&gt;', $slider_content);
	
			$slider_content = str_replace("\n","<br />",$slider_content);
			$slider_content = strip_tags($slider_content, $glam_slider_curr['allowable_tags']);
		
			$content_limit=$glam_slider_curr['content_limit'];
			$content_chars=$glam_slider_curr['content_chars'];
			if(empty($content_limit) && !empty($content_chars)){ 
				$slider_excerpt = substr($slider_content,0,$content_chars);
			}
			else{ 
				$slider_excerpt = glam_slider_word_limiter( $slider_content, $limit = $content_limit);
			}
			if(!isset($slider_excerpt))$slider_excerpt='';
			//filter hook
			$slider_excerpt=apply_filters('glam_slide_excerpt',$slider_excerpt,$post_id,$glam_slider_curr,$glam_slider_css);
			$slider_excerpt='<span '.$glam_slider_css['glam_slider_span'].'> '.$slider_excerpt.'</span>';
		}
		else{
		    $slider_excerpt='';
		}
		//filter hook
			$slider_excerpt=apply_filters('glam_slide_excerpt_html',$slider_excerpt,$post_id,$glam_slider_curr,$glam_slider_css);
		
		$glam_fields=$glam_slider_curr['fields'];		
		$fields_html='';
		if($glam_fields and !empty($glam_fields) ){
			$fields=explode( ',', $glam_fields );
			if($fields){
				foreach($fields as $field) {
					$field_val = get_post_meta($post_id, $field, true);
					if( $field_val and !empty($field_val) )
						$fields_html .='<div class="glam_'.$field.' glam_fields">'.$field_val.'</div>';
				}
			}
		}

//All images
	
		$glam_media = get_post_meta($post_id,'glam_media',true);
		if(!isset($glam_slider_curr['img_pick'][0])) $glam_slider_curr['img_pick'][0]='';
		if(!isset($glam_slider_curr['img_pick'][2])) $glam_slider_curr['img_pick'][2]='';
		if(!isset($glam_slider_curr['img_pick'][3])) $glam_slider_curr['img_pick'][3]='';
		if(!isset($glam_slider_curr['img_pick'][5])) $glam_slider_curr['img_pick'][5]='';
		if($glam_slider_curr['img_pick'][0] == '1'){
		 $custom_key = array($glam_slider_curr['img_pick'][1]);
		}
		else {
		 $custom_key = '';
		}
		
		if($glam_slider_curr['img_pick'][2] == '1'){
		 $the_post_thumbnail = true;
		}
		else {
		 $the_post_thumbnail = false;
		}
		
		if($glam_slider_curr['img_pick'][3] == '1'){
		 $attachment = true;
		 $order_of_image = $glam_slider_curr['img_pick'][4];
		}
		else{
		 $attachment = false;
		 $order_of_image = '1';
		}
		
		if($glam_slider_curr['img_pick'][5] == '1'){
			 $image_scan = true;
		}
		else {
			 $image_scan = false;
		}

		$gti_width = ($glam_slider_curr['iwidth']/$visible) - (($glam_slider_curr['padding']/2) * ($visible - 1));
	    $gti_height = $glam_slider_curr['height'];
		
		if($glam_slider_curr['crop'] == '0'){
		 $extract_size = 'full';
		}
		elseif($glam_slider_curr['crop'] == '1'){
		 $extract_size = 'large';
		}
		elseif($glam_slider_curr['crop'] == '2'){
		 $extract_size = 'medium';
		}
		else{
		 $extract_size = 'thumbnail';
		}
		
		//Slide link anchor attributes
		$a_attr='';$imglink='';
		$a_attr=get_post_meta($post_id,'glam_link_attr',true);
		if( empty($a_attr) and isset( $glam_slider_curr['a_attr'] ) ) $a_attr=$glam_slider_curr['a_attr'];
		$a_attr_orig=$a_attr;
		if( isset($glam_slider_curr['pphoto'])  and $glam_slider_curr['pphoto'] == '1' ){
			if($glam_slider_curr['pphoto'] == '1') $a_attr.=' rel="prettyPhoto"';
			if(!empty($glam_slide_redirect_url) and isset($glam_slide_redirect_url))
				$imglink=$glam_slide_redirect_url;
			else $imglink='1';
		}
		
		$img_args = array(
			'custom_key' => $custom_key,
			'post_id' => $post_id,
			'attachment' => $attachment,
			'size' => $extract_size,
			'the_post_thumbnail' => $the_post_thumbnail,
			'default_image' => glam_slider_plugin_url( 'images/default_image.png' ),
			'order_of_image' => $order_of_image,
			'link_to_post' => false,
			'image_class' => 'glam_slider_thumbnail',
			'image_scan' => $image_scan,
			'width' => $gti_width,
			'height' => $gti_height,
			'echo' => false,
			'permalink' => $permalink,
			'timthumb'=>$timthumb,
			'style'=> $glam_slider_css['glam_slider_thumbnail'],
			'a_attr'=> $a_attr,
			'imglink'=>$imglink
		);
		
		if( empty($glam_media) or $glam_media=='' or !($glam_media) ) {  
			$glam_large_image=glam_sslider_get_the_image($img_args);
		}
		else{
			$glam_large_image=$glam_media;
		}
		//filter hook
		$glam_large_image=apply_filters('glam_large_image',$glam_large_image,$post_id,$glam_slider_curr,$glam_slider_css);
		$thumbnail_image=get_post_meta($post_id, '_glam_disable_image', true);
		
		if($thumbnail_image!='1')
			$html .= $glam_large_image;
		/*Added for embeding any shortcode on slide - start */
		$glam_eshortcode=get_post_meta($post_id, '_glam_embed_shortcode', true);
		
		if(!empty($glam_eshortcode)){
			$shortcode_html=do_shortcode($glam_eshortcode);
			$html.='<div class="glam_eshortcode" '.$glam_slider_css['glam_slider_thumbnail'].'>'.$shortcode_html.'</div>';
			//die($glam_eshortcode."Test");		
		}	
		/* Added for embeding any shortcode on slide - end */	

		
		$page_close='';
		if( ($i%$glam_slider_curr['visible'] == ($glam_slider_curr['visible']-1) ) ){$page_close='</div><!-- /glam_slide -->';}
		  		
		if ($glam_slider_curr['image_only'] == '1') 
		{ 
			$html .= '<!-- /glam_slideri -->
			</div>'.$page_close;
		}
		else {
		   if($permalink!='') {
			$slide_title = '<h4 '.$glam_slider_css['glam_slider_h4'].'><a href="'.$permalink.'" '.$glam_slider_css['glam_slider_h4_a'].' '.$a_attr_orig.'>'.$post_title.'</a></h4>';
			//filter hook
		   $slide_title=apply_filters('glam_slide_title_html',$slide_title,$post_id,$glam_slider_curr,$glam_slider_css,$post_title);
			$html .= '<div class="glam_slide_content" '.$glam_slider_css['glam_slide_content'].'>'.$slide_title.$slider_excerpt.$fields_html;
			if($glam_slider_curr['show_content']=='1'){
			  $html .= '<p class="more"><a href="'.$permalink.'" '.$glam_slider_css['glam_slider_p_more'].' '.$a_attr_orig.'>'.$glam_slider_curr['more'].'</a></p>';
			}
			 $html .= '</div>	<!-- /glam_slideri -->
			</div>'.$page_close; }
		   else{
		   $slide_title = '<h4 '.$glam_slider_css['glam_slider_h4'].'>'.$post_title.'</h4>';
		   //filter hook
		   $slide_title=apply_filters('glam_slide_title_html',$slide_title,$post_id,$glam_slider_curr,$glam_slider_css,$post_title);
		   $html .= '<div class="glam_slide_content" '.$glam_slider_css['glam_slide_content'].'>'.$slide_title.$slider_excerpt.$fields_html.'
				</div> <!-- /glam_slideri -->
			</div>'.$page_close;    }
		}
	  $i++;
	}
	if( ($page_close=='' or empty($page_close)) and $posts ){$html=$html.'</div><!-- /glam_slide -->';}
	//filter hook
	$html=apply_filters('glam_extract_html',$html,$glam_sldr_j,$posts,$glam_slider_curr);
	if($out_echo == '1') {
	   echo $html;
	}
	$r_array = array( $glam_sldr_j, $html);
	$r_array=apply_filters('glam_r_array',$r_array,$posts, $glam_slider_curr,$set);
	return $r_array;
}
/*** ---------------------------Data Processor Function For Add-on----------------------------- ***/
function glam_data_processor_default($slides, $glam_slider_curr,$out_echo,$set,$data=array()){
	$skin='default'; 
	global $glam_slider;
	$glam_slider_css = glam_get_inline_css($set);
	$html = '';
	$glam_sldr_j = $i = 0;
	$page_close='';
	
	if(is_array($data)) extract($data,EXTR_PREFIX_ALL,'data');
	
	$timthumb='1';
	if($glam_slider_curr['timthumb']=='1'){
		$timthumb='0';
	}
	
	$visible = isset($glam_slider_curr['visible']) ? $glam_slider_curr['visible'] : '1' ;
	$visible = ( $visible > 0 ) ? $visible : '1' ;
	
	$slider_handle='';
	if ( !empty($data_slider_handle) ) {
		$slider_handle=$data_slider_handle;
	}	
	
	foreach($slides as $slide) {
		//print_r ($slides);
		$id = $slide->ID;
		$post_title = stripslashes($slide->post_title);
		$post_title = str_replace('"', '', $post_title);
		//filter hook
		if (isset($id))	$post_title=apply_filters('glam_post_title',$post_title,$id,$glam_slider_curr,$glam_slider_css);
		$slider_content = $slide->post_content;
		$post_id = $slide->ID;
		
		$glam_slide_redirect_url = get_post_meta($post_id, 'glam_slide_redirect_url', true);
		$glam_sslider_nolink = get_post_meta($post_id,'glam_sslider_nolink',true);
		trim($glam_slide_redirect_url);
		if(!empty($glam_slide_redirect_url) and isset($glam_slide_redirect_url)) {
		   $permalink = $glam_slide_redirect_url;
		}
		else{
		   $permalink = $slide->url;
		}
		if($glam_sslider_nolink=='1'){
		  $permalink='';
		}
		
		if($i%$glam_slider_curr['visible'] == 0){
		$html .= '<div class="glam_slide" '.$glam_slider_css['glam_slide'].'>
			<!-- glam_slide -->';
			$glam_sldr_j++;
		}
		
		$html .= '<div class="glam_slideri" '.$glam_slider_css['glam_slideri'].'>
			<!-- glam_slideri -->';
		
		if($glam_slider_curr['show_content']=='1'){
			if ($glam_slider_curr['content_from'] == "slider_content") {
				$slider_content = $slide->post_content;
				//echo $slider_content;
			}
			if ($glam_slider_curr['content_from'] == "excerpt") {
				$slider_content = $slide->post_excerpt;
				//echo $slider_content;
			}

			$slider_content = strip_shortcodes( $slider_content );

			$slider_content = stripslashes($slider_content);
			$slider_content = str_replace(']]>', ']]&gt;', $slider_content);
	
			$slider_content = str_replace("\n","<br />",$slider_content);
			$slider_content = strip_tags($slider_content, $glam_slider_curr['allowable_tags']);
			
			$content_limit=$glam_slider_curr['content_limit'];
			$content_chars=$glam_slider_curr['content_chars'];
			if(empty($content_limit) && !empty($content_chars)){ 
				$slider_excerpt = substr($slider_content,0,$content_chars);
			}
			else{ 
				$slider_excerpt = glam_slider_word_limiter( $slider_content, $limit = $content_limit);
			}
			if(!isset($slider_excerpt))$slider_excerpt='';
			//filter hook
			$slider_excerpt=apply_filters('glam_slide_excerpt',$slider_excerpt,$post_id,$glam_slider_curr,$glam_slider_css);
			$slider_excerpt='<span '.$glam_slider_css['glam_slider_span'].'> '.$slider_excerpt.'</span>';
		}
		else{
		    $slider_excerpt='';
		}
		//filter hook
			$slider_excerpt=apply_filters('glam_slide_excerpt_html',$slider_excerpt,$post_id,$glam_slider_curr,$glam_slider_css);
		
		$glam_fields=$glam_slider_curr['fields'];		
		$fields_html='';
		if(isset($glam_fields) and !empty($glam_fields) ){
			 $fields=explode( ',', $glam_fields );
			 if($fields){
				 foreach($fields as $field) {
					 if (isset ($field))
					if (isset ($field))	$field_val = ( isset($slide->$field) ) ? ( $slide->$field ) : '' ;
					 if( $field_val and !empty($field_val) ) $fields_html .='<div class="glam_'.$field.' glam_fields">'.$field_val.'</div>';
				 }
			 }
		 }

		//Slide link anchor attributes
		$a_attr='';$imglink='';
		if (isset ($slide->glam_link_attr))
		$a_attr=$slide->glam_link_attr;
		if( empty($a_attr) and isset( $glam_slider_curr['a_attr'] ) ) $a_attr=$glam_slider_curr['a_attr'];
		$a_attr_orig=$a_attr;
		if( isset($glam_slider_curr['pphoto'])  and $glam_slider_curr['pphoto'] == '1' ){
			if($glam_slider_curr['pphoto'] == '1') $a_attr.=' rel="prettyPhoto"';
			if(!empty($glam_slide_redirect_url) and isset($glam_slide_redirect_url))
				$imglink=$glam_slide_redirect_url;
			else $imglink='1';
		}
		
		//For media images
		if (isset ($slide->media)) $glam_media = $slide->media;
		if (isset ($slide->media_image)) $glam_media_image = $slide->media_image;
		
		if( ((empty($glam_media) or $glam_media=='' or !($glam_media)) and (empty($glam_media_image) or $glam_media_image=='' or !($glam_media_image)) ) or $data_media!='1' ) {
			$width = ($glam_slider_curr['iwidth']/$visible) - (($glam_slider_curr['padding']/2) * ($visible - 1));
			$height = $glam_slider_curr['height'];
			
			if($glam_slider_curr['crop'] == '0'){
			 $extract_size = 'full';
			}
			elseif($glam_slider_curr['crop'] == '1'){
			 $extract_size = 'large';
			}
			elseif($glam_slider_curr['crop'] == '2'){
			 $extract_size = 'medium';
			}
			else{
			 $extract_size = 'thumbnail';
			}
			
			$classes[] = $extract_size;
			//$classes[] = 'glam_slider_thumbnail';
			//$classes[] = $data_image_class;
			$class = join( ' ', array_unique( $classes ) );
	
			preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', $slide->content_for_image, $matches );
			if(isset($data_default_image))
			$img_url=$data_default_image;
			/* If there is a match for the image, return its URL. */
			$order_of_image='';
			if(isset($data['order'])) $order_of_image=$data['order'];
			
			if($order_of_image > 0) $order_of_image=$order_of_image; 
			else $order_of_image = 0;
			
			if ( isset( $matches ) && count($matches[1])<=$order_of_image) $order_of_image=count($matches[1]);
			
			if ( isset( $matches ) && $matches[1][$order_of_image] )
				$img_url = $matches[1][$order_of_image];
			
			$width = ( ( $width ) ? ' width="' . esc_attr( $width ) . '"' : '' );
			$height = ( ( $height ) ? ' height="' . esc_attr( $height ) . '"' : '' );
			
			$img_html = '<img src="' . $img_url . '" class="' . esc_attr( $class ) . '"' . $width . $height . $glam_slider_css['glam_slider_thumbnail'] .' />';
			
			//Prettyphoto Integration	
			$ipermalink=$permalink;
			if($imglink=='1' and $permalink!='') $ipermalink=$img_url;
			elseif($imglink=='') $ipermalink=$permalink;
			else {
				if($permalink!='')$ipermalink=$imglink;
			}
			
			if($permalink!='') {
			  $img_html = '<a href="' . $ipermalink . '" title="'.$post_title.'" '.$a_attr.'>' . $img_html . '</a>';
			}
				
			$glam_large_image=$img_html;
		}
		else{
			if(!empty($glam_media)){
				$glam_large_image=$glam_media;
			}
			else{
				$width = ($glam_slider_curr['iwidth']/$visible) - (($glam_slider_curr['padding']/2) * ($visible - 1));
				$height = $glam_slider_curr['height'];
				$width = ( ( $width ) ? ' width="' . esc_attr( $width ) . '"' : '' );
				$height = ( ( $height ) ? ' height="' . esc_attr( $height ) . '"' : '' );
				
				if($glam_slider_curr['crop'] == '0'){
				 $extract_size = 'full';
				}
				elseif($glam_slider_curr['crop'] == '1'){
				 $extract_size = 'large';
				}
				elseif($glam_slider_curr['crop'] == '2'){
				 $extract_size = 'medium';
				}
				else{
				 $extract_size = 'thumbnail';
				}
				
				$classes[] = $extract_size;
				$classes[] = 'glam_slider_thumbnail';
				//$classes[] = $data_image_class;
				$class = join( ' ', array_unique( $classes ) );
				if(!empty($glam_media_image)){
					$glam_large_image='<img src="'.$glam_media_image.'" class="' . esc_attr( $class ) . '"' . $width . $height . '/>';
					$img_url=$glam_media_image;
				}	
				else {
					$glam_large_image='<img src="'.$data_default_image.'" class="' . esc_attr( $class ) . '"' . $width . $height . '/>';
					$img_url=$data_default_image;
				}
				
				//Prettyphoto Integration	
				$ipermalink=$permalink;
				if($imglink=='1' and $permalink!='') $ipermalink=$img_url;
				elseif($imglink=='') $ipermalink=$permalink;
				else {
					if($permalink!='')$ipermalink=$imglink;
				}
				
				if($permalink!='') {
				  $glam_large_image = '<a href="' . $ipermalink . '" title="'.$post_title.'" '.$a_attr.'>' . $glam_large_image . '</a>';
				}
			}
		}
		
		//filter hook
		$glam_large_image=apply_filters('glam_large_image',$glam_large_image,$post_id,$glam_slider_curr,$glam_slider_css);
		$html .= $glam_large_image;
		
		$page_close='';
		if( ($i%$glam_slider_curr['visible'] == ($glam_slider_curr['visible']-1) ) ){$page_close='</div><!-- /glam_slide -->';}
		  		
		if ($glam_slider_curr['image_only'] == '1') { 
			$html .= '<!-- /glam_slideri -->
			</div>'.$page_close;
		}
		else {
		   if($permalink!='') {
			$slide_title = '<h4 '.$glam_slider_css['glam_slider_h4'].'><a href="'.$permalink.'" '.$glam_slider_css['glam_slider_h4_a'].' '.$a_attr_orig.'>'.$post_title.'</a></h4>';
			//filter hook
		   $slide_title=apply_filters('glam_slide_title_html',$slide_title,$post_id,$glam_slider_curr,$glam_slider_css,$post_title);
			$html .= '<div class="glam_slide_content" '.$glam_slider_css['glam_slide_content'].'>'.$slide_title.$slider_excerpt.$fields_html;
			if($glam_slider_curr['show_content']=='1'){
			  $html .= '<p class="more"><a href="'.$permalink.'" '.$glam_slider_css['glam_slider_p_more'].' '.$a_attr_orig.'>'.$glam_slider_curr['more'].'</a></p>';
			}
			 $html .= '</div>	<!-- /glam_slideri -->
			</div>'.$page_close; }
		   else{
		   $slide_title = '<h4 '.$glam_slider_css['glam_slider_h4'].'>'.$post_title.'</h4>';
		   //filter hook
		   $slide_title=apply_filters('glam_slide_title_html',$slide_title,$post_id,$glam_slider_curr,$glam_slider_css,$post_title);
		   $html .= '<div class="glam_slide_content" '.$glam_slider_css['glam_slide_content'].'>'.$slide_title.$slider_excerpt.$fields_html.'
				</div> <!-- /glam_slideri -->
			</div>'.$page_close;    }
		}
	  $i++;
	}
	if( ($page_close=='' or empty($page_close)) and $slides ){$html=$html.'</div><!-- /glam_slide -->';}
	//filter hook
	$html=apply_filters('glam_extract_html',$html,$glam_sldr_j,$slides,$glam_slider_curr);
	if($out_echo == '1') {
	   echo $html;
	}
	$r_array = array( $glam_sldr_j, $html);
	$r_array=apply_filters('glam_r_array',$r_array,$slides, $glam_slider_curr,$set);
	return $r_array;
}
/***-------------------------------------------------------------------------------------------------------***/

function glam_slider_get_default($slider_handle,$r_array,$glam_slider_curr,$set,$echo='1',$data=array()){
	$skin='default';
		global $glam_slider,$default_glam_slider_settings;
	$glam_sldr_j = $r_array[0];
	$glam_slider_css = glam_get_inline_css($set);
	$html='';
	$slider_id=0;
	
if(isset($r_array) && $r_array[0] >= 1) : //is slider empty?
	if (isset ($data['slider_id'])) {
		if( is_array($data)) $slider_id=$data['slider_id'];
		else $slider_id='';
	}
	if ( is_array($data) && isset($data['title'])){
		if($data['title']!='' )$sldr_title=$data['title'];
		else {
			if($glam_slider_curr['title_from']=='1' && !empty($slider_id) ) $sldr_title = get_glam_slider_name($slider_id);
			else $sldr_title = $glam_slider_curr['title_text'];
		}
	}
	else{
		if($glam_slider_curr['title_from']=='1' && !empty($slider_id) ) $sldr_title = get_glam_slider_name($slider_id);
		else $sldr_title = $glam_slider_curr['title_text']; 
	}

	//filter hook
	$sldr_title=apply_filters('glam_slider_title',$sldr_title,$slider_handle,$glam_slider_curr,$set);
	
	foreach($default_glam_slider_settings as $key=>$value){
		if(!isset($glam_slider_curr[$key])) $glam_slider_curr[$key]='';
	}
	$visible = isset($glam_slider_curr['visible']) ? $glam_slider_curr['visible'] : '1' ;
	$visible = ( $visible > 0 ) ? $visible : '1' ;
	
	$on_document_ready='jQuery(document).ready(function() {';
	$on_window_load='jQuery(window).on("load", function() {';	
	if ( $glam_slider_curr['image_only']=='1')
		$function_on=$on_window_load;
	else
		$function_on=$on_document_ready;
	
	$glam_media_queries='';$responsivejs='';
    	$glam_media_queries='.glam_slider_set'.$set.'.glam_slider{width: 100% !important;}.glam_slider_set'.$set.' .glam_slideri{height:auto !important;}@media only screen and (max-width: 599px) {.glam_slider_set'.$set.' .glam_slide_content span{display:none;}}';
	//filter hook
	$glam_media_queries=apply_filters('glam_media_queries',$glam_media_queries,$glam_slider_curr,$set);
		
	$responsivejs='glam_responsiveScrollable("'. $slider_handle.'_wrap",'.$glam_slider_curr['width'].','.$glam_slider_curr['height'].','.$glam_slider_curr['iwidth'].','.$glam_slider_curr['swidth'].','.$glam_slider_curr['padding'].','.$visible.');

	jQuery(window).resize(function() {
		glam_waitForFinalEvent( glam_responsiveScrollable, 900, "'.$slider_handle.'","'. $slider_handle.'_wrap",'.$glam_slider_curr['width'].','.$glam_slider_curr['height'].','.$glam_slider_curr['iwidth'].','.$glam_slider_curr['swidth'].','.$glam_slider_curr['padding'].','.$visible.');
	});';
		
		//JS
		wp_enqueue_script( 'glam', glam_slider_plugin_url( 'js/glam.js' ),
		array('jquery'), GLAM_SLIDER_VER, false);		
		wp_enqueue_script( 'easing', glam_slider_plugin_url( 'js/jquery.easing.js' ),
			array('jquery'), GLAM_SLIDER_VER, false); 
		wp_enqueue_script( 'jquery.touchwipe', glam_slider_plugin_url( 'js/jquery.touchwipe.js' ),
			array('jquery'), GLAM_SLIDER_VER, false);
	
		if(!isset($glam_slider_curr['fouc']) or $glam_slider_curr['fouc']=='' or $glam_slider_curr['fouc']=='0' ){
			$fouc_dom='jQuery("html").addClass("glam_slider_fouc");jQuery(".glam_slider_fouc .glam_slider_set'.$set.'").hide();';
			$fouc_ready='jQuery(document).ready(function() {
			   jQuery(".glam_slider_fouc .glam_slider_set'.$set.'").show();
			});';
		}	
		else{
			$fouc_dom='';$fouc_ready='';
		}		
	$slider_handle_string=str_replace('-','__',$slider_handle);
	$easing=isset($glam_slider_curr['easing']) ? (',easing:"'.$glam_slider_curr['easing']).'"' : '';
	$speed=isset($glam_slider_curr['speed']) ? (',speed:'. ( $glam_slider_curr['speed'] * 100 )) : '';
	$arrows=( !isset($glam_slider_curr['prev_next']) or  $glam_slider_curr['prev_next'] == '' or $glam_slider_curr['prev_next'] == '0') ? 
			( 'next:"#'.$slider_handle.'_wrap .svglam_next",
			prev:"#'.$slider_handle.'_wrap .svglam_prev",' )
			: '' ;
	$autoscroll=( isset($glam_slider_curr['autostep']) and $glam_slider_curr['autostep'] == '1' ) ? 
				('.autoscroll({
					autoplay:true,
					interval: '. ( isset($glam_slider_curr['time']) ? ( $glam_slider_curr['time'] * 1000 ) : ( 6000 ) ) .'
				})' ) 
				: '';
	$navigator=( isset($glam_slider_curr['navnum']) and ( $glam_slider_curr['navnum'] == '1' or $glam_slider_curr['navnum'] == '2' ) ) ? 
				('.navigator({
					navi:"#'.$slider_handle.'_nav", 
					activeClass: "glam_nav_a",
					style:"'. $glam_slider_css['glam_nav_a'] .'"
				})' ) 
				: '';
	$s_opacity=(isset($glam_slider_curr['s_opacity']))?$glam_slider_curr['s_opacity']:'0.5';
	$content_tran=(isset($glam_slider_curr['content_tran']))?$glam_slider_curr['content_tran']:'0';
	$slider_script='var '.$slider_handle_string.'_a;
	var '.$slider_handle_string.'_y=0;
	jQuery(document).ready(function() {
				jQuery("#'.$slider_handle.'").scrollable({
					circular:true,
					clonedClass:"glam_cloned",
					disabledClass:"glam_disabled",
					items:".glam_items",
					'.$arrows.'
					onSeek:function(G,F){
						if(!'.$slider_handle_string.'_a){'.$slider_handle_string.'_a=jQuery(this.getItems()[0]);}
						if('.$slider_handle_string.'_y<F){svilla_u('.$slider_handle_string.'_a,G,F,"toRight",'.$s_opacity.')}
						else{svilla_u('.$slider_handle_string.'_a,G,F,"toLeft",'.$s_opacity.')}
						'.$slider_handle_string.'_a=jQuery(this.getItems()[F]);
						svilla_t("'.$content_tran.'",'.$slider_handle_string.'_a,F);
						'.$slider_handle_string.'_y=F
					}
					'.$easing.$speed.'
				})
				'.$autoscroll.' 
				'.$navigator.';
				svilla_glam_onload("'.$content_tran.'","#'.$slider_handle.'");
				'.$responsivejs.'
		});';	
	$html=$html.'<script type="text/javascript"> '.$fouc_ready;
	/*( (!isset($glam_slider_curr['fouc']) or $glam_slider_curr['fouc']=='' or $glam_slider_curr['fouc']=='0' ) ? 
	'jQuery("html").addClass("glam_slider_fouc"); '.$function_on.'
	   jQuery(".glam_slider_fouc .glam_slider").css({"display" : "block"});
	});' : '' );*/
	
	if(!empty($glam_media_queries)){
			$slider_script.='jQuery(document).ready(function() {jQuery("head").append("<style type=\"text/css\">'. $glam_media_queries .'</style>");});';
	}
	
	if($glam_slider_curr['pphoto'] == '1') {
		wp_enqueue_script( 'jquery.prettyPhoto', glam_slider_plugin_url( 'js/jquery.prettyPhoto.js' ),
							array('jquery'), GLAM_SLIDER_VER, false);
		wp_enqueue_style( 'prettyPhoto_css', glam_slider_plugin_url( 'css/prettyPhoto.css' ),
				false, GLAM_SLIDER_VER, 'all');
		$lightbox_script='jQuery(document).ready(function(){
			jQuery("a[rel^=\'prettyPhoto\']").prettyPhoto({deeplinking: false,social_tools:false});
		});';
		//filter hook
		   $lightbox_script=apply_filters('glam_lightbox_inline',$lightbox_script);
		$html.=$lightbox_script;
	}	

	//action hook
	do_action('glam_global_script',$slider_handle,$glam_slider_curr);
	$html.='</script> <noscript><p><strong>'. $glam_slider['noscript'] .'</strong></p></noscript>';

	$html.='<div id="'. $slider_handle.'_wrap" class="glam_slider glam_slider_set'. $set .'" '.$glam_slider_css['glam_slider'].'>'.
		( (!empty($sldr_title)) ? '<div class="sldr_title" '.$glam_slider_css['sldr_title'].'>'. $sldr_title .'</div>' : '' ).  
		( ($glam_slider_curr['navnum'] == "2") ? '<div id="'.$slider_handle.'_nav" class="glam_nav" '. $glam_slider_css['glam_nav'] .' ></div>' : '' ) . 
		
		( ($glam_slider_curr['prev_next'] != 1) ? '<div class="glam_slider_nav_wrap"><a class="svglam_prev glam_browse_link">
		<div class="glam_prev" '. $glam_slider_css['glam_prev'] .'>
		<span class="glam_arrow"></span>
		</div>
		<div id="'. $slider_handle.'_prev" class="glam_left_link glam_side_link" '. $glam_slider_css['glam_side_link'] .'>
		</div>
		</a>': '' ).
		
		'<div class="glam_slides_wrap" '. $glam_slider_css['glam_slides_wrap'] .'><div id="'. $slider_handle.'" class="glam_slider_instance" '. $glam_slider_css['glam_slider_instance'] .'>
			<div class="glam_items" '.$glam_slider_css['glam_items'].'>
				'.$r_array[1].'
			</div>
		</div></div> '. 
		
		( ($glam_slider_curr['navnum'] == "1") ? '<div id="'.$slider_handle.'_nav" class="glam_nav" '. $glam_slider_css['glam_nav'] .' ></div>' : '' ) . 
		
		( ($glam_slider_curr['prev_next'] != 1) ? '<a class="svglam_next glam_browse_link">
		<div class="glam_next" '. $glam_slider_css['glam_next'].'>
		<span class="glam_arrow"></span>
		</div>
		<div id="'. $slider_handle.'_next" class="glam_right_link glam_side_link" '. $glam_slider_css['glam_side_link'] .'>
		</div>
		</a></div>': '' ). 
		
	'</div>';
	
	$html.='<script type="text/javascript">'.$fouc_dom.$slider_script.'</script>';
	$html=apply_filters('glam_slider_html',$html,$r_array,$glam_slider_curr,$set);
	if($echo == '1')  {echo $html; }
	else { return $html; }
endif; //is slider empty?
}
?> 
