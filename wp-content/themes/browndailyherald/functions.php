<?php

require_once(get_template_directory() . DIRECTORY_SEPARATOR . 'functions.php');

function filter_uncat($cat) {
	preg_match_all('/uncategorized/is',$cat,$matches);
	return count($matches[0]) == 0;
}

function the_category_filter($thelist,$separator=' ') {  
    //if(!defined('WP_ADMIN')) {
        //Category Names to exclude
        $cats = array_filter(explode($separator,$thelist),'filter_uncat');
        $thelist = implode($separator,$cats);  
    //}
    return $thelist;
} 
add_filter('the_category','the_category_filter', -40, 2);
