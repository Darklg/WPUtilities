<?php

$social_links = array(
    'twitter'   => 'Twitter',
    'facebook'  => 'Facebook',
    'instagram' => 'Instagram',
);

if(isset($social_links)) {
  foreach ( $social_links as $id => $name ) {
      $social_link = trim( get_option( 'social_'.$id.'_url' ) );
      if ( !empty( $social_link ) ) {
          $social_output[] = '<a href="'.$social_link.'" rel="me" class="'.$id.'" target="_blank" title="'. sprintf( __("%s : Follow %s (open in new window)", 'wputh'), $name, get_bloginfo('name')) .'">'.$name.'</a>';
      }
  }

  if(isset($social_output) && is_array($social_output)) {
      echo '<ul class="header__social">';
      echo '<li>'. implode('</li><li>', $social_output) . '</li>';
      echo '</ul>';
  }
}
