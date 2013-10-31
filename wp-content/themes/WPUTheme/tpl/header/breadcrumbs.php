<?php
/**
 * Breadcrumbs
 *
 * @package default
 */


if ( !isset( $elements_ariane ) ) {
    // Hide breadcrumbs if called on homepage
    if ( is_home() ) {
        return;
    }

    $elements_ariane = array();
    $elements_ariane['home'] = array(
        'name' => __( 'Home', 'wputh' ),
        'link' => site_url()
    );

    if ( is_singular() ) {
        $main_category = get_the_category();
        if ( isset( $main_category[0] ) ) {
            $elements_ariane['category'] = array(
                'name' => $main_category[0]->cat_name,
                'link' => get_category_link( $main_category[0]->term_id )
            );
        }
    }

    if ( is_category() ) {
        $category = get_categories( array( 'include' => $cat ) );
        if ( isset( $category[0] ) ) {
            // Checking for parent categories
            $cat_tmp = $category[0]->parent;
            $parents_categories = array();
            while ( $cat_tmp != 0 ) {
                $category_parent = get_categories( array( 'include' => $cat_tmp ) );
                if ( isset( $category_parent[0] ) ) {
                    $parents_categories['parent-category-'.$cat_tmp] = array(
                        'name' => $category_parent[0]->name,
                        'link' => get_category_link( $category_parent[0]->term_id )
                    );
                    $cat_tmp = $category_parent[0]->parent;
                }
                else {
                    $cat_tmp = 0;
                }
            }
            // Reordering & merging parents
            if ( !empty( $parents_categories ) ) {
                arsort( $parents_categories );
                $elements_ariane = array_merge( $elements_ariane, $parents_categories );
            }
            // Adding category
            $elements_ariane['category'] = array(
                'name' => $category[0]->name,
                'last' => 1
            );
        }
    }

    if ( is_singular() || is_page() ) {
        $elements_ariane['single-page'] = array(
            'name' => get_the_title(),
            'last' => 1
        );
    }

    if ( is_404() ) {
        $elements_ariane['404-error'] = array(
            'name' => __( '404 Error', 'wputh' ),
            'last' => 1
        );
    }

    if ( is_search() ) {
        $elements_ariane['search-results'] = array(
            'name' => sprintf( __( 'Search results for "%s"', 'wputh' ),  get_search_query() ),
            'last' => 1
        );
    }
}

if ( !empty( $elements_ariane ) ) {
    echo '<div class="breadcrumbs">';
    foreach ( $elements_ariane as $id => $element ) {
        $last = ( isset( $element['last'] ) && $element['last'] == 1 );
        $className = 'element-ariane element-ariane--'.$id.' '.( $last ? 'is-last' : '' );
        if ( isset( $element['link'] ) ) {
            echo '<a href="'.$element['link'].'" class="'.$className.'">'.$element['name'].'</a>';
        }
        else {
            echo '<strong class="'.$className.'">'.$element['name'].'</strong>';
        }
        if ( !$last ) {
            echo ' <span class="ariane-sep">/</span> ';
        }
    }
    echo '</div>';
}
