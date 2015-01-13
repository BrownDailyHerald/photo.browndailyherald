<?php
/*
Template Name: Archives Page
*/
?>
<?php get_header(); ?>
 
<div id="main">
	
	<div id="content">
 

		<h1 class="archive_title">
			<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'wpzoom' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h1>
	  
	 
		<?php edit_post_link( __('Edit', 'wpzoom'), '', ''); ?>
			
		<?php while (have_posts()) : the_post(); ?>
			
			<div class="post clearfix">
		 
				<div class="entry">
					<div class="col_arch">
						<div class="left">
					<?php _e('By Months:', 'wpzoom'); ?>	 
					</div>
					<div class="right"> 
						<ul>											  
							<?php wp_get_archives('type=monthly&show_post_count=1') ?>	
						</ul>	
					</div>
					</div>
				
					<div class="col_arch">
						<div class="left">
					<?php _e('By Categories:', 'wpzoom'); ?>	 
					</div>
					<div class="right"> 
						<ul>											  
							<?php wp_list_categories('title_li=&hierarchical=0&show_count=1') ?>	
						</ul>	
					</div>
					</div>
				
					<div class="col_arch">
						<div class="left">
					<?php _e('By Tags:', 'wpzoom'); ?>	 
					</div>
					<div class="right"> 
						<?php wp_tag_cloud('format=list&smallest=14&largest=14&unit=px'); ?>
					</div>
					</div>
				</div><!-- / .entry --> 
 				<div class="clear"></div>   
			    
			</div><!-- / .post -->
		
		<?php endwhile; ?>  

	</div><!-- /#content -->
	
	<?php get_sidebar();  ?>

</div><!-- /#main -->
 <?php get_footer(); ?>