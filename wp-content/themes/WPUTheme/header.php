<?php
include dirname( __FILE__ ) . '/z-protect.php';
$menu_args = array(
    'depth' => 1,
    'theme_location' => 'main',
    'menu_class' => 'main-menu'
);
if(!isset($_POST['ajax'])){
?><!DOCTYPE HTML>
<!--[if lt IE 8 ]><html <?php language_attributes(); ?> class="is_ie7 lt_ie8 lt_ie9 lt_ie10"><![endif]-->
<!--[if IE 8 ]><html <?php language_attributes(); ?> class="is_ie8 lt_ie9 lt_ie10"><![endif]-->
<!--[if IE 9 ]><html <?php language_attributes(); ?> class="is_ie9 lt_ie10"><![endif]-->
<!--[if gt IE 9]><html <?php language_attributes(); ?> class="is_ie10"><![endif]-->
<!--[if !IE]><!--> <html <?php language_attributes(); ?>><!--<![endif]-->
<head><?php include TEMPLATEPATH . '/tpl/header/head.php'; ?></head>
<body <?php body_class(); ?>>
<header class="main-header">
    <?php include TEMPLATEPATH . '/tpl/header/title.php'; ?>
    <?php include TEMPLATEPATH . '/tpl/header/searchform.php'; ?>
    <?php wp_nav_menu( $menu_args ); ?>
</header>
<div class="main-container">
<?php } else { ?>
<script>var page_title = '<?php echo addslashes(trim(wp_title('', false))); ?>';</script>
<?php }