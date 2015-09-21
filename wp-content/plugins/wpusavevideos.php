<?php

/*
Plugin Name: WPU Save videos
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Save videos images
Version: 0.1
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
        )
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
                $videos[$id] = $new_video;
            }
        }

        /* Save video list */
        update_post_meta($post_id, 'wpusavevideos_videos', serialize($videos));
    }

    function extract_videos_from_text($text) {

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

            // Test youtube
            if (in_array($url_parsed['host'], $this->hosts['youtube'])) {
                $videos[$url_key] = array(
                    'url' => $url
                );
            }
        }
        return $videos;
    }

    function retrieve_thumbnail($video_url, $post_id) {
        $url_parsed = parse_url($video_url);

        // Test youtube
        if (isset($url_parsed['host']) && in_array($url_parsed['host'], $this->hosts['youtube']) && isset($url_parsed['query'])) {
            parse_str($url_parsed['query'], $query);
            if (isset($query['v'])) {
                $image_url = 'http://img.youtube.com/vi/' . $query['v'] . '/0.jpg';
                return $this->media_sideload_image($image_url, $post_id);
            }
        }

        return false;
    }

    function media_sideload_image($file, $post_id, $desc = '') {

        // Set variables for storage, fix file filename for query strings.
        preg_match('/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches);
        $file_array = array();
        $file_array['name'] = basename($matches[0]);

        // Download file to temp location.
        $file_array['tmp_name'] = download_url($file);

        // If error storing temporarily, return the error.
        if (is_wp_error($file_array['tmp_name'])) {
            return $file_array['tmp_name'];
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
