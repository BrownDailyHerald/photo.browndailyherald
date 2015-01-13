<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 
    <title><?php ui::title(); ?></title>

    <link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" media="screen" />
     
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

    <?php wp_head(); ?>
    <?php if (is_home() ) {
    	if ( option::get('featured_enable') == 'on' ) { ui::js("flexslider");  }
   		wp_enqueue_script("jquery-ui-tabs"); 
	} ?>
</head>
<body <?php body_class() ?>>

    <header id="header">
        <div id="navbar"><div class="wrap">
            <?php if (has_nav_menu( 'secondary' )) { 
				wp_nav_menu(array(
				'container' => 'menu',
				'container_class' => '',
				'menu_class' => 'dropdown',
				'menu_id' => 'mainmenu',
				'sort_column' => 'menu_order',
				'theme_location' => 'secondary'
				));
			}					
			else
				{
					echo '<p>Please set your Top navigation menu on the <strong><a href="'.get_admin_url().'nav-menus.php">Appearance > Menus</a></strong> page.</p>
				 ';
				}
            ?>
            <div class="clear"></div>
        </div><!-- /.wrap --></div><!-- /#navbar -->
        
        <div class="wrap">
            
            <div id="logo">
				<?php if (!option::get('misc_logo_path')) { echo "<h1>"; } ?>
				
				<a href="http://www.browndailyherald.com" title="<?php bloginfo('description'); ?>"><!--altered home link-->
					<?php if (!option::get('misc_logo_path')) { bloginfo('name'); } else { ?>
						<img src="<?php echo ui::logo(); ?>" alt="<?php bloginfo('name'); ?>" />
					<?php } ?>
				</a>
				
				<?php if (!option::get('misc_logo_path')) { echo "</h1>"; } ?>
			</div><!-- / #logo -->


			<?php if (option::get('ad_head_select') == 'on') { ?>
				<div class="adv">
				
					<?php if ( option::get('ad_head_code') <> "") { 
						echo stripslashes(option::get('ad_head_code'));             
					} else { ?>
						<a href="<?php echo option::get('banner_top_url'); ?>"><img src="<?php echo option::get('banner_top'); ?>" alt="<?php echo option::get('banner_top_alt'); ?>" /></a>
					<?php } ?>		   	
						
				</div><!-- /.adv --> <div class="clear"></div>
			<?php } ?>

			<?php if (option::get('searchform_enable') == 'on') { ?>
				<div class="search_form">
					<?php get_search_form(); ?>
				</div>
			<?php } ?>

			 <div class="clear"></div>

         </div>
    </header>
    
    <div id="navbarsecond">
        <div class="wrap">
            <?php if (has_nav_menu( 'primary' )) { 
				wp_nav_menu(array(
				'container' => 'menu',
				'container_class' => '',
				'menu_class' => 'dropdown',
				'menu_id' => 'secondmenu',
				'sort_column' => 'menu_order',
				'theme_location' => 'primary'
				));
			}					
			else
				{
					echo '<p>Please set your Main navigation menu on the <strong><a href="'.get_admin_url().'nav-menus.php">Appearance > Menus</a></strong> page.</p>
				 ';
				}
            ?>
        </div><!-- /.wrap -->
        <div class="clear"></div>
    </div><!-- /#navbarsecond -->

    <div class="wrap">