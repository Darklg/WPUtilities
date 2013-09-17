<?php
/**
 * Breadcrumbs
 *
 * @package default
 */


if (!isset($elements_ariane)) {
    // Hide breadcrumbs if called on homepage
    if (is_home()) {
        return;
    }

    $elements_ariane = array();
    $elements_ariane['home'] = array(
        'name' => __('Home', 'wputh'),
        'link' => site_url()
    );

    if (is_singular()) {
        $main_category = $category = get_the_category();
        if (isset($main_category[0])) {
            $elements_ariane['category'] = array(
                'name' => $main_category[0]->cat_name,
                'link' => get_category_link($main_category[0]->term_id)
            );
        }
    }

    if (is_singular() || is_page()) {
        $elements_ariane['single-page'] = array(
            'name' => get_the_title(),
            'last' => 1
        );
    }
}

if (!empty($elements_ariane)) {
    echo '<div class="breadcrumbs">';
    foreach ($elements_ariane as $id => $element) {
        $last = (isset($element['last']) && $element['last'] == 1);
        $className = 'element-ariane element-ariane--'.$id.' '.($last ? 'is-last' : '');
        if (isset($element['link'])) {
            echo '<a href="'.$element['link'].'" class="'.$className.'">'.$element['name'].'</a>';
        }
        else {
            echo '<strong class="'.$className.'">'.$element['name'].'</strong>';
        }
        if (!$last) {
            echo ' <span class="ariane-sep">/</span> ';
        }
    }
    echo '</div>';
}
