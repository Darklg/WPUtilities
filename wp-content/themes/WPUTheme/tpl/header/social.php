<?php
$social_twitter_url = trim( get_option( 'social_twitter_url' ) );
$social_facebook_url = trim( get_option( 'social_facebook_url' ) );
$social_instagram_url = trim( get_option( 'social_instagram_url' ) );
echo '<ul class="header__social">';
if ( !empty( $social_twitter_url ) ) {
    echo '<li><a href="'.$social_twitter_url.'" class="twitter" target="_blank">Twitter</a></li>';
}
if ( !empty( $social_facebook_url ) ) {
    echo '<li><a href="'.$social_facebook_url.'" class="facebook" target="_blank">Facebook</a></li>';
}
if ( !empty( $social_instagram_url ) ) {
    echo '<li><a href="'.$social_instagram_url.'" class="instagram" target="_blank">Instagram</a></li>';
}
echo '</ul>';
