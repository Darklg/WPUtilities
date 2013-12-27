<?php
global $wp_query, $wputh_query, $paged;

function wputh_paginate_links( $args = '' ) {
    $defaults = array(
        'base' => '%_%', // http://example.com/all_posts.php%_% : %_% is replaced by format (below)
        'format' => '?page=%#%', // ?page=%#% : %#% is replaced by the page number
        'total' => 1,
        'current' => 0,
        'show_all' => false,
        'prev_next' => true,
        'prev_text' => __('&laquo; Previous'),
        'next_text' => __('Next &raquo;'),
        'end_size' => 1,
        'mid_size' => 2,
        'type' => 'plain',
        'add_args' => false, // array of query args to add
        'add_fragment' => ''
    );

    $args = wp_parse_args( $args, $defaults );
    extract($args, EXTR_SKIP);

    // Who knows what else people pass in $args
    $total = (int) $total;
    if ( $total < 2 )
        return;
    $current  = (int) $current;
    $end_size = 0  < (int) $end_size ? (int) $end_size : 1; // Out of bounds?  Make it the default.
    $mid_size = 0 <= (int) $mid_size ? (int) $mid_size : 2;
    $add_args = is_array($add_args) ? $add_args : false;
    $r = '';
    $page_links = array();
    $n = 0;
    $dots = false;

    if ( $prev_next && $current && 1 < $current ) :
        $link = str_replace('%_%', 2 == $current ? '' : $format, $base);
        $link = str_replace('%#%', $current - 1, $link);
        if ( $add_args )
            $link = add_query_arg( $add_args, $link );
        $link .= $add_fragment;
        $page_links[] = '<a class="pagination__prev page-numbers prev" rel="prev" href="' . esc_url( apply_filters( 'wputh_paginate_links', $link ) ) . '" title="'.sprintf(__('Previous page (%1$s of %2$s)', 'wputh'), $current-1, $total).'">' . $prev_text . '</a>';
    endif;
    for ( $n = 1; $n <= $total; $n++ ) :
        $n_display = number_format_i18n($n);
        if ( $n == $current ) :
            $page_links[] = '<strong class="pagination__current page-numbers current">['.$n_display.']</strong>';
            $dots = true;
        else :
            if ( $show_all || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :
                $link = str_replace('%_%', 1 == $n ? '' : $format, $base);
                $link = str_replace('%#%', $n, $link);
                $seo_noindex = ($n == 1 ? '' : ' rel="noindex follow"');
                if ( $add_args )
                    $link = add_query_arg( $add_args, $link );
                $link .= $add_fragment;
                $page_links[] = '<a class="pagination__link page-numbers" '. $seo_noindex .' href="'. esc_url( apply_filters( 'wputh_paginate_links', $link ) ) .'" title="'.sprintf(__('Go to page %1$s of %2$s', 'wputh'), $n, $total).'">'. $n_display .'</a>';
                $dots = true;
            elseif ( $dots && !$show_all ) :
                $page_links[] = '<span class="pagination__dots page-numbers dots">'. __( '&hellip;' ) .'</span>';
                $dots = false;
            endif;
        endif;
    endfor;
    if ( $prev_next && $current && ( $current < $total || -1 == $total ) ) :
        $link = str_replace('%_%', $format, $base);
        $link = str_replace('%#%', $current + 1, $link);
        if ( $add_args )
            $link = add_query_arg( $add_args, $link );
        $link .= $add_fragment;
        $page_links[] = '<a class="pagination__next page-numbers next" rel="next" href="' . esc_url( apply_filters( 'wputh_paginate_links', $link ) ) . '" title="'.sprintf(__('Next page (%1$s of %2$s)', 'wputh'), $current+1, $total).'">' . $next_text . '</a>';
    endif;
    switch ( $type ) :
        case 'array' :
            return $page_links;
            break;
        case 'list' :
            $r .= '<ul class="list-inline list-separate pagination__list mvn"><li class="pagination__item">';
            $r .= join('</li><li class="pagination__item">', $page_links);
            $r .= '</li></ul>';
            break;
        default :
            $r = join("\n", $page_links);
            break;
    endswitch;
    return $r;
}

$next_page = '';

// Getting a real number for paged
$pagedd = max( 1, $paged );

// Getting the good query
$pagi_query = $wp_query;
if ( is_object( $wputh_query ) ) {
    $pagi_query = $wputh_query;
}

// Default parameters
$display_pagination = true;
// need an unlikely integer
$big = 999999999;
$paginate_args = array(
    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
    'format' => '?paged=%#%',
    'current' => $pagedd,
    'total' => $pagi_query->max_num_pages,
    'type' => 'list'
);

// load next page
if ( $pagedd < $pagi_query->max_num_pages ) {
    $next_page = '<a class="load-more" href="'.get_pagenum_link( $pagedd+1 ).'">'.__( 'Next page', 'wputh' ).'</a>';
}

// Hiding pagination if not enough pages
if ( $pagi_query->max_num_pages == 1 ) {
    $display_pagination = false;
}

if ( $display_pagination ) { ?>
<nav class="pagination noprint mvl center"><h2 class="h4 pagination__title hide"><?php echo sprintf(__('Page %1$s of %2$s', 'wputh'), $pagedd, $pagi_query->max_num_pages); ?></h2>

    <?php
    switch ( PAGINATION_KIND ) {
    case 'numbers':
        echo wputh_paginate_links( $paginate_args );
        break;
    case 'load-more':
        echo $next_page;
        break;
    default :
        posts_nav_link();
    }
    ?>
</nav>
<?php }

unset( $pagi_query );
