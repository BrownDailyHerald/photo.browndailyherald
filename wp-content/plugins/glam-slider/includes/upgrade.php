<?php
class glam_update_class
{
    public $glam_slider_current_version;
    public $glam_slider_update_path;
    public $glam_slider_plugin_slug;
    public $glam_slider_slug;
    function __construct($glam_slider_current_version, $glam_slider_update_path, $glam_slider_plugin_slug)
    { 
        // Set the class public variables
        $this->current_version = $glam_slider_current_version;
        $this->update_path = $glam_slider_update_path;
        $this->plugin_slug = $glam_slider_plugin_slug;
        list ($t1, $t2) = explode('/', $glam_slider_plugin_slug);
        $this->slug = str_replace('.php', '', $t2);

        // define the alternative API for updating checking
        add_filter('pre_set_site_transient_update_plugins', array(&$this, 'glam_check_update'));

        // Define the alternative response for information checking
        add_filter('plugins_api', array(&$this, 'glam_check_info'), 10, 3);
    }

    public function glam_check_update($transient)
    { 
        if (empty($transient->checked)) {
            return $transient;
        }
		$glam_license_key=get_option('glam_license_key');
        $remote_version = $this->glam_getRemote_version();

        if (version_compare($this->current_version, $remote_version, '<')) {
            $obj = new stdClass();
            $obj->slug = $this->slug;
            $obj->new_version = $remote_version;
            $obj->url = $this->update_path;
			if(isset($glam_license_key) && !empty($glam_license_key))
				$obj->package = 'http://slidervilla.com/store/receipt/?duid='.$glam_license_key;
            $transient->response[$this->plugin_slug] = $obj;
        }
        return $transient;
    }

    public function glam_check_info($false, $action, $arg)
    { 
        if ($arg->slug === $this->slug or $arg->slug === $this->plugin_slug) {
            $glam_license_key=get_option('glam_license_key');
			$information = $this->glam_getRemote_information();
			if(isset($glam_license_key) && !empty($glam_license_key)){
				$information = (array)$information;
				$information['download_link']='http://slidervilla.com/store/receipt/?duid='.$glam_license_key;
				$information=(object)$information;
			}
			return $information;
        }
        return $false;
    }

    public function glam_getRemote_version()
    {
        $request = wp_remote_post($this->update_path, array('body' => array('action' => 'version')));
        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            return $request['body'];
        }
        return false;
    }

    public function glam_getRemote_information()
    {
        $request = wp_remote_post($this->update_path, array('body' => array('action' => 'info')));
        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            return unserialize($request['body']);
        }
        return false;
    }

    public function glam_getRemote_license()
    {
        $request = wp_remote_post($this->update_path, array('body' => array('action' => 'license')));
        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            return $request['body'];
        }
        return false;
    }
}