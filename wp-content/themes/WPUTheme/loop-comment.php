<?php
include dirname( __FILE__ ) . '/z-protect.php';
$id = $comment->comment_ID ;
?>

<div id="comment-<?php echo $id; ?>" <?php echo comment_class( 'loop-comment' ); ?> style="margin-left:<?php echo $comment_depth * 20; ?>px;">
    <aside class="comment-metas">
        <strong><?php echo $comment->comment_author; ?></strong> —
        <time><?php echo comment_date(  __( 'F j, Y', 'wputh' ), $id ); ?></time>
    </aside>
    <div class="comment-content">
        <?php echo get_comment_text( $id ); ?>
    </div>
</div>
