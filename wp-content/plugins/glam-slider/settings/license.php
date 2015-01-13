<?php
function glam_slider_license(){
$glam_license_key=get_option('glam_license_key');
?>
<div class="wrap" style="clear:both;">
<h2><?php _e('License','glam-slider'); echo $curr; ?> </h2>
<form method="post" action="options.php" id="glam_slider_form"> <?php settings_fields('glam-slider-license-info'); ?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php _e('License Key','glam-slider'); ?></th>
			<td><input type="text" name="glam_license_key" id="glam_license_key" class="regular-text code" value="<?php echo $glam_license_key; ?>" />
				<div>
					<?php _e('Enter the License Key which you would have received on ','glam-slider');
					echo '<a href="http://support.slidervilla.com/my-downloads/" target="_blank">';_e('My Downloads Area','glam-slider');echo '</a>';?>
				</div>
			</td>
		</tr>
	</table>
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
</form>	
</div>
<?php
}
function glam_license_notice() {  
$glam_license_key=get_option('glam_license_key');
	if ( isset($_GET['page']) && ('glam-slider-admin' == $_GET['page'] or 'glam-slider-settings' == $_GET['page']) && empty($glam_license_key) ){
	?>
		<div class="error">
			<p><?php _e( 'Enter the License Key for Glam Slider on ', 'glam-slider' ); echo '<a href="'.glam_sslider_admin_url( array( 'page' => 'glam-slider-license-key' ) ).'">';_e('this page','glam-slider');echo '</a>';?></p>
		</div>
	<?php
	}
}
add_action( 'admin_notices', 'glam_license_notice' );
?>