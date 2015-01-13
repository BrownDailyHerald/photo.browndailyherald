<?php
 
// Styling for the custom post type icon
add_action( 'admin_head', 'wp_video_icon' );
 
function wp_video_icon() {
    ?>
    <style type="text/css" media="screen">
 		#icon-edit.icon32-posts-video {background: url(<?php echo get_template_directory_uri(); ?>/images/video-32.png) no-repeat;}
    </style>
<?php }


/*-----------------------------------------------------------------------------------*/
/*	Register Video custom post types
/*-----------------------------------------------------------------------------------*/

function wpzoom_create_post_type_videos() 
{
	$labels = array(
		'name' => _x( 'Videos', 'post type general name', 'wpzoom'),
		'singular_name' => _x( 'Video', 'post type general name', 'wpzoom'),
		'rewrite' => array('slug' => 'video' ),
		'add_new' => _x('Add New', 'video', 'wpzoom'),
		'add_new_item' => __('Add New Video', 'wpzoom'),
		'edit_item' => __('Edit Videos', 'wpzoom'),
		'new_item' => __('New Videos', 'wpzoom'),
		'view_item' => __('View Video', 'wpzoom'),
		'search_items' => __('Search Videos', 'wpzoom'),
		'not_found' =>  __('No videos found', 'wpzoom'),
		'not_found_in_trash' => __('No videos found in Trash', 'wpzoom'), 
		'parent_item_colon' => ''
	  );
	  
	  $args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'rewrite' => array(	"slug" => "video"	), 
		'menu_position' => null,
		'show_in_nav_menus'	=> true ,
		'has_archive' => true,
		'menu_icon' => get_template_directory_uri() .'/images/video2.png', // 16px16
		'supports' => array('title', 'editor', 'excerpt', 'comments', 'thumbnail'),
		'taxonomies' => array( 'videos')
 	  ); 
	  
	  register_post_type('video' ,$args);
}

add_action( 'init', 'wpzoom_create_post_type_videos' ); 




/*
/*	Create custom taxonomies for the video post type
==============================================================*/

function wpzoom_build_taxonomies(){
	register_taxonomy("videos", 
		array("video"), 
		array(  "hierarchical"		=> true, 
				"label" 			=> __( "Video Categories", 'wpzoom' ), 
				"singular_label" 	=> __( "Video Category", 'wpzoom' ), 
				'public' 			=> true,
				'show_ui' 			=> true,
				"rewrite" 			=> array(
										'slug' => 'videos', 
										'hierarchical' => true
										))); 
}
  
 add_action( 'init', 'wpzoom_build_taxonomies', 0 );
