<?php
/*
Template Name Posts: Video Template
*/
?>
<?php get_header(); ?>

<?php $template = get_post_meta($post->ID, 'wpzoom_post_template', true); ?>
<?php $reporter_type = get_post_meta($post->ID, 'bdh_reporter_type', true); ?>
<?php $post_thumbnail_id = get_post_thumbnail_id($post->ID); ?>

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
			<?php $images =& get_children(sprintf('post_parent=%d&post_type=attachment&post_mime_type=image&orderby=menu_order&order=ASC', $post->ID)); ?>
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

			<?php $video = get_post_meta($post->ID, 'wpzoom_post_embed_code', true); 
				if (strlen($video) > 1) { // Embedding video 
					if (strpos($video, '<iframe') !== false) {
						$video = preg_replace("/(width\s*=\s*[\"\'])[0-9]+([\"\'])/i", "$1 650 $2", $video);
						$video = preg_replace("/(height\s*=\s*[\"\'])[0-9]+([\"\'])/i", "$1 360 $2", $video);
						$video = str_replace("<embed","<param name='wmode' value='transparent'></param><embed",$video);
						$video = str_replace("<embed","<embed wmode='transparent' ",$video);
					} else {
						$video = wp_oembed_get($video, array('width' => 650, 'height' => 360));
					}
					?>
					<div class="post-cover"><?php echo "$video"; ?></div>
			<?php } ?>

			<div id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
				 
				<div class="entry">
<?php
// Per http://codex.wordpress.org/Function_Reference/the_content
$content = get_the_content();
$content = apply_filters('the_content', $content);
$content = str_replace(']]>', ']]&gt;', $content);

$paragraph_after = 3;
$offset = 0;

for ($i = 0; $i < $paragraph_after; ++$i) {
	$offset = strpos($content, '</p>', $offset);

	// If we run through the whole post and don't find a paragraph, stop early.
	if ($offset === false) {
		$offset = strlen($content);
		break;
	}

	$offset += strlen('</p>');

	// We reached the end of the post.
	if ($offset === strlen($content)) {
		break;
	}
}

// Display the first part of the post.
echo substr($content, 0, $offset);
?>
<?php if (!empty($images)): ?>
					<div class="post-sidebar">
<?php foreach ($images as $attachment): ?>
<?php
	if ($post_thumbnail_id == $attachment->ID) {
		continue;
	}

	$image_attributes = wp_get_attachment_image_src($attachment->ID, 'full');
	$title_attr = '';
	if (empty($attachment->post_excerpt)) {
		$title_attr = strip_tags(get_media_credit_html($attachment->ID));
	}
?>
						<div id="post-<?php echo $attachment->ID; ?>" class="post-image">
							<a href="<?php echo $image_attributes[0]; ?>" title="<?php echo esc_attr($title_attr); ?>" rel="post-sidebar"><?php echo wp_get_attachment_image($attachment->ID, 'post-sidebar'); ?></a>
							<p class="image-byline"><?php echo get_media_credit_html($attachment->ID); ?></p>
<?php if (!empty($attachment->post_excerpt)): ?>
							<p class="image-caption"><?php echo $attachment->post_excerpt; ?></p>
<?php endif; ?>
						</div>
<?php endforeach; ?>
					</div>
<?php endif; ?>
<?php
//Display the rest of the content
echo substr($content, $offset);
?>
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