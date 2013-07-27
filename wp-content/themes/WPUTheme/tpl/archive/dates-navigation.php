<?php

// Allowing results to be refreshed.
if (isset($_GET['reload']) && current_user_can('moderate_comments')) {
    delete_transient('wputh_archive_posts_years_months');
}

// Obtaining a list of all months with at least a post
if ( false === ( $results = get_transient( 'wputh_archive_posts_years_months' ) ) ) {
    global $wpdb;
    $results = $wpdb->get_results(
        "SELECT DISTINCT CONCAT(YEAR(post_date_gmt),'-',MONTH(post_date_gmt)) as post_year_month
        FROM ".$wpdb->posts."
        WHERE 1=1
        AND post_status='publish'
        AND post_type='post'
        ORDER BY post_date DESC"
    );
    set_transient( 'wputh_archive_posts_years_months', $results, 1 * HOUR_IN_SECONDS );
}

$years = array();
$current_year = get_the_time('Y');
$current_month = get_the_time('n');

// Sorting results to obtain a list of years and a list of months for the current year
foreach ($results as $result) {
    $details_date = explode('-', $result->post_year_month);
    if (ctype_digit($details_date[1])) {
        $year = $details_date[0];
        $month = $details_date[1];
        if (!isset($years[$year])) {
            $years[$year] = array();
        }
        $years[$year][$month] = date_i18n("F", mktime(0, 0, 0, $month, 1, 2011));
    }
}

// Years
echo '<ul class="archive-years">';
foreach ($years as $year => $months) {
    echo '<li class="archive-years--year"><strong class="archive-years--title"><a href="'.get_year_link($year).'">'.$year.'</a></strong><ul class="archive-months">';
    ksort($months);
    foreach ($months as $num => $month) {
        echo '<li class="'.($year == $current_year && $num == $current_month ? 'current':'').'"><a href="'.get_month_link($year, $num).'">'.$month.'</a></li>';
    }
    echo '</ul></li>';
}
echo '</ul>';
