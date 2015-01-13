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
<?php
	$id = get_post_meta(get_the_ID(), 'wpcf-slider-id', TRUE); 
?>
<?php if( function_exists( "get_glam_slider" ) ){ get_glam_slider( $slider_id="$id",$set="1") ;}?>
<?php get_footer(); ?>

