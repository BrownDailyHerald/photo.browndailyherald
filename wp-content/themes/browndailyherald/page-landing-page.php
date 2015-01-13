<?php
/*
Template Name: Landing Page Template
*/
?>
<?php get_header();

// We don't want to display orgseries stuff on pages, just articles.
global $orgseries;
if (isset($orgseries))
{
	remove_filter('the_content', array(&$orgseries, 'add_series_meta'), 12);
}

?>

<div id="main">

	<div id="content">

 		<h1 class="archive_title">
			<?php the_title(); ?>
		</h1>

  
  		<?php while (have_posts()) : the_post(); ?>

			<div class="post clearfix">

				<div class="entry">
					<?php the_content(); ?>
					<div class="clear"></div>
				</div><!-- / .entry -->
				<div class="clear"></div>
		 
			</div><!-- /.post -->

		<?php endwhile; ?>

		<?php $wp_query = new WP_Query($cftl_previous['query']); ?>
		<?php get_template_part('loop'); ?>

	</div><!-- /#content -->
	
	<?php get_sidebar();  ?>

</div><!-- /#main -->
<?php get_footer(); ?>
