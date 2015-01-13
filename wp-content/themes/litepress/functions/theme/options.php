<?php return array(


/* Theme Admin Menu */
"menu" => array(
     array("id"    => "1",
          "name"  => "General"),

     array("id"    => "2",
          "name"  => "Homepage"),

  	array("id"    => "5",
          "name"  => "Styling"),

  	array("id"    => "7",
          "name"  => "Banners"),
),

/* Theme Admin Options */
"id1" => array(
     array("type"  => "preheader",
          "name"  => "Theme Settings"),

     array("name"  => "Color Style",
          "desc"  => "Choose the style that you would like to use.<br />",
          "id"    => "theme_style",
          "options" => array('Default', 'Boxed Default', 'Boxed Red', 'Boxed Black', 'Blue', 'Red', 'Black'),
          "std"   => "Default",
          "type"  => "select"),

	array("name"  => "Logo Image",
          "desc"  => "Upload a custom logo image for your site, or you can specify an image URL directly.",
          "id"    => "misc_logo_path",
          "std"   => "",
          "type"  => "upload"),

     array("name"  => "Favicon URL",
          "desc"  => "Upload a favicon image (16&times;16px).",
          "id"    => "misc_favicon",
          "std"   => "",
          "type"  => "upload"),

     array("name"  => "Custom Feed URL",
          "desc"  => "Example: <strong>http://feeds.feedburner.com/wpzoom</strong>",
          "id"    => "misc_feedburner",
          "std"   => "",
          "type"  => "text"),

 	array("name"  => "Enable comments on static pages",
          "id"    => "comments_page",
          "std"   => "off",
          "type"  => "checkbox"),

	array("name"  => "Display Search Form in the Header",
          "id"    => "searchform_enable",
          "std"   => "on",
          "type"  => "checkbox"),


 	array("type"  => "preheader",
          "name"  => "Global Posts Options"),

	array("name"  => "Content",
          "desc"  => "Number of posts displayed on homepage can be changed <a href=\"options-reading.php\" target=\"_blank\">here</a>.",
          "id"    => "display_content",
          "options" => array('Excerpt', 'Full Content', 'None'),
          "std"   => "Excerpt",
          "type"  => "select"),

    array("name"  => "Excerpt length",
          "desc"  => "Default: <strong>50</strong> (words)",
          "id"    => "excerpt_length",
          "std"   => "50",
          "type"  => "text"),

    array("type" => "startsub",
            "name" => "Thumbnails"),

        array("name"  => "Display thumbnail",
              "id"    => "index_thumb",
              "std"   => "on",
              "type"  => "checkbox"),

        array("name"  => "Thumbnail Width (in pixels)",
              "desc"  => "Default: <strong>200</strong> (pixels)",
              "id"    => "thumb_width",
              "std"   => "200",
              "type"  => "text"),

        array("name"  => "Thumbnail Height (in pixels)",
              "desc"  => "Default: <strong>150</strong> (pixels)",
              "id"    => "thumb_height",
              "std"   => "150",
              "type"  => "text"),
    array("type"  => "endsub"), 


    array("name"  => "Display Category",
          "id"    => "display_category",
          "std"   => "off",
          "type"  => "checkbox"),

    array("name"  => "Display Comments Count",
          "id"    => "display_comments",
          "std"   => "on",
          "type"  => "checkbox"),

    array("name"  => "Display Read More link",
          "id"    => "display_readmore",
          "std"   => "on",
          "type"  => "checkbox"),

	array("name"  => "Display Date/Time",
          "desc"  => "<strong>Date/Time format</strong> can be changed <a href='options-general.php' target='_blank'>here</a>.",
          "id"    => "display_date",
          "std"   => "on",
          "type"  => "checkbox"),


	array("type"  => "preheader",
          "name"  => "Single Post Options"),

    array("type" => "startsub",
            "name" => "Post Image"),

    array("name"  => "Display Featured Image at the Top",
          "id"    => "post_thumb",
          "std"   => "off",
          "type"  => "checkbox"),

    array("name"  => "Limit Height of Featured Image?",
          "id"    => "post_thumb_limit",
          "std"   => "off",
          "type"  => "checkbox"),

    array("name"  => "Featured Image Height (in pixels)",
          "desc"  => "Default: <strong>250</strong> (pixels)",
          "id"    => "post_thumb_height",
          "std"   => "250",
          "type"  => "text"),

    array("type"  => "endsub"), 


	array("name"  => "Display Date/Time",
          "desc"  => "<strong>Date/Time format</strong> can be changed <a href='options-general.php' target='_blank'>here</a>.",
          "id"    => "post_date",
          "std"   => "on",
          "type"  => "checkbox"),

     array("name"  => "Display Author",
          "desc"  => "You can edit your profile on this <a href='profile.php' target='_blank'>page</a>.",
          "id"    => "post_author",
          "std"   => "on",
          "type"  => "checkbox"),

     array("name"  => "Display Tags",
          "id"    => "post_tags",
          "std"   => "on",
          "type"  => "checkbox"),

	array("name"  => "Display Share Buttons",
          "id"    => "post_share",
          "std"   => "on",
          "type"  => "checkbox"),

     array("name"  => "Display Related Posts",
          "id"    => "post_related",
          "std"   => "on",
          "type"  => "checkbox"),

     array("name"  => "Display Comments",
          "id"    => "post_comments",
          "std"   => "on",
          "type"  => "checkbox"),

),

"id2" => array(
 
 	array("type"  => "preheader",
          "name"  => "Featured Section"),

	array("name"  => "Enable the Featured Section",
          "desc"  => "Edit posts which you want to feature, and check the option from editing page: <strong>Feature this Post?</strong> ",
          "id"    => "featured_enable",
          "std"   => "on",
          "type"  => "checkbox"),

   	array("name"  => "Title for Featured Section",
          "desc"  => "Default: <em>Featured News</em>",
          "id"    => "featured_title",
          "std"   => "Featured News",
          "type"  => "text"),

  	array("name"  => "Number of Featured Posts",
          "desc"  => "Default: 5",
          "id"    => "featured_number",
          "std"   => "5",
          "type"  => "text"),

	array("name"  => "Autoplay Slider",
          "desc"  => "Should the slider start rotating automatically?",
          "id"    => "featured_rotate",
          "std"   => "off",
          "type"  => "checkbox"),

	array("name"  => "Autoplay Interval",
          "desc"  => "Select the interval (in miliseconds) at which the slider should change posts (if autoplay is enabled). Default: 3000 (3 seconds).",
          "id"    => "featured_interval",
          "std"   => "3000",
          "type"  => "text"),

	array("name"  => "Display thumbnail",
          "id"    => "featured_thumb",
          "std"   => "on",
          "type"  => "checkbox"),

    array("name"  => "Thumbnail Height (in pixels)",
          "desc"  => "Default: <strong>230</strong> (pixels)",
          "id"    => "featured_thumb_height",
          "std"   => "230",
          "type"  => "text"),

    array("name"  => "Display Date/Time",
          "desc"  => "<strong>Date/Time format</strong> can be changed <a href='options-general.php' target='_blank'>here</a>.",
          "id"    => "featured_date",
          "std"   => "on",
          "type"  => "checkbox"),

    array("name"  => "Display Comments Count",
          "id"    => "featured_comments",
          "std"   => "on",
          "type"  => "checkbox"),



	array("type"  => "preheader",
          "name"  => "Recent Posts"),

	array("name"  => "Display Recent Posts on Homepage",
          "id"    => "recent_posts",
          "std"   => "on",
          "type"  => "checkbox"),

  	array("name"  => "Title for Recent Posts",
          "desc"  => "Default: <em>Other News</em>",
          "id"    => "recent_title",
          "std"   => "Other News",
          "type"  => "text"),

	array("name"  => "Exclude categories",
          "desc"  => "Choose the categories which should be excluded from the main Loop on the homepage.<br/><em>Press CTRL or CMD key to select/deselect multiple categories </em>",
          "id"    => "recent_part_exclude",
          "std"   => "",
          "type"  => "select-category-multi"),

	array("name"  => "Hide Featured Posts in Recent Posts?",
          "desc"  => "You can use this option if you want to hide posts which are featured in the slider on front page.",
          "id"    => "hide_featured",
          "std"   => "on",
          "type"  => "checkbox"),


    array("type"  => "preheader",
          "name"  => "Video Slider"),

    array("name"  => "Display Video Slider on Homepage?",
          "desc"  => "This slider will display Video Custom Post Types, which you can add on this <a href='edit.php?post_type=video' target='_blank'>address</a>",
          "id"    => "video_enable",
          "std"   => "on",
          "type"  => "checkbox"),

    array("name"  => "Section Title",
          "desc"  => "Default: <em>Video</em>",
          "id"    => "video_title",
          "std"   => "Video",
          "type"  => "text"),

    array("name"  => "Number of Featured Posts",
          "desc"  => "Default: 10",
          "id"    => "video_posts",
          "std"   => "10",
          "type"  => "text"),

    array("name"  => "Display Video Title",
          "id"    => "video_post_title",
          "std"   => "on",
          "type"  => "checkbox"),

    array("name"  => "Display Date",
          "id"    => "video_date",
          "std"   => "on",
          "type"  => "checkbox"),

    array("name"  => "Display Excerpt",
          "id"    => "video_excerpt",
          "std"   => "on",
          "type"  => "checkbox"),


	array("type"  => "preheader",
          "name"  => "Carousel Settings"),

	array("name"  => "Display a Carousel on Homepage?",
          "desc"  => "This carousel will appear below the Video Section on homepage.",
          "id"    => "carousel_enable",
          "std"   => "on",
          "type"  => "checkbox"),

  	array("name"  => "Carousel Title",
          "desc"  => "Default: <em>Editorial</em>",
          "id"    => "carousel_title",
          "std"   => "Editorial",
          "type"  => "text"),

  	array("name"  => "Number of Posts",
          "desc"  => "Default: 10",
          "id"    => "carousel_posts",
          "std"   => "10",
          "type"  => "text"),

	array("name"  => "Autoplay Carousel?",
          "desc"  => "Should the carousel start rotating automatically?",
          "id"    => "carousel_rotate",
          "std"   => "off",
          "type"  => "checkbox"),

	array("name"  => "Autoplay Interval",
          "desc"  => "Select the interval (in milliseconds) at which the carousel should rotate (if autoplay is enabled). Default: 3 (3 seconds).",
          "id"    => "carousel_interval",
          "std"   => "3",
          "type"  => "text"),

 	array("name"  => "Display Posts By",
          "id"    => "carousel_type",
          "desc"  => "Do you want to display posts from a category OR posts having a specific tag?",
          "options" => array('Category', 'Tag'),
          "std"   => "Category",
          "type"  => "select"),

	array("name"  => "Category",
          "desc"  => "If you have selected CATEGORY above, then choose the category for the posts.<br />Hold CTRL to select multiple categories.",
          "id"    => "carousel_category",
          "type"  => "select-category-multi"),

	array("name"  => "Tag",
          "desc"  => "If you have selected TAG above, then choose the tag for the posts.<br />Hold CTRL to select multiple tags.",
          "id"    => "carousel_tag",
          "type"  => "select-tag-multi"),

),


"id5" => array(
	array("type"  => "preheader",
          "name"  => "Colors"),

	array("name"  => "Main Text Color",
		   "id"   => "text_css_color",
           "type" => "color",
           "selector" => "body",
           "attr" => "color"),

	array("name"  => "Link Color",
		   "id"   => "a_css_color",
           "type" => "color",
           "selector" => "a",
           "attr" => "color"),

	array("name"  => "Link Hover Color",
		   "id"   => "ahover_css_color",
           "type" => "color",
           "selector" => "a:hover",
           "attr" => "color"),

	array("name"  => "Featured Category: Active Post Background",
		   "id"   => "active_post_css_color",
           "type" => "color",
           "selector" => ".category-widget .tabs li.ui-tabs-active",
           "attr" => "background"),

	array("name"  => "Widget Title Color",
		   "id"   => "widget_css_color",
           "type" => "color",
           "selector" => ".widget h3.title ",
           "attr" => "color"),

     array("type"  => "preheader",
          "name"  => "Fonts"),

    array("name" => "General Text Font Style", 
          "id" => "typo_body", 
          "type" => "typography", 
          "selector" => "body" ),

    array("name" => "Logo Text Style", 
          "id" => "typo_logo", 
          "type" => "typography", 
          "selector" => "#logo h1 a" ),

    array("name" => "Slider Post Title Style", 
          "id" => "typo_slider", 
          "type" => "typography", 
          "selector" => "#slider #slides h2 a" ),

    array("name" => "Featured Caregories Title Style", 
          "id" => "typo_featcat", 
          "type" => "typography", 
          "selector" => ".category-widget h2.title a" ),

    array("name" => "Video Slider Post Title Style", 
          "id" => "typo_videoslider", 
          "type" => "typography", 
          "selector" => ".video_slider #panes h4 a" ),

    array("name"  => "Post Title Style",
           "id"   => "typo_post_title",
           "type" => "typography",
           "selector" => ".recent-post h2 a"),

    array("name"  => "Individual Post Title Style",
           "id"   => "typo_individual_title",
           "type" => "typography",
           "selector" => ".single h1.title a"),
 
     array("name"  => "Widget Title Style",
           "id"   => "typo_widget",
           "type" => "typography",
           "selector" => ".widget h3.title"),
 ),

"id7" => array(
    array("type"  => "preheader",
          "name"  => "Header Ad"),

    array("name"  => "Enable ad space in the header?",
          "id"    => "ad_head_select",
          "std"   => "off",
          "type"  => "checkbox"),

    array("name"  => "HTML Code (Adsense)",
          "desc"  => "Enter complete HTML code for your banner (or Adsense code) or upload an image below.",
          "id"    => "ad_head_code",
          "std"   => "",
          "type"  => "textarea"),

    array("name"  => "Upload your image",
          "desc"  => "Upload a banner image or enter the URL of an existing image.<br/>Recommended size: <strong>468 × 60px</strong>",
          "id"    => "banner_top",
          "std"   => "",
          "type"  => "upload"),

    array("name"  => "Destination URL",
          "desc"  => "Enter the URL where this banner ad points to.",
          "id"    => "banner_top_url",
          "type"  => "text"),

    array("name"  => "Banner Title",
          "desc"  => "Enter the title for this banner which will be used for ALT tag.",
          "id"    => "banner_top_alt",
          "type"  => "text"),


	array("type"  => "preheader",
          "name"  => "Sidebar Ad"),

	array("name"  => "Enable ad space in sidebar?",
          "id"    => "banner_sidebar_enable",
          "std"   => "off",
          "type"  => "checkbox"),

	array("name"  => "Ad Position",
          "desc"  => "Do you want to place the banner before the widgets or after the widgets?",
          "id"    => "banner_sidebar_position",
          "options" => array('Before widgets', 'After widgets'),
          "std"   => "Before widgets",
          "type"  => "select"),

    array("name"  => "HTML Code (Adsense)",
          "desc"  => "Enter complete HTML code for your banner (or Adsense code) or upload an image below.",
          "id"    => "banner_sidebar_html",
          "std"   => "",
          "type"  => "textarea"),

	array("name"  => "Upload your image",
          "desc"  => "Upload a banner image or enter the URL of an existing image.<br/>Recommended size: <strong>230 × 125px</strong>",
          "id"    => "banner_sidebar",
          "std"   => "",
          "type"  => "upload"),

	array("name"  => "Destination URL",
          "desc"  => "Enter the URL where this banner ad points to.",
          "id"    => "banner_sidebar_url",
          "type"  => "text"),

	array("name"  => "Banner Title",
          "desc"  => "Enter the title for this banner which will be used for ALT tag.",
          "id"    => "banner_sidebar_alt",
          "type"  => "text"),

)


/* end return */);