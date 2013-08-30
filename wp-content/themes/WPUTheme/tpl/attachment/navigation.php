<?php
// Display attachment navigation
if ($current_attachment != -1) {
    echo '<div>';
    echo '<a class="prev" href="'.get_attachment_link( $previous->ID ).'">'.$previous_content.'</a>';
    echo '<a class="next" href="'.get_attachment_link( $next->ID ).'">'.$next_content.'</a>';
    echo '</div>';
}