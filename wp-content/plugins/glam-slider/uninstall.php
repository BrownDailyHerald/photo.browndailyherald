<?php 
//This plugin creates an entry in the options database. When the plugin will be deleted, this code will automatically delete the database entry from the options Wordpress table.
$scounter=get_option('glam_slider_scounter');
for($i=1;$i<=$scounter;$i++){
	if ($i==1){
		delete_option('glam_slider_options');
	}
	else{
		delete_option('glam_slider_options'.$i);
	}
} 
delete_option('glam_slider_scounter');
delete_option('glam_db_version');
delete_option('glam_license_key');
//This plugin creates its own database tables to save the post ids for the posts and pages added to Glam Slider. When the plugin will be deleted, the database tables will also get deleted.
global $wpdb, $table_prefix;

$slider_table = $table_prefix.'glam_slider';
$slider_meta = $table_prefix.'glam_slider_meta';
$slider_postmeta = $table_prefix.'glam_slider_postmeta';
$sql = "DROP TABLE $slider_table;";
$wpdb->query($sql);
$sql = "DROP TABLE $slider_meta;";
$wpdb->query($sql);
$sql = "DROP TABLE $slider_postmeta;";
$wpdb->query($sql);
?>
