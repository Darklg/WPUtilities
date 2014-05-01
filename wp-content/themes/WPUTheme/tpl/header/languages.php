<?php
if ( function_exists( 'qtrans_getSortedLanguages' ) ) {
    global $wp;
    /* Thanks to http://kovshenin.com/2012/current-url-in-wordpress/ */
    $current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
    $current_lang = qtrans_getLanguage();
    $languages = qtrans_getSortedLanguages();
    echo '<div class="languages">';
    foreach ( $languages as $lang ) {
        echo '<a '.( $lang == $current_lang ? 'class="current"':'' ).' href="'.qtrans_convertURL( $current_url, $lang ).'">'.$lang.'</a>';
    }
    echo '</div>';
}
