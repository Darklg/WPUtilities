<?php
global $wp_query, $wputh_query, $paged;

$show_pages_number = true;

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
    'total' => $pagi_query->max_num_pages
);


// Hiding pagination if not enough pages
if ( $pagi_query->max_num_pages == 1 ) {
    $display_pagination = false;
}

if ( $display_pagination ) { ?>
<nav class="main-pagination">
    <p><?php
    if ( $show_pages_number ) {
        echo paginate_links($paginate_args);
    }
    else {
        posts_nav_link();
    }
    ?></p>
</nav>
<?php }

unset( $pagi_query );
