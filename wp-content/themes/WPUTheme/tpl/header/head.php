<?php
include dirname( __FILE__ ) . '/../../z-protect.php';
?><meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>
<?php include get_template_directory() . '/tpl/header/head/metas.php'; ?>
<?php wp_head(); ?>
<meta name="viewport" content="width=device-width" />
<!--[if lt IE 9]>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/ie/html5.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/ie/selectivizr-min.js"></script>
<![endif]-->