<?php

function search_display_name($query) {
	global $wpdb;
	$query->query_where .= $wpdb->prepare( " OR $wpdb->users.display_name LIKE %s", '%' . like_escape(get_search_query(false)) . '%' );
}

// Only search if we have a decent amount of stuff to search for
if (strlen(str_replace('*', '', get_search_query(false))) >= 3) {
	// Search for authors
	$args  = array(
		'search'			=> sprintf('*%s*', get_search_query(false)),
		'orderby' 			=> 'display_name',
		'fields'			=> array('ID', 'display_name', 'user_login')
	);

	// Create the WP_User_Query object
	add_action('pre_user_query', 'search_display_name');
	$wp_user_query = new WP_User_Query($args);
	remove_action('pre_user_query', 'search_display_name');

	// Get the results
	$authors = $wp_user_query->get_results();
} else {
	$authors = array();
}

?>
<?php get_header(); ?>

<div id="main">
	<div id="content">
		<?php if (have_posts() || !empty($authors)): ?>

		<h1 class="archive_title"><?php _e('Search results for','wpzoom');?> <strong>"<?php the_search_query(); ?>"</strong></h1>

		<?php if (!empty($authors)): ?>
		<?php foreach ($authors as $cur_author): ?>
		<?php $staff_title = get_metadata('user', $cur_author->ID, 'bdh_staff_title', true); ?>
		<div class="recent-post">
			<div class="post-content">
				<h2><a href="<?php echo get_author_posts_url($cur_author->ID); ?>"><?php echo $cur_author->display_name; ?></a></h2>
<?php if (!empty($staff_title)): ?>
				<div class="meta-author"><?php echo esc_html($staff_title); ?></div>
<?php endif; ?>
			</div>
			<div class="clear"></div>
		</div>
		<?php endforeach; ?>
		<?php endif; ?>

		<?php get_template_part('loop'); ?>

		<?php else: ?>
			<h1 class="archive_title">Nothing Found</h1>

			<div class="post-content">
				<div class="entry">
					<p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'wpzoom' ); ?></p>
				</div><!-- /.entry -->
			</div>
		<?php endif; ?>

	</div><!-- /#content -->

	<?php get_sidebar(); ?>

</div><!-- /#main -->
<?php get_footer(); ?> 