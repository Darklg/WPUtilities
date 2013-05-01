<?php
include dirname( __FILE__ ) . '/z-protect.php';
$id = $comment->comment_ID ;
?>

<div id="comment-<?php echo $id; ?>" <?php echo comment_class( 'loop-comment' ); ?> style="margin-left:<?php echo $comment_depth * 42; ?>px;">
    <div class="bmedia">
        <div><?php echo get_avatar( $comment, 32 ) ?></div>
        <div class="bm-cont">
            <aside class="comment-metas">
                <?php echo wputh_get_comment_author_name_link($comment); ?> —
                <time><?php echo comment_date(  __( 'F j, Y', 'wputh' ), $id ); ?></time>
            </aside>
            <div class="comment-content">
                <?php echo get_comment_text( $id ); ?>
            </div>
        </div>
    </div>
</div>
