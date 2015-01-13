<?php /* We override the parent header.php to put in an ad */ ?>
<?php 
function add_print_stylesheet() {
	echo '<link rel="stylesheet" type="text/css" href="'.get_stylesheet_uri().'" media="print"/>';
}
$id = $post->ID;
function add_author_metatag() {
	global $post;
	if (is_single()) {
		echo '<meta name="author" content="'.coauthors(',',',',null,null,false).'" />';
	}
}
add_action('wp_head', 'add_print_stylesheet'); 
add_action('wp_head', 'add_author_metatag');
?>
<?php require get_template_directory() . '/header.php'; ?>
<?php get_template_part( 'ad', 'header-leaderboard' ); ?>
