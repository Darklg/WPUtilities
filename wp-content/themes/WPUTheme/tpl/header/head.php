<?php
include dirname( __FILE__ ) . '/../../z-protect.php';
?><meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php wp_title(); ?></title>
<?php wp_head(); ?>
<meta name="viewport" content="width=device-width" />
<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicon.ico" />
<!--[if lt IE 9]>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/ie/html5.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/ie/selectivizr-min.js"></script>
<![endif]-->