<?php

/*
Plugin Name: WPU Cache External
Description: Cache External URLs
Version: 0.1.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUCacheExternal {

    private $cache_dir_set = false;

    private $config = array(
        'expires' => 86400,
        'base_cache_url' => '',
        'base_cache_dir' => '',
        'cache_folder' => 'wpucacheexternal'
    );

    public function __construct() {
        add_action('init', array(&$this,
            'set_cache_dir'
        ));
    }

    private function set_cache_dir() {
        $wp_upload_dir = wp_upload_dir();
        $this->config['base_cache_dir'] = $wp_upload_dir['basedir'] . '/' . $this->config['cache_folder'] . '/';
        $this->config['base_cache_url'] = $wp_upload_dir['baseurl'] . '/' . $this->config['cache_folder'] . '/';
        if (!is_dir($this->config['base_cache_dir'])) {
            mkdir($this->config['base_cache_dir']);
        }
    }

    public function get_url($url, $args = array()) {
        $return_url = '';

        if (!$this->cache_dir_set) {
            $this->set_cache_dir();
        }

        if (!is_array($args)) {
            $args = array();
        }

        /* Get Extension */
        $extension = '';
        $url_details = parse_url($url);
        if (isset($url_details['path']) && !isset($args['extension'])) {
            $ext = pathinfo($url_details['path'], PATHINFO_EXTENSION);
            if (!empty($ext)) {
                $extension = '.' . $ext;
            }
        }
        if (isset($args['extension'])) {
            $extension = '.' . $args['extension'];
        }

        /* Build cache file name */
        $cache_id = md5($url . serialize($args)) . $extension;
        $cache_url = $this->config['base_cache_url'] . $cache_id;
        $cache_file = $this->config['base_cache_dir'] . $cache_id;

        /* Check cache expiration time */
        $expired_time = time() - $this->config['expires'];
        if (isset($args['expires']) && is_numeric($args['expires'])) {
            $expired_time = time() - $args['expires'];
        }

        $cache_ok = true;
        if (!file_exists($cache_file) || filemtime($cache_file) < $expired_time) {
            $cache_ok = $this->cache_url_in_file($url, $cache_file);
        }

        /* Return cached URL if available */
        if ($cache_ok && file_exists($cache_file)) {
            return $cache_url;
        }

        /* Something has gone wrong : return original URL */
        return $url;

    }

    private function cache_url_in_file($url, $cache_file) {
        $result = '';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_exec($ch);
        if (!$result = curl_exec($ch)) {
            return false;
        }
        curl_close($ch);
        if (!file_put_contents($cache_file, $result)) {
            return false;
        }
        return true;
    }

}

$WPUCacheExternal = new WPUCacheExternal();

/*
// Cache URL for one hour
$url = $WPUCacheExternal->get_url('http://placehold.it/100x100', array(
    'extension' => 'png',
    'expires' => 3600
));
*/
