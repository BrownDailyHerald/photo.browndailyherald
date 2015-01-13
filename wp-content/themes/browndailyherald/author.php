<?php get_header(); 
 	$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
	$staff_title = get_metadata('user', $curauth->ID, 'bdh_staff_title', true);

?>

<div id="main" role="main">

	<div id="content">
		<div class="author_top">
			<div class="author_photo"><?php echo get_avatar( $curauth->ID , 65 ); ?></div>
			<h1><a href="<?php echo $curauth->user_url; ?>"><?php echo $curauth->display_name; ?></a></h1>
<?php if (!empty($staff_title)): ?>
			<span class="author_title"><?php echo esc_html($staff_title); ?></span>
<?php endif; ?>
			<div class="clear"></div>
		</div>

		<div class="author_desc">
<?php if (!empty($curauth->user_description)): ?>
			<?php echo wpautop(wp_kses_post($curauth->user_description)); ?>
<?php else: ?>
			<p>No description available.</p>
<?php endif; ?>
		</div>

		<div class="post-meta">
<?php if (!empty($curauth->twitter)): ?>
			<div class="author_follow"><a href="https://twitter.com/<?php echo esc_attr($curauth->twitter); ?>" class="twitter-follow-button" data-show-count="true">Follow @<?php echo esc_html($curauth->twitter); ?></a></div>
<?php endif; ?>
			<div class="share_box">
				<div class="share_btn"><iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_author_posts_url($curauth->ID)); ?>&amp;layout=button_count&amp;show_faces=false&amp;width=1000&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:80px; height:21px;" allowTransparency="true"></iframe></div>
				<div class="share_btn"><a href="http://twitter.com/share" data-url="<?php the_permalink() ?>" class="twitter-share-button" data-count="horizontal">Tweet</a></div>
				<div class="share_btn"><g:plusone size="medium"></g:plusone></div>
			</div>
			<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
			<div class="clear"></div>
		</div>

<?php
if (have_posts()) { 
	$post_count = count_user_posts($curauth->ID); ?>
		<h1 class="archive_title">Articles by <?php echo $curauth->display_name; ?> (<?php echo $post_count; ?>)</h2>
		<?php get_template_part('loop'); ?>
<?php } // endif ?>

<?php
$authorMediaCount = count(author_media($curauth->ID));
if ($authorMediaCount != 0): ?>
		<h1 class="archive_title">Multimedia by <?php echo $curauth->display_name; ?> (<?php echo $authorMediaCount; ?>)</h2>
		<?php /* TODO: actual pagination */ display_author_media($curauth->ID, false, 999999, false, '', true); ?>
<?php endif; ?>

	</div> <!-- /#content -->
	
	<?php get_sidebar(); ?> 

</div> <!-- /#main -->
<?php get_footer(); ?> 
