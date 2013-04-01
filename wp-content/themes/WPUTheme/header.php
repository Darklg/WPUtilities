<!DOCTYPE HTML>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8" />
    <title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>
    <?php wp_head(); ?>
</head>
<body>
<header>
    <?php include TEMPLATEPATH . '/tpl/header/title.php'; ?>
    <?php include TEMPLATEPATH . '/tpl/header/searchform.php'; ?>
</header>