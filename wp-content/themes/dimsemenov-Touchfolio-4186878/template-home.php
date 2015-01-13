<?php 
/* 
	Template Name: Home 
*/ 
?>
<?php get_header(); ?>

<?php include (TEMPLATEPATH . '/assets/inc/inc-courtesyNav.php'); ?>
<?php include (TEMPLATEPATH . '/inc/inc-header.php'); ?>
<?php include (TEMPLATEPATH . '/assets/inc/inc-mainNav.php'); ?>

<body>
hello world

<?php echo do_shortcode("[huge_it_slider id='1']"); ?>
</body>

<?php get_footer("home"); ?>