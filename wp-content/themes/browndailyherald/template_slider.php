<?php get_header();
/*
Template Name: Slider
*/
?>
<style type="text/css">
.glam_slider {
	margin: auto !important;
	padding-bottom: 30px;
}
</style>
<div class="gallery-page">
	<h2><?php the_title(); ?></h2>
	<h5><?php if(have_posts()) : while(have_posts()) : the_post(); the_content(); endwhile; endif; ?></h5>
</div>
<?php
	$id = get_post_meta(get_the_ID(), 'wpcf-slider-id', TRUE); 
?>
<?php if( function_exists( "get_glam_slider" ) ){ get_glam_slider( $slider_id="$id",$set="1") ;}?>
<?php get_footer(); ?>

