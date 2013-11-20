<?php
$previous_post_link = trim( get_previous_post_link( '&laquo; %link' ) );
$next_post_link = trim( get_next_post_link( '%link &raquo;' ) );

$hasALink = ( !empty( $previous_post_link ) || !empty( $next_post_link ) );
$hasTwoLinks = ( !empty( $previous_post_link ) && !empty( $next_post_link ) );

if ( $hasALink ) {
    echo '<nav class="main-pagination"><p>';
    echo $previous_post_link;
    if ( $hasTwoLinks ) {
        echo ' | ';
    }
    echo $next_post_link;
    echo '</p></nav>';
}
