<?php  
	$number = option::get('carousel_posts');
	$type = option::get('carousel_type');

	if ($type == 'Category')
	{
		$category1 = option::get('carousel_category');
		$param = 'cat';
		if (option::get('carousel_category') && (option::get('carousel_category') != 'off'))
		{
			$val = implode(',',option::get('carousel_category'));
		}
	}
	elseif ($type == 'Tag') {
		$tag1 = $instance['tag1'];
		$param = 'tag__in';
		if (option::get('carousel_tag') != 'off') {
			$val = option::get('carousel_tag');
		} 
	}
?>
 
<div id="featured" class="jcarousel">

	<h3><?php echo option::get('carousel_title'); ?></h3>
 	<ul>
 
	<?php 
		$loop = new WP_Query( array( 'post__not_in' => get_option( 'sticky_posts' ), 'posts_per_page' => $number, 'orderby' => 'date', 'order' => 'DESC', $param => $val ) );
		while ( $loop->have_posts() ) : $loop->the_post(); 
		unset( $image); 
		?>
		
 		<li>
			<?php get_the_image( array( 'size' => 'carousel',  'width' => 180, 'height' => 120, 'before' => '<div class="thumb">', 'after' => '</div>' ) );  ?>
		
			<a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
			 
		</li><?php endwhile; ?>
	</ul>
</div><!-- /.featured --> 
<div class="clear"></div>
<?php wp_reset_query(); ?>