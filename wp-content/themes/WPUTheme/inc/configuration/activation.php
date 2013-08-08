<?php
/**
 * Configuration after Theme activation
 *
 * @package default
 */

// Options
$wputh_setup_options = array(
    'date_format' => 'j F Y',
    'permalink_structure' => '/%postname%/',
    'timezone_string' => 'Europe/Paris',
    'time_format' => 'H:i'
);

// Old way for activation hook
if ( is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" ) {
    // Setting options
    foreach ($wputh_setup_options as $name => $value) {
        update_option($name, $value);
    }
    // Updating permalinks
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}
