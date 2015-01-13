<?php get_header(); ?>
<?php $template = get_post_meta($post->ID, 'wpzoom_post_template', true); ?>

<div id="main"<?php 
	  if ($template == 'side-left') {echo " class=\"side-left\"";}
	  if ($template == 'full') {echo " class=\"full-width\"";} 
	  ?>>
	
	<div id="content">

		<?php if (option::get('post_thumb') == 'on') {

			get_the_image( array( 'size' => 'singlepost', 'width' => 650, 'before' => '<div class="post-cover">', 'after' => '<p>'.get_post(get_post_thumbnail_id())->post_excerpt.'</p></div>' ) );
  
		} ?>
 		
		<div class="post-meta">
			<?php if (option::get('post_date') == 'on') { ?><?php printf( __('%s at %s', 'wpzoom'),  get_the_date(), get_the_time()); ?><?php } ?>

			<?php edit_post_link( __('Edit', 'wpzoom'), '', ''); ?>

			<?php if (option::get('post_share') == 'on') { ?>
				<div class="share_box">
					<div class="share_btn"><a href="http://twitter.com/share" data-url="<?php the_permalink() ?>" class="twitter-share-button" data-count="horizontal">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>
					<div class="share_btn"><iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;layout=button_count&amp;show_faces=false&amp;width=1000&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:80px; height:21px;" allowTransparency="true"></iframe></div>
					<div class="share_btn"><g:plusone size="medium"></g:plusone></div>
				</div>
			<?php } ?>
				 
		</div><!-- /.post-meta -->	
		
		
		<h1 class="title">
			<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'wpzoom' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h1>
		
		<?php while (have_posts()) : the_post(); ?>

  			<?php if (option::get('post_author') == 'on') { ?><div class="meta-author"><?php _e('Posted by', 'wpzoom'); ?> <?php the_author_posts_link(); ?></div><?php } ?>
  				
			<div id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
				 
				<div class="entry">
					<?php the_content(); ?>
					<div class="clear"></div>
					
					<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'wpzoom' ) . '</span>', 'after' => '</div>' ) ); ?>
					<div class="clear"></div>
				
				</div><!-- / .entry -->
				<div class="clear"></div>
			 
			</div><!-- #post-<?php the_ID(); ?> -->

			
			<?php if (option::get('post_tags') == 'on') { ?><?php the_tags( '<div class="tag_list"> #', ' #', '</div>'); ?><?php } ?>
			
			
			<?php if (option::get('post_share') == 'on') { ?>
				<div class="post-meta">
					<div class="share_box">
						<div class="share_btn"><a href="http://twitter.com/share" data-url="<?php the_permalink() ?>" class="twitter-share-button" data-count="horizontal">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>
						<div class="share_btn"><iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;layout=button_count&amp;show_faces=false&amp;width=1000&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:80px; height:21px;" allowTransparency="true"></iframe></div>
						<div class="share_btn"><g:plusone size="medium"></g:plusone></div>
					</div>
				</div><!-- /.post-meta -->	
			<?php } ?>


			<?php if (option::get('post_related') == 'on') { ?>
					<?php wp_zoomrelated_posts(); ?>
			<?php } ?>
		 
		 
			<?php if (option::get('post_comments') == 'on') { 
				comments_template();
				} ?>
			
		<?php endwhile; ?>

	</div><!-- /#content -->
	
	
	<?php if ($template != 'full') { 
		get_sidebar(); 
	} else { echo "<div class=\"clear\"></div>"; } ?>
 
</div><!-- /#main -->
<?php get_footer(); ?> 