<div class="video_slider">
  	<h3><?php echo option::get('video_title'); ?></h3>

		<div id="panes">

			<?php

			$video_posts =  option::get('video_posts');

			$video_loop = new WP_Query(
				array(
					'post_type' 		=> 'video',
					'post__not_in' 		=> get_option( 'sticky_posts' ),
					'posts_per_page'	=> $video_posts
					) );
				?>

	        <?php $i = 0; while ($video_loop->have_posts())
				{
				$video_loop->the_post(); global $post;

				unset($video);
				$video = get_post_meta($post->ID, 'wpzoom_post_embed_code', true);
 				?>


			<div class="<?php $i++; echo $i . (($i == 1) ? " active" : ""); ?>">

				<?php if (strlen($video) > 1) { // Embedding video
					$video = preg_replace("/(width\s*=\s*[\"\'])[0-9]+([\"\'])/i", "$1 580 $2", $video);
					$video = preg_replace("/(height\s*=\s*[\"\'])[0-9]+([\"\'])/i", "$1 320 $2", $video);
					$video = str_replace("<embed","<param name='wmode' value='transparent'></param><embed",$video);
					$video = str_replace("<embed","<embed wmode='transparent' ",$video); ?>

					<span class="cover"><?php echo "$video"; ?></span>

				<?php } else {

  						get_the_image( array( 'size' => 'video-big', 'width' => 580, 'height' => 320, 'before' => '<span class="cover">', 'after' => '</span>'  ) );

					} // if a video does not exist ?>

				<?php if (option::get('video_post_title') == 'on') { ?><h4><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h4><?php } ?>

 				<?php if (option::get('video_excerpt') == 'on') { ?><?php the_excerpt(); ?><?php } ?>


			</div>

			<?php } ?>
		</div> <!-- /#panes -->

		<div class="latest_videos">
			<?php
 			$video_posts =  option::get('video_posts');

			$video_loop = new WP_Query(
				array(
					'post_type' 		=> 'video',
					'post__not_in' 		=> get_option( 'sticky_posts' ),
					'posts_per_page'	=> $video_posts
					) );
				?>
 			<div class="scrollable">

				<ul class="items">

				 <?php  $i = 0; while ($video_loop->have_posts())
						{
						$video_loop->the_post(); global $post;
					?>


					<li class="<?php $i++; echo $i . (($i == 1) ? " active" : ""); ?>">

						<a title="<?php the_title(); ?>">

 							<?php get_the_image( array( 'size' => 'video-small', 'width' => 145, 'height' => 95, 'link_to_post' => false, 'before' => '<i></i>'  ) ); ?>
 							<div class="item_info">
 								<?php the_title(); ?>
 								<?php if (option::get('video_date') == 'on') { ?><span><?php printf('%s', get_the_date()); ?></span><?php } ?>
							</div>
						</a>
  					</li>
					<?php } ?>

				</ul>
			</div>

			<a class="prevPage browse left prev">Prev</a>
	 		<a class="nextPage browse right next">Next</a>
 		</div>

</div> <!-- /.video_slider -->
<div class="clear"></div>
<?php wp_reset_query(); ?>