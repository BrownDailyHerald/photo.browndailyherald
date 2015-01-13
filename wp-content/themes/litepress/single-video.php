<?php get_header(); ?>
<?php $template = get_post_meta($post->ID, 'wpzoom_post_template', true); ?>

<div id="main"<?php 
	  if ($template == 'side-left') {echo " class=\"side-left\"";}
	  if ($template == 'full') {echo " class=\"full-width\"";} 
	  ?>>
	  
  	<div id="content">
  
		<?php $video = get_post_meta($post->ID, 'wpzoom_post_embed_code', true); 
			if (strlen($video) > 1) { // Embedding video 
				$video = preg_replace("/(width\s*=\s*[\"\'])[0-9]+([\"\'])/i", "$1 650 $2", $video);
				$video = preg_replace("/(height\s*=\s*[\"\'])[0-9]+([\"\'])/i", "$1 360 $2", $video);
				$video = str_replace("<embed","<param name='wmode' value='transparent'></param><embed",$video);
				$video = str_replace("<embed","<embed wmode='transparent' ",$video); ?>
				
				<div class="post-cover"><?php echo "$video"; ?></div>
		<?php } ?>
	 
		<h1 class="title">
			<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'wpzoom' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h1>
	 
 
		<?php while (have_posts()) : the_post(); ?>
				
			<div id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
				 
				<div class="entry">
					<?php the_content(); ?>
					<div class="clear"></div>
					<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'wpzoom' ) . '</span>', 'after' => '</div>' ) ); ?>
					<div class="clear"></div>
				</div><!-- / .entry -->
				<div class="clear"></div>
			 
			</div><!-- #post-<?php the_ID(); ?> -->
			
 			<div class="post-meta">
				<?php if (option::get('post_date') == 'on') { ?><?php printf( __('%s at %s', 'wpzoom'),  get_the_date(), get_the_time()); ?><?php } ?>

				<?php edit_post_link( __('Edit', 'wpzoom'), '', ''); ?>

				<?php if (option::get('post_share') == 'on') { ?>
					<div class="share_box">
						<div class="share_btn"><a href="http://twitter.com/share" data-url="<?php the_permalink() ?>" class="twitter-share-button" data-count="horizontal">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>
						<div class="share_btn"><iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;layout=button_count&amp;show_faces=false&amp;width=1000&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:80px; height:21px;" allowTransparency="true"></iframe></div>
					</div>
				<?php } ?>
					 
			</div><!-- /.post-meta -->	
	 
	 
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