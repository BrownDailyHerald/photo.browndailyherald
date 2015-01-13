<?php
	$loop = new WP_Query(
	array(
		'post__not_in' => get_option( 'sticky_posts' ),
		'posts_per_page' => option::get('featured_number'),
		'meta_key' => 'wpzoom_is_featured',
		'meta_value' => 1
	) );
?>

<div id="slider">

	<h3><?php echo option::get('featured_title'); ?></h3>
 
	<div id="slides">

		<?php
		$i = 0;
		if ( $loop->have_posts() ) : ?>

        <ul class="slides">

            <?php rewind_posts();
			while ( $loop->have_posts() ) : $loop->the_post(); $i++; ?>

           	<li class="post-<?php the_ID(); ?>">

				<?php if (option::get('featured_thumb') == 'on' ) {

					get_the_image( array( 'size' => 'slider', 'width' => 305) );

				}?>

				<div class="slide_content">
					<h2><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

					<?php the_excerpt(); ?>

 					<?php if (option::get('featured_comments') == 'on') { ?><span class="comments"><?php comments_popup_link( __('0 comments', 'wpzoom'), __('1 comment', 'wpzoom'), __('% comments', 'wpzoom'), '', __('Comments are Disabled', 'wpzoom')); ?></span><?php } ?>

				</div>

			</li><?php endwhile; ?>
			<div class="clear"></div>

 		</ul><!-- /.slides -->

		<?php else : ?>

		<div class="notice">
			There are no featured posts. Start marking posts as featured, or disable the slider from <strong><a href="<?php echo home_url(); ?>/wp-admin/admin.php?page=wpzoom_options">Theme Options</a></strong>. <br />
		    For more information please <strong><a href="http://www.wpzoom.com/documentation/litepress/">read the documentation</a></strong>.
		</div><!-- /.notice -->

 		<?php endif; ?>

	</div><!-- /#slides -->
 

	<?php
	$i = 0;
	if ( $loop->have_posts() ) : ?>

	<div id="slider_nav">
		<div class="tiles">
			<?php
			$first = true;
			while ( $loop->have_posts() ) : $loop->the_post();  ?>

				<div class="item<?php echo $first ? ' current' : ''; ?> post-<?php the_ID(); ?>">
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php get_the_image( array( 'size' => 'slider-small', 'width' => 75, 'height' => 55, 'link_to_post' => false  ) ); ?><?php if (option::get('featured_date') == 'on') { ?><span><?php printf( __('%s at %s', 'wpzoom'),  get_the_date(), get_the_time()); ?></span><?php } ?><?php the_title(); ?></a>
					<div class="clear"></div>

				</div>
			<?php
			$first = false;
			endwhile; ?>
		</div>
	</div>

	<?php endif; ?>


 	<div class="clear"></div>

</div><!-- /#slider -->

<?php wp_reset_query(); ?>