<?php
global $wp;
$display_languages = array();
$current_lang = '';

/* Thanks to http://kovshenin.com/2012/current-url-in-wordpress/ */
$current_url = add_query_arg($wp->query_string, '', home_url($wp->request));

// Obtaining from Qtranslate
if (function_exists('qtrans_getSortedLanguages')) {
    $current_lang = qtrans_getLanguage();
    $languages = qtrans_getSortedLanguages();
    foreach ($languages as $lang) {
        $display_languages[$lang] = array(
            'name' => $lang,
            'url' => qtrans_convertURL($current_url, $lang)
        );
    }
}

// Obtaining from Polylang
if (function_exists('pll_current_language')) {
    global $polylang;
    $current_lang = pll_current_language();
    $poly_langs = pll_the_languages(array(
        'raw' => 1,
        'echo' => 0
    ));

    foreach ($poly_langs as $lang) {
        $display_languages[$lang['slug']] = array(
            'name' => $lang['slug'],
            'url' => $lang['url'],
        );
    }
}

if (!empty($display_languages)) {
    echo '<div class="languages">';
    foreach ($display_languages as $lang) {
        echo '<a hreflang="' . $lang['name'] . '" ' . ($lang['name'] == $current_lang ? 'class="current"' : '') . ' href="' . $lang['url'] . '">' . $lang['name'] . '</a>';
    }
    echo '</div>';
}
