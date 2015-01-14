<?php get_header();
/*
Template Name: Home
*/
?>
<style type="text/css">

body {
	min-width: 1000px;
}
	
#home-content {
	height: auto;
}

.sidebar, .features {
	float: left;
	display: block;
}

.sidebar {
	width: 150px;
}

.features {
	width: 850px;
}

.people-box {
	width: 470px;
	height: 420px;
	display: block;
	float: left;
	padding-left: 15px;
	padding-right: 30px;
}

.people-box img {
	width: 440px;
}

.people-box h3{
	margin-top: 0px;
	margin-bottom: 3px;
}

.people-box p {
	margin-top: 0px;
	margin-bottom: 0px;
}

.row {
	width: 950px;
	margin: auto;
}

@media screen and (max-width: 1000px) {
    .sidebar {
    	visibility: hidden;
        display: none;
        height: 0px;
    }
	.people-box {
		width: 420px;
		height: 410px;
		display: block;
		float: left;
		padding-left: 15px;
		padding-right: 30px;
	}
	.people-box img {
		width: 420px;
	}
	
}

@media screen and (max-width: 750px) {
	body {
		min-width: 700px;
	}
    .sidebar {
    	visibility: hidden;
        display: none;
        height: 0px;
    }
    .features {
	 	width: 700px;
	}
	.people-box {
		width: 320px;
		height: 310px;
		display: block;
		float: left;
		padding-left: 15px;
		padding-right: 30px;
	}
	.people-box img {
		width: 320px;
	}
	
}

@media screen and (max-width: 550px) {
    body {
    	margin: auto;
		min-width: 400px;
	}
    .sidebar {
    	visibility: hidden;
        display: none;
        height: 0px;
    }
    .features {
	 	width: 400px;
	}
	.row {
		width: 400px;
	}
	#home-content {
		max-width: 400px;
	}
	.people-box {
		margin: auto;
		width: 400px;
		height: 340px;
		display: block;
	}
	.people-box img {
		width: 400px;
	}
}
</style>

<body>                
 <link href="wp-content/themes/browndailyherald/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<div id="home-content" class="clearfix">
	<div class="sidebar">
		<?php

        $args = array(
            'post_type' => 'essay',
            'orderby' => 'ASC'
        );

        $the_query = new WP_Query( $args );

    	?>

			    <?php if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : 
			    												$the_query->the_post(); 
			    												$link = get_post_meta(get_the_ID(), 'wpcf-link', TRUE); ?>

			     <p><a href="<?php echo $link; ?>"><?php the_title(); ?></a></p>

			    <?php endwhile; else : ?>

			    <p>There are no essays :( </p>

			<?php endif; wp_reset_postdata(); ?>
	</div>
	<div class="features">
  		<div class="row">

			<?php

	        $args = array(
	            'post_type' => 'essay',
	            'orderby' => 'ASC'
	        );

	        $the_query = new WP_Query( $args );

	    	?>

			    <?php if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) :
			    												 $the_query->the_post(); 
			    												 $link = get_post_meta(get_the_ID(), 'wpcf-link', TRUE); 
			    												 $photo = get_post_meta(get_the_ID(), 'wpcf-photo', TRUE); ?>
			      <div class="people-box">
			        <div class="thumbnail" style="border: none;">
			          <a href="<?php echo $link; ?>"><img src="<?php echo $photo; ?>" alt="..."></a>
			          <div class="caption">
			            <h3><?php the_title(); ?></h3>
			            <p><?php the_content();?></p>
			          </div>
			        </div>
			      </div>

			    <?php endwhile; else : ?>

			    <p>There are no essays :( </p>

			<?php endif; wp_reset_postdata(); ?>

		</div>
	</div>
</div>
</body>
<?php get_footer(); ?>