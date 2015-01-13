	<div id="footer">
		
		<?php if ( is_active_sidebar( 'footer_1'  ) ||  is_active_sidebar( 'footer_2'  )  ||  is_active_sidebar( 'footer_3'  ) ) : ?>
			<div class="widget-area">
		<?php endif; ?>
  			
			<div class="column">
				<?php if (function_exists('dynamic_sidebar')) { dynamic_sidebar('Footer (column 1)'); } ?>
			</div><!-- / .column -->
			
			<div class="column">
				<?php if (function_exists('dynamic_sidebar')) { dynamic_sidebar('Footer (column 2)'); } ?>
			</div><!-- / .column -->
			
			<div class="column last">
				<?php if (function_exists('dynamic_sidebar')) { dynamic_sidebar('Footer (column 3)'); } ?>
			</div><!-- / .column -->
 
		<?php if ( is_active_sidebar( 'footer_1'  ) ||  is_active_sidebar( 'footer_2'  )  ||  is_active_sidebar( 'footer_3'  ) ) : ?>
  			<div class="clear"></div>
	        </div><!-- /.widget-area-->		
        <?php endif; ?>
        <div class="clear"></div>
        
        <div class="copyright">
			<div class="left">
				<?php _e('Copyright', 'wpzoom'); ?> &copy; <?php echo date("Y",time()); ?> <?php bloginfo('name'); ?>. <?php _e('All Rights Reserved', 'wpzoom'); ?>.
			</div>
			
			<div class="right">
				<p class="wpzoom"><a href="http://www.wpzoom.com" target="_blank" title="Premium WordPress Themes"><img src="<?php echo get_template_directory_uri(); ?>/images/wpzoom.png" alt="WPZOOM" /></a> <?php _e('Designed by', 'wpzoom'); ?></p>
			</div>
			
		</div><!-- /.copyright -->
 
    </div>
 
</div><!-- /.wrap -->


<?php if (is_home() && $paged < 2 && option::get('video_enable') == 'on' )  { ui::js("jtools"); } ?>
	
<?php if ( $paged < 2 && is_home() ) { ?>
<script type="text/javascript">
jQuery(document).ready(function() {

	<?php if (option::get('video_enable') == 'on' ) {  /* Video Slider */ ?>
 	 
		var scrollableElements = jQuery(".scrollable li");
		if (scrollableElements.size() <= 4) {
			jQuery("a.browse").addClass("disabled");
		}
		jQuery(".scrollable").scrollable({
			"keyboard": false,
			"vertical":true
		});

		scrollableElements.click(function() {
			jQuery(".scrollable .active").removeClass("active");
			jQuery(".scrollable ." + jQuery(this).attr("class").split(' ').slice(0, 1)).addClass("active");
			jQuery("#panes .active").removeClass("active");
			jQuery("#panes ." + jQuery(this).attr("class").split(' ').slice(0, 1)).addClass("active");
		}).filter(":first").click();
	 
	<?php } ?>

 
	<?php if (is_home() && $paged < 2 && option::get('carousel_enable') == 'on' ) { /* Footer Carousel */ ?>
	 	jQuery('#featured').jcarousel({
			scroll: 1,
			wrap: 'circular',
			auto: <?php if (option::get('carousel_rotate') == 'on') { echo option::get('carousel_interval'); }  else { ?>0<?php } ?> 
		});
	<?php } ?>
  
});

<?php if (option::get('featured_enable') == 'on' ) {  /* Main Slider */ ?>
jQuery(window).load(function(){

	if ( jQuery('.slides li').length > 0 ) {

		jQuery('#slides').flexslider({
			controlNav: false,
			directionNav: true,
			animationLoop: true,
			animation: 'fade',
			useCSS: true,
			smoothHeight: false,
			touch: false,
	 		slideshow: <?php echo option::get('featured_rotate') == 'on' ? 'true' : 'false'; ?>,
			<?php if ( option::get('featured_rotate') == 'on' ) echo 'slideshowSpeed: ' . option::get('featured_interval') . ','; ?>
			pauseOnAction: true,
			animationSpeed: 150,
			start: function(slider){
				jQuery('#slider_nav .item').hover(function(){
					var id = getPostIdClass(this);
					if ( id <= 0 ) return;

					var index = slider.slides.index( slider.slides.filter('.' + id) );

					slider.direction = (index > slider.currentSlide) ? 'next' : 'prev';
					slider.flexAnimate(index, slider.pauseOnAction);
				});
			},
			before: function(slider){
				var id = getPostIdClass( slider.slides.eq(slider.animatingTo) );
				if ( id <= 0 ) return;

				jQuery('#slider_nav .item').removeClass('current');
				jQuery('#slider_nav .item.' + id).addClass('current');

				if ( jQuery('#slider_nav .row').length > 1 ) {
					var navSlider = jQuery('#slider_nav').data('flexslider'),
					    currPage = navSlider.slides.index( navSlider.slides.find('.item.' + id).parent('.row') );
					navSlider.direction = (currPage > navSlider.currentSlide) ? 'next' : 'prev';
					navSlider.flexAnimate(currPage, navSlider.pauseOnAction);
				}
			}
		});

		jQuery('#slider_nav .item').wrapInChunks('<div class="row" />', 5);

		jQuery('#slider_nav').flexslider({
			selector: '.tiles > .row',
	  		direction:'vertical',
			controlNav: true,
			directionNav: false,
			animationLoop: false,
			animation: 'slide',
			useCSS: true,
			smoothHeight: false,
			touch: false,
			slideshow: false,
			pauseOnAction: true,
			animationSpeed: 150
		});

	}
});
<?php } ?>

</script>
<?php } ?>


<?php wp_footer(); ?> 

<?php
wp_reset_query();
if (is_single()) { ?><script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script><?php } // Google Plus button
?>
 

</body>
</html>