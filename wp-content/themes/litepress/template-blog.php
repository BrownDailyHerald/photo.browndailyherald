<?php
/*
Template Name: Blog
*/
?>

<?php get_header(); ?>

<div id="main" role="main">

	<div id="content">
 	
		<h1 class="archive_title">
			<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'wpzoom' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h1>
	 
		<?php // WP 3.0 PAGED BUG FIX
			if ( get_query_var('paged') )
				$paged = get_query_var('paged');
			elseif ( get_query_var('page') ) 
				$paged = get_query_var('page');
			else 
				$paged = 1;
			//$paged = (get_query_var('paged')) ? get_query_var('paged') : 1; 
			 
			query_posts("paged=$paged"); if (have_posts()) : 
			?>
			
				<?php get_template_part('loop'); ?>

			<?php endif; ?>
  
	</div> <!-- /#content -->
	<?php get_sidebar();  ?>
	
</div> <!-- /#main -->
<?php get_footer(); ?>  