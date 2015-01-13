<?php get_header();

// If we're looking at a series page, we don't need to list that an article is part of a series.
if (function_exists('is_series') && is_series())
{
	global $orgseries;
	remove_filter('the_excerpt', array(&$orgseries, 'add_series_meta_excerpt'));
}

?>

<div id="main" role="main">

	<div id="content">
		<h1 class="archive_title"> 
			<?php /* category archive */ if (is_category()) { ?> <?php single_cat_title(); ?>
			<?php /* tag archive */ } elseif( is_tag() ) { ?><?php _e('Topic:', 'wpzoom'); ?> <?php single_tag_title(); ?>
			<?php /* series archive */ } elseif ( function_exists('is_series') && is_series() ) { ?><?php single_series_title(); ?>
			<?php /* daily archive */ } elseif (is_day()) { ?><?php _e('Archive for', 'wpzoom'); ?> <?php the_time('F jS, Y'); ?>
			<?php /* monthly archive */ } elseif (is_month()) { ?><?php _e('Archive for', 'wpzoom'); ?> <?php the_time('F, Y'); ?>
			<?php /* yearly archive */ } elseif (is_year()) { ?><?php _e('Archive for', 'wpzoom'); ?> <?php the_time('Y'); ?>
			<?php /* paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?><?php _e('Archives', 'wpzoom'); } ?>
		</h1>

		<?php get_template_part('loop'); ?>

	</div> <!-- /#content -->
	
	<?php get_sidebar(); ?> 

</div> <!-- /#main -->
<?php get_footer(); ?> 