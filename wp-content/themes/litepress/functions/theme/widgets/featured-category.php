<?php 
/**
 * Featured Category Widget
 */
add_action('widgets_init', create_function('', 'return register_widget("Category_Widget");'));

class Category_Widget extends WP_Widget
{
    function Category_Widget()
    {
        $widgetOps = array(
            "classname"   => "wpzoom_category",
            "description" => "Displays a featured block with posts from specific categories or tags.",
        );

        $controlOps = array(
             "id_base" => "category-widget"
        );

        $this->WP_Widget("category-widget", "WPZOOM: Featured Category", $widgetOps, $controlOps);
    }

    function widget($args, $instance)
    {
		extract($args);
        //print_r($instance);
        $title = apply_filters("widget_title", $instance["title"]);
 		$type = $instance['type'];
 		$category = $instance['category'];
		$slugs = $instance['slugs'];
		$posts = $instance['posts'];
		$exclude_featured = $instance['exclude_featured'];

		if ($type == 'tag')
		{
			$postsq = $slugs;
		}
		elseif ($type == 'cat')
		{
			$postsq = implode(",",$category); 
			$firstcategory = get_category($instance['category'][0]);
			if ($firstcategory) {
				$category_link = get_category_link($firstcategory);
			}
		
		}

		/* Exclude featured posts from Widget Posts */
		$postnotin = '';
		if ($exclude_featured == 'on') {
			
			$featured_posts = new WP_Query( 
				array( 
					'post__not_in' => get_option( 'sticky_posts' ),
					'posts_per_page' => option::get('featured_number'),
					'meta_key' => 'wpzoom_is_featured',
					'meta_value' => 1				
					) );
				
			while ($featured_posts->have_posts()) {
				$featured_posts->the_post();
				global $post;
				$postIDs[] = $post->ID;
			}
			$postnotin = $postIDs;
		}


 		$random = rand(0,1000);
        ?>

        <div class="category-widget">
           
           <?php if ( $title ) { echo '<h2 class="title">';
           if ($category_link) {
				echo '<a href="'.$category_link.'">';
		   }
		   echo $title;
		   if ($category_link) {
				echo '</a>';
		   }
		   echo '</h2>'; } ?>
            
			<?php $sq = new WP_Query( array( $type => $postsq, 'showposts' => $posts, 'orderby' => 'date', 'post__not_in' => $postnotin, 'order' => 'DESC' ) );?>

			<div id="tabs_<?php echo $random; ?>">

				<ul class="tabs">
					<?php if ( $sq->have_posts() ) : while( $sq->have_posts() ) : $sq->the_post(); global $post; ?>
	 				<li>
	 					<a href="<?php the_permalink(); ?>" title="<?php printf( the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">
	 						 
	 						<?php get_the_image( array( 'size' => 'featured-cat-small',  'width' => 65, 'height' => 50, 'link_to_post' => false, 'default_image' => 'http://placehold.it/65x50' ) ); ?>
	 
							<div class="item">
		 						<h3><?php the_title(); ?></h3>
		 						<p><?php the_content_limit(80); ?></p>
		 					</div>
	 					 	<div class="clear"></div>
						</a>
	 					 
					</li>
	  				<?php endwhile; ?>
	  				<?php endif; ?>
				</ul>

 				<?php rewind_posts($sq); ?>

 				<?php $i = 0; if ( $sq->have_posts() ) : while( $sq->have_posts() ) : $sq->the_post(); global $post; $i++; ?>
	 				<div id="tabs_<?php echo $random; ?>-<?php echo $i-1; ?>" class="slide">
						<?php get_the_image( array( 'size' => 'featured-cat', 'width' => 300, 'height' => 236, 'default_image' => 'http://placehold.it/300x236' ) ); ?>
	 				</div>
  				<?php endwhile; ?>
  				<?php endif; ?>
   
			</div>
   
        <div class="clear"></div>
       	</div><!-- ./category-widget -->

       	<script type="text/javascript">
			var $j = jQuery.noConflict();

			var tabId = '#tabs_<?php echo $random; ?>';

			$j(tabId + ' .tabs a').each(function(index, val){

				$j(this).data('href', $j(this).attr('href')).attr('href', tabId + '-' + index);
			});

			$j(document).ready(function(){
				
				$j("#tabs_<?php echo $random; ?>").tabs({
					event: "mouseover",
					fx: {
						opacity:'toggle',
						duration:130   
                    }
				});

				$j("#tabs_<?php echo $random; ?> .ui-tabs-nav a").click(function(){
					location.href = $j(this).data('href');
					return false;
				});

			});
 		</script>
 
        
        <?php
		}
		/* Update the widget settings.*/
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['type'] = $new_instance['type'];
 		$instance['name'] = strip_tags( $new_instance['name'] );
		$instance['category'] = $new_instance['category'];
		$instance['slugs'] = $new_instance['slugs'];
		$instance['posts'] = $new_instance['posts'];
		$instance['exclude_featured'] = $new_instance['exclude_featured'];

		return $instance;
	}

	/** Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function when creating your form elements. This handles the confusing stuff. */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Featured Category', 'wpzoom'), 'type' => 'cat', 'category' => '', 'posts' => '3', 'exclude_featured' => '');
		$instance = wp_parse_args( (array) $instance, $defaults );
 
	?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'wpzoom'); ?></label><br/>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>"  />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Posts marked by:', 'wpzoom'); ?></label>
			<select id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>" style="width:90%;">
			<option value="cat"<?php if ($instance['type'] == 'cat') { echo ' selected="selected"';} ?>>Categories</option>
			<option value="tag"<?php if ($instance['type'] == 'tag') { echo ' selected="selected"';} ?>>Tag(s)</option>
			</select>
		</p>
		
		
		<p>
			<label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category (if selected above):', 'wpzoom'); ?></label>
			<?php 
			$activeoptions = $instance['category'];
			if (!$activeoptions)
			{
				$activeoptions = array();
			}
			?>

			<select multiple="true" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>[]" style="width:90%; height: 100px;">

			<?php
				$cats = get_categories('hide_empty=0');

				foreach ($cats as $cat) {
				$option = '<option value="'.$cat->term_id;
				if ( in_array($cat->term_id,$activeoptions)) { $option .='" selected="selected'; }
				$option .= '">';
				$option .= $cat->cat_name;
				$option .= ' ('.$cat->category_count.')';
				$option .= '</option>';
				echo $option;
				}
			?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'slugs' ); ?>"><?php _e('Tag slugs (if selected above, separated by comma ","):', 'wpzoom'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'slugs' ); ?>" name="<?php echo $this->get_field_name( 'slugs' ); ?>" value="<?php echo $instance['slugs']; ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('posts'); ?>"><?php _e('Posts to show:', 'wpzoom'); ?></label>
			<select id="<?php echo $this->get_field_id('posts'); ?>" name="<?php echo $this->get_field_name('posts'); ?>" style="width:90%;">
			<?php
				$m = 0;
				while ($m < 20) {
				$m++;
				$option = '<option value="'.$m;
				if ($m == $instance['posts']) { $option .='" selected="selected';}
				$option .= '">';
				$option .= $m;
				$option .= '</option>';
				echo $option;
				}
			?>
			</select>
		</p>
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('exclude_featured'); ?>" name="<?php echo $this->get_field_name('exclude_featured'); ?>" <?php if ($instance['exclude_featured'] == 'on') { echo ' checked="checked"';  } ?> /> 
			<label for="<?php echo $this->get_field_id('exclude_featured'); ?>"><?php _e('Exclude Featured Posts from Widget', 'wpzoom'); ?></label>
			<br/>
		</p>
	 
	<?php
		}
}
?>