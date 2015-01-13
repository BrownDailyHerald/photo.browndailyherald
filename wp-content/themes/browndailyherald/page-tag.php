<?php
/*
Template Name: Topics List
*/
?>
<?php get_header();

// We don't want to display orgseries stuff on pages, just articles.
global $orgseries;
if (isset($orgseries))
{
	remove_filter('the_content', array(&$orgseries, 'add_series_meta'), 12);
}

?>

<div id="main" role="main">

	<div id="content">
		<h1 class="archive_title">Topics</h1>

<?php

$tags = get_tags();
$html = '<ul class="post_tags_list">';
foreach ($tags as $tag){
	$tag_link = get_tag_link($tag->term_id);

	$html .= sprintf(
		'<li><a href="%s" title="%s" class="%s">%s</a></li>',
		$tag_link,
		esc_attr($tag->name . ' Tag'),
		esc_attr($tag->slug),
		esc_html($tag->name)
	);
}
$html .= '</ul>';
echo $html;

?>

	</div> <!-- /#content -->
	
	<?php get_sidebar(); ?> 

</div> <!-- /#main -->
<?php get_footer(); ?> 