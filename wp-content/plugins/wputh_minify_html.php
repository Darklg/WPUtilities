<?php

/*
Plugin Name: WP Utilities Minify HTML
Description: Minify HTML
Version: 0.2
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUMinifyHTML
{
    private $filter_tags = array(
        'code',
        'kbd',
        'pre',
        'samp',
        'script',
        'style',
    );
    function __construct() {
        add_action('template_redirect', array(&$this,
            'set_ob_action'
        ));
    }

    function set_ob_action() {
        ob_start(array(&$this,
            'minify_html'
        ));
    }

    function minify_html($html) {

        $noncompressedtags = array();
        $noncompressedtagsi = 0;

        // Isolate some tags
        foreach ($this->filter_tags as $tag) {
            preg_match_all('/<' . $tag . '(.*)<\/' . $tag . '>/isU', $html, $matches);
            foreach ($matches[0] as $match) {
                $tag_match = array(
                    $match,
                    '<______tag_' . $noncompressedtagsi . '____/>'
                );
                $noncompressedtags[] = $tag_match;
                $noncompressedtagsi++;
                $html = str_replace($match, $tag_match[1], $html);
            }
        }

        // Removing multiple spaces
        $html = preg_replace('/(\s{2,})/', ' ', $html);

        // Removing spaces between tags
        $html = preg_replace('/>(\s+)</', '><', $html);

        // Replace isolated tags
        foreach ($noncompressedtags as $item) {
            $html = str_replace($item[1], $item[0], $html);
        }

        return $html;
    }
}

if (!WP_DEBUG) {
    $WPUMinifyHTML = new WPUMinifyHTML();
}

