<?php

/*
Plugin Name: WPU Override Translations
Description: Override core translations
Version: 0.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUOverrideTranslations {

    private $translations;

    public function __construct() {
        add_action('plugins_loaded', array(&$this, 'plugins_loaded'), 10);
    }

    /* ----------------------------------------------------------
      Start function
    ---------------------------------------------------------- */

    public function plugins_loaded() {
        $this->translations = apply_filters('wpuoverridetranslations__list', array());

        /* Add filter only if translations are available */
        if (!empty($this->translations)) {
            add_filter('gettext', array(&$this,
                'filter_gettext'
            ), 10, 3);
            add_filter('gettext_with_context', array(&$this,
                'filter_gettext_with_context'
            ), 10, 4);
        }
    }

    /* ----------------------------------------------------------
      Filters
    ---------------------------------------------------------- */

    public function filter_gettext($translated, $original, $domain) {
        foreach ($this->translations as $trans) {
            if ($trans[0] === $translated) {
                return $trans[1];
            }
        }

        return $translated;
    }

    public function filter_gettext_with_context($translated, $original, $context, $domain) {
        foreach ($this->translations as $trans) {
            if ($trans[0] === $translated) {
                return $trans[1];
            }
        }

        return $translated;
    }
}

$WPUOverrideTranslations = new WPUOverrideTranslations();
