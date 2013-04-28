<?php include dirname( __FILE__ ) . '/z-protect.php'; ?>

<div <?php echo comment_class(); ?> style="margin-left:<?php echo $comment_depth * 20; ?>px;">
    <aside>
        <strong><?php echo $comment->comment_author; ?></strong> —
        <time><?php echo comment_date(  __( 'F j, Y', 'wputh' ) ); ?></time>
    </aside>
    <?php echo get_comment_text( $comment->comment_ID ); ?>
</div>
