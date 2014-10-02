<?php
include dirname( __FILE__ ) . '/../../../WPUTheme/z-protect.php';

global $current_user;

// Don't continue if comments are closed
if ( !comments_open() ) {
    return;
}

// Don't continue if registration is needed
if ( get_option( 'comment_registration' ) && !is_user_logged_in() ) {
    $logURL = site_url() . '/wp-login.php?redirect_to=' . urlencode( get_permalink() );
    echo '<p>' . sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment', 'wputh' ), $logURL ) . '</p>';
    return;
}

// Comment form
?>
<h3><?php echo __( 'Leave a reply', 'wputh' ); ?></h3>
<form action="<?php echo get_option( 'siteurl' ); ?>/wp-comments-post.php" method="post" id="commentform">
    <ul class="cssc-form cssc-form--default">
        <?php if ( is_user_logged_in() ) { get_currentuserinfo(); ?>
        <li class="box">
            <?php echo sprintf( __( 'Logged in as <strong>%s</strong>.', 'wputh' ), $current_user->display_name ); ?>
            <a title="<?php echo __( 'Log out of this account', 'wputh' ); ?>" href="<?php echo wp_logout_url( get_permalink() ); ?>">Log out &raquo;</a>
        </li>
        <?php } else { ?>
        <li>
            <ul class="twoboxes">
                <li class="box">
                    <label for="author"><?php echo __( 'Your name', 'wputh' ); ?> *</label>
                    <input required id="author" name="author" type="text"/>
                </li>
                <li class="box">
                    <label for="email"><?php echo __( 'Your mail', 'wputh' ); ?> *</label>
                    <input required id="email" name="email" type="email" />
                </li>
            </ul>
        </li>
        <li>
            <ul class="twoboxes">
                <li class="box">
                    <label for="url"><?php echo __( 'Your website', 'wputh' ); ?></label>
                    <input name="url" id="url" type="url"/>
                </li>
            </ul>
        </li>
        <?php } ?>
        <li class="box">
            <label for="comment"><?php echo __( 'Your comment', 'wputh' ); ?></label>
            <textarea name="comment" id="comment" cols="50" rows="10"></textarea>
        </li>
        <li class="box">
            <?php
            // Required by WordPress
            comment_id_fields();
            do_action( 'comment_form', $post->ID );
            ?>
            <button type="submit" class="cssc-button cssc-button--default"><?php echo __( 'Submit', 'wputh' ); ?></button>
        </li>
    </ul>
</form>
