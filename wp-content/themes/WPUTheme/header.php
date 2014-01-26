<?php
include dirname( __FILE__ ) . '/z-protect.php';
if ( !isset( $_GET['is_ajax'] ) ) {
?><!DOCTYPE HTML>
<!--[if lt IE 8 ]><html <?php language_attributes(); ?> class="is_ie7 lt_ie8 lt_ie9 lt_ie10"><![endif]-->
<!--[if IE 8 ]><html <?php language_attributes(); ?> class="is_ie8 lt_ie9 lt_ie10"><![endif]-->
<!--[if IE 9 ]><html <?php language_attributes(); ?> class="is_ie9 lt_ie10"><![endif]-->
<!--[if gt IE 9]><html <?php language_attributes(); ?> class="is_ie10"><![endif]-->
<!--[if !IE]><!--> <html <?php language_attributes(); ?>><!--<![endif]-->
<head><?php include get_template_directory() . '/tpl/header/head.php'; ?></head>
<body <?php body_class( 'no-js cssc-is-responsive' ); ?>>
<div class="main-header centered-container">
    <header class="banner" role="banner" id="banner">
    <?php
    /* Title */
    $main_tag = is_home() ? 'h1' : 'div';
    echo '<'.$main_tag.' class="h1 main-title">';
    echo '<a href="' . site_url() . '">'.get_bloginfo( 'name' ).'</a>';
    echo '</'.$main_tag.'>';
    /* Search form */
    include get_template_directory() . '/tpl/header/searchform.php';
    /* Social links */
    include get_template_directory() . '/tpl/header/social.php';
    /* Main menu */
    wp_nav_menu( array(
        'depth' => 1,
        'theme_location' => 'main',
        'menu_class' => 'main-menu'
    ) );
    ?>
    </header>
</div>
<div class="main-container centered-container"><div class="main-container--inner" id="content">
<?php }
include get_template_directory() . '/tpl/header/breadcrumbs.php';
include get_template_directory() . '/tpl/header/jsvalues.php';
