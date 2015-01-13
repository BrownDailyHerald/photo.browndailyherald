<?php get_header(); ?>
 
<div id="main" class="full-width">
  	
	<h1 class="title"><a href="<?php echo get_permalink( $post->post_parent ); ?>" ><?php echo get_the_title( $post->post_parent ); ?></a></h1>
 
	<div id="content">

		<div class="post">
	 
			<?php while (have_posts()) : the_post(); ?>

			<div class="entry">
				<?php if ( wp_attachment_is_image() ) : ?>
		
					<p class="attachment" style="padding-top:20px; text-align:center; "><a href="<?php echo wp_get_attachment_url(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php
						echo wp_get_attachment_image( $post->ID, $size='fullsize' ); // max $content_width wide or high.
					?></a></p>
					
 					<?php else : ?>
					<a href="<?php echo wp_get_attachment_url(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php echo basename( get_permalink() ); ?></a>
				<?php endif; ?>
				<div class="clear"></div>
			</div><!-- /.entry -->
			<div class="clear"></div>
			
			<div class="navigation clearfix">
				<div class="floatleft"><?php previous_image_link( false, __('&larr; Previous picture', 'wpzoom')); ?></div>
				<div class="floatright"><?php next_image_link( false, __('Next picture &rarr;', 'wpzoom')); ?></div>
			</div> 
			
		</div><!-- / .post -->

		<div class="thumbnails">
			<?php
			echo show_all_thumbs();
			?>
		</div>
		 
		
		<?php endwhile; ?>
		<div class="clear"></div>
	</div><!-- /#content -->
	<div class="clear"></div>
</div><!-- /#main -->
<div class="clear"></div>
<?php get_footer(); ?> 