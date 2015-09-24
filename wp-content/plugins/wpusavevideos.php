<?php

/*
Plugin Name: WPU Save Videos
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Save Videos thumbnails.
Version: 0.2
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUSaveVideos {

    private $hosts = array(
        'youtube' => array(
            'youtube.com',
            'www.youtube.com'
        ) ,
        'vimeo' => array(
            'vimeo.com',
            'www.vimeo.com'
        )
    );

    private $no_save_posttypes = array(
        'revision',
        'attachment'
    );

    function __construct() {
        add_action('save_post', array(&$this,
            'save_post'
        ) , 10, 3);
    }

    function save_post($post_id, $post) {
        if (!is_object($post)) {
            return;
        }

        if (!is_numeric($post_id)) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }

        if (in_array($post->post_type, $this->no_save_posttypes)) {
            return;
        }

        /* Get current video list */
        $videos = unserialize(get_post_meta($post_id, 'wpusavevideos_videos', 1));

        if (!is_array($videos)) {
            $videos = array();
        }

        /* Add new videos  */
        $new_videos = $this->extract_videos_from_text($post->post_content);

        foreach ($new_videos as $id => $new_video) {
            if (!array_key_exists($id, $videos)) {
                $new_video['thumbnail'] = $this->retrieve_thumbnail($new_video['url'], $post_id);
                if ($new_video['thumbnail'] !== false) {
                    $videos[$id] = $new_video;
                }
            }
        }

        /* Save video list */
        update_post_meta($post_id, 'wpusavevideos_videos', serialize($videos));
    }

    function extract_videos_from_text($text) {

        $hosts = array();
        foreach ($this->hosts as $new_hosts) {
            $hosts = array_merge($hosts, $new_hosts);
        }

        $videos = array();
        $urls = wp_extract_urls($text);
        foreach ($urls as $url) {

            // Get URL Key
            $url_key = md5($url);
            $url_parsed = parse_url($url);

            // No valid host
            if (!isset($url_parsed['host'])) {
                continue;
            }

            // Test host
            if (in_array($url_parsed['host'], $hosts)) {
                $videos[$url_key] = array(
                    'url' => $url
                );
            }
        }

        return $videos;
    }

    function retrieve_thumbnail($video_url, $post_id) {

        $thumbnail_url = $this->retrieve_thumbnail_url($video_url);

        if (!empty($thumbnail_url)) {
            return $this->media_sideload_image($thumbnail_url, $post_id);
        }

        return false;
    }

    function retrieve_thumbnail_url($video_url) {

        $url_parsed = parse_url($video_url);

        if (!isset($url_parsed['host'])) {
            return '';
        }

        // Extract for youtube
        if (in_array($url_parsed['host'], $this->hosts['youtube']) && isset($url_parsed['query'])) {
            parse_str($url_parsed['query'], $query);
            if (isset($query['v'])) {
                return 'http://img.youtube.com/vi/' . $query['v'] . '/0.jpg';
            }
        }

        // Extract for vimeo
        if (in_array($url_parsed['host'], $this->hosts['vimeo'])) {
            $vimeo_url = explode('/', $video_url);
            $vimeo_id = false;
            foreach ($vimeo_url as $url_part) {
                if (is_numeric($url_part)) {
                    $vimeo_id = $url_part;
                }
            }

            $vimeo_details = array();
            if (is_numeric($vimeo_id)) {
                $vimeo_response = wp_remote_get("http://vimeo.com/api/v2/video/$vimeo_id.json");
                $vimeo_details = json_decode(wp_remote_retrieve_body($vimeo_response));
            }

            if (isset($vimeo_details[0], $vimeo_details[0]->thumbnail_large)) {
                return $vimeo_details[0]->thumbnail_large;
            }
        }

        return '';
    }

    function media_sideload_image($file, $post_id, $desc = '') {

        // Set variables for storage, fix file filename for query strings.
        preg_match('/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches);

        $tmp = download_url($file);
        if (is_wp_error($tmp)) {
            return false;
        }

        $file_array = array();
        $file_array['name'] = basename($matches[0]);

        // Download file to temp location.
        $file_array['tmp_name'] = $tmp;

        // If error storing temporarily, return an error.
        if (is_wp_error($file_array['tmp_name'])) {
            return false;
        }

        // Do the validation and storage stuff.
        $id = media_handle_sideload($file_array, $post_id, $desc);

        // If error storing permanently, unlink.
        if (is_wp_error($id)) {
            @unlink($file_array['tmp_name']);
        }

        return $id;
    }
}

$WPUSaveVideos = new WPUSaveVideos();
