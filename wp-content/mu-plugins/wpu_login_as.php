<?php

add_action('wp_loaded', 'wpuloginas_redirecttouser');
function wpuloginas_redirecttouser() {
    /* Check authorizations */
    if (!is_admin() || !is_user_logged_in() || !current_user_can('remove_users')) {
        return false;
    }

    /* Check correct value */
    if (!isset($_GET['loginas']) || !is_numeric($_GET['loginas'])) {
        return false;
    }

    /* Check nonce */
    if (!isset($_GET['wpuloginas']) || !wp_verify_nonce($_GET['wpuloginas'], 'redirecttouser')) {
        return false;
    }

    /* Login as user */
    wp_set_current_user($_GET['loginas']);
    wp_set_auth_cookie($_GET['loginas']);

    /* Redirect to admin home */
    wp_redirect('/wp-admin/');
    die;
}


add_action('show_user_profile', 'wpuloginas_displaybutton');
add_action('edit_user_profile', 'wpuloginas_displaybutton');
function wpuloginas_displaybutton($user) {
    $user_id = $user->ID;
    if (!current_user_can('remove_users') || $user_id == get_current_user_id()) {
        return false;
    }

    $_redirectUrl = wp_nonce_url(admin_url('index.php?loginas=' . $user_id), 'redirecttouser', 'wpuloginas');
    echo '<a href="' . $_redirectUrl . '" class="button">' . sprintf(__('Login as %s'), esc_attr(get_user_meta($user_id, 'nickname', 1))) . '</a>';
}
