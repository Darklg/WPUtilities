<?php
echo '<ul>';
if (is_user_logged_in()) {
    echo '<li><a href="'. wp_logout_url(site_url()).'">'.__('Logout', 'wputh').'</a></li>';
} else {
    echo '<li class="wdrw">';
    echo '<a href="#">'.__('Log in', 'wputh').'</a>';
    echo '<div class="submenu">'.wp_login_form(array(
            'echo' => false
        )).'</div>';
    echo '</li>';
}
echo '</ul>';
