<?php
include dirname( __FILE__ ) . '/z-protect.php';
if(!isset($_POST['is_ajax'])){
?><!DOCTYPE HTML>
<!--[if lt IE 8 ]><html <?php language_attributes(); ?> class="is_ie7 lt_ie8 lt_ie9 lt_ie10"><![endif]-->
<!--[if IE 8 ]><html <?php language_attributes(); ?> class="is_ie8 lt_ie9 lt_ie10"><![endif]-->
<!--[if IE 9 ]><html <?php language_attributes(); ?> class="is_ie9 lt_ie10"><![endif]-->
<!--[if gt IE 9]><html <?php language_attributes(); ?> class="is_ie10"><![endif]-->
<!--[if !IE]><!--> <html <?php language_attributes(); ?>><!--<![endif]-->
<head><?php include get_template_directory() . '/tpl/header/head.php'; ?></head>
<body <?php body_class('cssc-is-responsive'); ?>>
<header class="main-header centered-container">
    <div>
    <?php include get_template_directory() . '/tpl/header/title.php'; ?>
    <?php include get_template_directory() . '/tpl/header/searchform.php'; ?>
    <?php wp_nav_menu( array(
        'depth' => 1,
        'theme_location' => 'main',
        'menu_class' => 'main-menu'
    ) ); ?>
    </div>
</header>
<div class="main-container centered-container"><div class="main-container--inner">
<?php } ?>
<?php include get_template_directory() . '/tpl/header/breadcrumbs.php'; ?>
<script>
var wputh_page_title = "<?php echo addslashes(trim(get_bloginfo('name') . ' ' . wp_title(' ', false))); ?>";
var wputh_pagination_kind = '<?php echo PAGINATION_KIND; ?>';
</script>