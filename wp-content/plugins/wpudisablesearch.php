<?php
/*
Plugin Name: WPU disable search
Plugin URI: http://github.com/Darklg/WPUtilities
Description: Disable search
Version: 0.2
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
Thanks: http://www.geekpress.fr/?p=722
*/

function wpu_disable_search__in_query($query, $error = true) {
    if (!is_search()) {
        return;
    }
    $query->is_search = false;
    $query->query_vars['s'] = false;
    $query->query['s'] = false;
    // to error
    if ($error) {
        $query->is_404 = true;
    }
}

function wpu_disable_search__get_search_form($a) {
    return null;
}

add_action('init', 'wpu_disable_search__init');
function wpu_disable_search__init() {
    if (is_admin()) {
        return;
    }
    add_action('parse_query', 'wpu_disable_search__in_query');
    add_filter('get_search_form', 'wpu_disable_search__get_search_form');
}
