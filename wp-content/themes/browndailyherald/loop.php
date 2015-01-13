<div id="recent-posts" class="clearfix">
 
<?php
while (have_posts()) : the_post();?>
	<div id="post-<?php the_ID(); ?>" class="recent-post">

		<?php if (option::get('index_thumb') == 'on') { 
 			get_the_image( array( 'size' => 'thumbnail', 'width' => option::get('thumb_width'), 'height' => option::get('thumb_height'), 'before' => '<div class="post-thumb">', 'after' => '</div>' ) );
 		} ?>
 
		<div class="post-content">	
<?php if (is_category()): ?>
<?php
$cats = get_the_category();
$categories = array();
foreach ($cats as $cur_cat) {
	if ($cur_cat->term_id == get_query_var('cat')) {
		continue;
	}

	$categories[] = sprintf(
		'<a href="%s" title="%s">%s</a>',
		get_category_link($cur_cat->term_id),
		esc_attr(sprintf( __( "View all posts in %s" ), $cur_cat->name)),
		$cur_cat->name
	);
}

?>
<?php if (!empty($categories)): ?>
			<p class="category_list"><?php echo implode(', ', $categories); ?></p>
<?php endif; ?>
<?php else: ?>
			<p class="category_list"><?php the_category(', '); ?></p>
<?php endif; ?>
  			<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e( 'Permalink to %s', 'wpzoom'); ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

			<?php if (option::get('post_author') == 'on' && !is_author()  && get_post_type() == 'post') { ?><div class="meta-author"><?php _e('By', 'wpzoom'); ?> <?php if ( function_exists( 'coauthors_posts_links' ) ) { coauthors_posts_links(); } else { the_author_posts_link(); } ?></div><?php } ?>

 			<div class="recent-meta">
				<?php if (option::get('display_category') == 'on' && get_post_type() == 'post') { ?><span><?php the_category(', '); ?></span><?php } ?>
				<?php if (option::get('display_date') == 'on' && get_post_type() == 'post') { ?><span><?php printf( __('%s', 'wpzoom'),  get_the_date()); ?></span><?php } ?>
				<?php if (option::get('display_comments') == 'on' && get_post_type() == 'post') { ?><span class="comments"><?php comments_popup_link( __('0 comments', 'wpzoom'), __('1 comment', 'wpzoom'), __('% comments', 'wpzoom'), '', __('Comments are Disabled', 'wpzoom')); ?></span><?php } ?>
				 
				<?php edit_post_link( __('Edit', 'wpzoom'), '<span>', '</span>'); ?>
			</div><!-- /.post-meta -->	

 
 			<div class="entry">
				<?php if (option::get('display_content') == 'Full Content') {  the_content('<span>'.__('Read more', 'wpzoom').' &#8250;</span>'); } if (option::get('display_content') == 'Excerpt')  { the_excerpt(); } ?>

				<?php if (option::get('display_readmore') == 'on' && option::get('display_content') == 'Excerpt') { ?><a class="more-link" href="<?php the_permalink() ?>"><span><?php _e('Read more', 'wpzoom'); ?> &#8250;</span></a><?php } ?>
			</div><!-- /.entry -->
			
 
		</div><!-- /.post-content -->

		<div class="clear"></div>

	</div><!-- #post-<?php the_ID(); ?> -->

<?php endwhile; ?>
<?php get_template_part( 'pagination'); ?>
<?php wp_reset_query(); ?>
</div>
