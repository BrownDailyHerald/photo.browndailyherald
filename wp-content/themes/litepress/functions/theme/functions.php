<?php

/* Register Thumbnails Size 
================================== */

if ( function_exists( 'add_image_size' ) ) {

	/* Slider */
	add_image_size( 'slider', 305, option::get('featured_thumb_height'), true );
	add_image_size( 'slider-small', 75, 55, true );

	/* Individual Post Thumbnail */
	if (option::get('post_thumb_limit') == 'on') { $image_height = option::get('post_thumb_height'); } else { $image_height = "auto"; }
	add_image_size( 'singlepost', 650, $image_height, true );

	/* Featured Category Widget */
	add_image_size( 'featured-cat', 300, 236, true );
	add_image_size( 'featured-cat-small', 65, 50, true );

	/* Footer Carousel */
	add_image_size( 'carousel', 180, 120, true );

	/* Video Slider */
	add_image_size( 'video-big', 580, 320, true );
	add_image_size( 'video-small', 145, 95, true );

	/* Recent Posts Widget */
	add_image_size( 'recent-widget', 75, 50, true );

}

/* Default Thubmnail */
update_option('thumbnail_size_w', option::get('thumb_width'));
update_option('thumbnail_size_h', option::get('thumb_height'));
update_option('thumbnail_crop', 1);


/* 	Register Custom Menu 
==================================== */

register_nav_menu('secondary', 'Top Menu');
register_nav_menu('primary', 'Main Menu');



/* Add support for Custom Background 
==================================== */

if ( ui::is_wp_version( '3.4' ) )
	add_theme_support( 'custom-background' ); 
else
	add_custom_background( $args );


 
/* Custom Excerpt Length
==================================== */

function new_excerpt_length($length) {
	return (int) option::get("excerpt_length") ? (int) option::get("excerpt_length") : 50;
}
add_filter('excerpt_length', 'new_excerpt_length');


/* Reset [gallery] shortcode styles						
==================================== */

add_filter('gallery_style', create_function('$a', 'return "<div class=\'gallery\'>";'));


/* Email validation
==================================== */

function simple_email_check($email) {
    // First, we check that there's one @ symbol, and that the lengths are right
    if (!preg_match("/^[^@]{1,64}@[^@]{1,255}$/", $email)) {
        // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
        return false;
    }

    return true;
}


/* Maximum width for images in posts 
=========================================== */
if ( ! isset( $content_width ) ) $content_width = 650;



/* Show all thumbnails in attachment.php
=========================================== */

function show_all_thumbs() {
	global $post;
	
	$post = get_post($post);
	$images =& get_children( 'post_type=attachment&post_mime_type=image&output=ARRAY_N&orderby=menu_order&order=ASC&post_parent='.$post->post_parent);
	if($images){
		foreach( $images as $imageID => $imagePost ){
			if($imageID==$post->ID){
			
			unset($the_b_img);
			$the_b_img = wp_get_attachment_image($imageID, 'thumbnail', false);
			$thumblist .= '<a class="active" href="'.get_attachment_link($imageID).'">'.$the_b_img.'</a>';
			
			
			} else {
			unset($the_b_img);
			$the_b_img = wp_get_attachment_image($imageID, 'thumbnail', false);
			$thumblist .= '<a href="'.get_attachment_link($imageID).'">'.$the_b_img.'</a>';
			}
		}
	}
	return $thumblist;
}
 

 

/* Fix widgets no-title bug  
==================================== */

add_filter ('widget_title', 'wpzoom_fix_widgets');

function wpzoom_fix_widgets($content) { 
	
	$title = $content;
	
	if (!$title)
	{
		$content = '<div class="empty"></div>';
	}
    
    return $content; 
}



/*  Limit Posts						
/*									
/*  Plugin URI: http://labitacora.net/comunBlog/limit-post.phps
/*	Usage: the_content_limit($max_charaters, $more_link)
===================================================== */

if ( !function_exists( 'the_content_limit' ) ) { 
 
 	function the_content_limit($max_char, $more_link_text = '(more...)', $stripteaser = 0, $more_file = '') {
		$content = get_the_content($more_link_text, $stripteaser, $more_file);
		// remove [caption] shortcode
		$content = preg_replace("/\[caption.*\[\/caption\]/", '', $content);
		$content = preg_replace("/\[wzslider.*\]/", '', $content);
		// short codes are applied
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]&gt;', $content);
		$content = strip_tags($content);

		if ((strlen($content)>$max_char) && ($espacio = strpos($content, " ", $max_char ))) {
		    $content = substr($content, 0, $espacio);
		    $content = $content;
		    echo "<p>";
		    echo $content;
		    echo "...";
		    echo "</p>";
		}
		else {
		  echo "<p>";
		  echo $content;
		  echo "</p>";
		}
	}
 
} 


/* Related Posts				
==================================== */

if ( !function_exists( 'wp_get_zoomrelated_posts' ) ) {
	function wp_get_zoomrelated_posts() {
		global $wpdb, $post,$table_prefix;
		$wp_rp = get_option("wp_rp");
		
		$exclude = explode(",",$wp_rp["wp_rp_exclude"]);
		$limit = $wp_rp["wp_rp_limit"];
		$wp_rp_title = $wp_rp["wp_rp_title"];
		$wp_no_rp = $wp_rp["wp_no_rp"];
	  	
		if ( $exclude != '' ) {
			$q = "SELECT tt.term_id FROM ". $table_prefix ."term_taxonomy tt, " . $table_prefix . "term_relationships tr WHERE tt.taxonomy = 'category' AND tt.term_taxonomy_id = tr.term_taxonomy_id AND tr.object_id = $post->ID";

			$cats = $wpdb->get_results($q);
			
			foreach(($cats) as $cat) {
				if (in_array($cat->term_id, $exclude) != false){
					return;
				}
			}
		}
			
		if(!$post->ID){return;}
		$now = current_time('mysql', 1);
		$tags = wp_get_post_tags($post->ID);
	  
		$taglist = "'" . $tags[0]->term_id. "'";
		
		$tagcount = count($tags);
		if ($tagcount > 1) {
			for ($i = 1; $i <= $tagcount; $i++) {
				$taglist = $taglist . ", '" . $tags[$i]->term_id . "'";
			}
		}
			
		if ($limit) {
			$limitclause = "LIMIT $limit";
		}	else {
			$limitclause = "LIMIT 4";
		}
		
		$q = "SELECT p.ID, p.post_title, p.post_date, p.comment_count, count(t_r.object_id) as cnt FROM $wpdb->term_taxonomy t_t, $wpdb->term_relationships t_r, $wpdb->posts p WHERE t_t.taxonomy ='post_tag' AND t_t.term_taxonomy_id = t_r.term_taxonomy_id AND t_r.object_id  = p.ID AND (t_t.term_id IN ($taglist)) AND p.ID != $post->ID AND p.post_status = 'publish' AND p.post_date_gmt < '$now' GROUP BY t_r.object_id ORDER BY cnt DESC, p.post_date_gmt DESC $limitclause;";

	 	$related_posts = $wpdb->get_results($q);
		$output = "";
		
		if ($related_posts) {
			
			$output  .= '<div class="related_posts">';
			$output  .= '<h3 class="title">'.__('Related Posts', 'wpzoom').'</h3>';
			$output  .= '<ul>';
			 
		}		
			
		foreach ($related_posts as $related_post ){
			$output .= '<li>';
			
	 		$output .=  '<a href="'.get_permalink($related_post->ID).'" title="'.wptexturize($related_post->post_title).'">'.wptexturize($related_post->post_title).'</a>';  
	  		
			$output .=  " </li>";
		}
		
		if ($related_posts) {
			
	 		$output  .= '</ul><div class="clear"></div></div>';
			 
		}	
	 
		return $output;
	}
}


if ( !function_exists( 'wp_zoomrelated_posts' ) ) {
 	function wp_zoomrelated_posts(){
			
		$output = wp_get_zoomrelated_posts() ;

		echo $output;
	}
}
 

/* Comments Custom Template						
==================================== */

function wpzoom_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 60 ); ?>
			<?php printf( __( '%s <span class="says">says:</span>', 'wpzoom' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
			
			<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
				<?php printf( __('%s at %s', 'wpzoom'), get_comment_date(), get_comment_time()); ?></a><?php edit_comment_link( __( '(Edit)', 'wpzoom' ), ' ' );
				?>
				
			</div><!-- .comment-meta .commentmetadata -->
		
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'wpzoom' ); ?></em>
			<br />
		<?php endif; ?>

		 

		<div class="comment-body"><?php comment_text(); ?></div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'wpzoom' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'wpzoom' ), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}

/* Video auto-thumbnail
==================================== */

if (is_admin()) {
	WPZOOM_Video_Thumb::init();
}

  
/* Tabbed Widget
============================ */

function tabber_tabs_load_widget() {
	// Register widget.
	register_widget( 'Slipfire_Widget_Tabber' );
}

 
/**
 * Temporarily hide the "tabber" class so it does not "flash"
 * on the page as plain HTML. After tabber runs, the class is changed
 * to "tabberlive" and it will appear.
 */
function tabber_tabs_temp_hide(){
	echo '<script type="text/javascript">document.write(\'<style type="text/css">.tabber{display:none;}</style>\');</script>';
}


// Function to check if there are widgets in the Tabber Tabs widget area
// Thanks to Themeshaper: http://themeshaper.com/collapsing-wordpress-widget-ready-areas-sidebars/
function is_tabber_tabs_area_active( $index ){
  global $wp_registered_sidebars;

  $widgetcolums = wp_get_sidebars_widgets();
		 
  if ($widgetcolums[$index]) return true;
  
	return false;
}

 
 // Let's build a widget
class Slipfire_Widget_Tabber extends WP_Widget {

	function Slipfire_Widget_Tabber() {
		$widget_ops = array( 'classname' => 'tabbertabs', 'description' => __('Drag me to the Sidebar', 'wpzoom') );
		$control_ops = array( 'width' => 230, 'height' => 300, 'id_base' => 'slipfire-tabber' );
		$this->WP_Widget( 'slipfire-tabber', __('WPZOOM: Tabs', 'wpzoom'), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		
		$style = $instance['style']; // get the widget style from settings
		
		echo "\n\t\t\t" . $before_widget;
		
		// Show the Tabs
		echo '<div class="tabber">'; // set the class with style
			if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('tabber_tabs') )
		echo '</div>';		
		
		echo "\n\t\t\t" . $after_widget;
		echo '</div>';
 	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['style'] = $new_instance['style'];
		
		return $instance;
	}

	function form( $instance ) {

		//Defaults
		$defaults = array( 'title' => __('Tabber', 'wpzoom'), 'style' => 'style1' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<div style="float:left;width:98%;"></div>
		<p>
		<?php _e('Place your widgets in the <strong>WPZOOM: Tabs Widget Area</strong> and have them show up here.', 'wpzoom')?>
		</p>
		 
		<div style="clear:both;">&nbsp;</div>
	<?php
	}
} 
 
/* Tabber Tabs Widget */
tabber_tabs_plugin_init();

/* Initializes the plugin and it's features. */
function tabber_tabs_plugin_init() {

	// Loads and registers the new widget.
	add_action( 'widgets_init', 'tabber_tabs_load_widget' );
	
	//Registers the new widget area.
	register_sidebar(
		array(
			'name' => __('WPZOOM: Tabs Widget Area', 'wpzoom'),
			'id' => 'tabber_tabs',
			'description' => __('Build your tabbed area by placing widgets here.  !! DO NOT PLACE THE WPZOOM: TABS IN THIS AREA.', 'wpzoom'),
			'before_widget' => '<div id="%1$s" class="tabbertab %2$s">',
			'after_widget' => '</div>'
 		)
	);

	// Hide Tabber until page load 
	add_action( 'wp_head', 'tabber_tabs_temp_hide' );

}


/* Show/hide tag/category list in Theme Options 
================================================ */

function dpbc() {
?>
    <script type="text/javascript">
    (function($) {
        $("#s_carousel_category").parent().hide();
        $("#s_carousel_tag").parent().hide();
        
        $("#s_carousel_" + $("#carousel_type").val().toLowerCase() ).parent().show();

         $("#carousel_type").selectBox().change(function() {
            $("#s_carousel_category").parent().hide();
            $("#s_carousel_tag").parent().hide();

            $("#s_carousel_" + $(this).val().toLowerCase() ).parent().show();
        });
    })(jQuery);
    </script>
<?php
}

add_action('admin_footer', 'dpbc');
?>