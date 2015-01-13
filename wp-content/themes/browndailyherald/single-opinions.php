<?php
/*
Template Name Posts: Opinions Column Template
*/
?>
<?php get_header(); ?>

<?php $template = get_post_meta($post->ID, 'wpzoom_post_template', true); ?>
<?php $reporter_type = get_post_meta($post->ID, 'bdh_reporter_type', true); ?>

<div id="main"<?php 
	  if ($template == 'side-left') {echo " class=\"side-left\"";}
	  if ($template == 'full') {echo " class=\"full-width\"";} 
	  ?>>
	
	<div id="content">

		<p class="category_list"><?php the_category(', '); ?></p>

		<h1 class="title">
			<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'wpzoom' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h1>

		<?php if (function_exists('the_subtitle')) { the_subtitle('<h2 class="subtitle">', '</h2>'); } ?>
		
		<?php while (have_posts()) : the_post(); ?>

			<?php if (option::get('post_author') == 'on') { ?><div class="meta-author"><?php _e('By', 'wpzoom'); ?> <?php if ( function_exists( 'coauthors_posts_links' ) ) { coauthors_posts_links(); } else { the_author_posts_link(); } ?><?php if (!empty($reporter_type)) { ?><br /><?php echo esc_html($reporter_type); ?><?php } ?></div><?php } ?>
 
			<div class="post-meta">
				<span class="meta-data">
				<?php if (option::get('post_date') == 'on') { ?><?php echo get_the_date('l, F j, Y'); ?><?php } ?>

				<?php edit_post_link( __('Edit', 'wpzoom'), '', ''); ?>
				</span>

				<div class="share_box">
					<?php if (function_exists('do_sociable')) { do_sociable(); } ?>
				</div>
				<div class="clear"></div>
			</div><!-- /.post-meta -->	

			<div id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
				 
				<div class="entry">
					<div class="post-sidebar">
<?php foreach (get_the_coauthor_meta('ID') as $cur_id): $user = get_userdata($cur_id);?>
						<div class="article-author-image post-image">
							<a href="<?php echo get_author_posts_url($cur_id); ?>" rel="post-sidebar"><?php echo get_avatar($cur_id, 300); ?></a>
							<p class="image-byline"><?php echo $user->display_name ?></p>
						</div>
<?php endforeach; ?>
					</div>

					<?php the_content(); ?>
					<div class="clear"></div>
					
					<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'wpzoom' ) . '</span>', 'after' => '</div>' ) ); ?>
					<div class="clear"></div>
				
				</div><!-- / .entry -->
				<div class="clear"></div>
			 
			</div><!-- #post-<?php the_ID(); ?> -->

			
			<?php if (option::get('post_tags') == 'on') { ?><?php the_tags( '<div class="tag_list"> Topics: ', ', ', '</div>'); ?><?php } ?>
			
			<div class="post-meta">
				<div class="share_box">
					<?php if (function_exists('do_sociable')) { do_sociable(); } ?>
				</div>
				<div class="clear"></div>
			</div><!-- /.post-meta -->

			<?php if (function_exists('MRP_show_related_posts')) MRP_show_related_posts(); ?>

			<?php if (function_exists('nrelate_related')) nrelate_related(); ?>
		 
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