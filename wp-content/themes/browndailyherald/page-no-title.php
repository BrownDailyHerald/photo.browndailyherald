<?php
/*
Template Name: Page with no title
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
  
  		<?php while (have_posts()) : the_post(); ?>

			<div class="post clearfix">

				<div class="entry">
					<?php the_content(); ?>
					<div class="clear"></div>
					<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'wpzoom' ) . '</span>', 'after' => '</div>' ) ); ?>
					<div class="clear"></div>
					<?php edit_post_link( __('Edit', 'wpzoom'), '', ''); ?>
				</div><!-- / .entry -->
				<div class="clear"></div>
		 
			</div><!-- /.post -->

	 		<?php if (option::get('comments_page') == 'on') { 
				comments_template();
				} ?>
		
		<?php endwhile; ?>

	</div><!-- /#content -->
	
	<?php get_sidebar();  ?>

</div><!-- /#main -->
<?php get_footer(); ?>