<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="UTF-8" />
    <title><?php bloginfo( 'name' ); ?></title>
</head>
<body>
    <h1><?php bloginfo( 'name' ); ?></h1>
    <p><?php echo sprintf( __( '%s is in maintenance mode.', 'wpumaintenance' ), '<strong>' . get_bloginfo( 'name' ) . '</strong>' ); ?></p>
</body>
</html>
