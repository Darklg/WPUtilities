<?php
include dirname( __FILE__ ) . '/../../z-protect.php';
?>
<div class="search search--header header-search">
  <form role="search" method="get" id="search" class="search__form" action="<?php echo site_url(); ?>">
      <div class="search__inner">
          <label class="cssc-remove-element" for="s"><?php _e( 'Search for&nbsp;:', 'wputh' ); ?></label>
          <input type="text" value="" name="s" id="s" class="search__input cssc-input" placeholder="<?php esc_attr_e( 'Enter your keywords...', 'wputh' ); ?>" title="<?php esc_attr_e( 'Search by keywords', 'wputh' ); ?>" />
          <button class="search__submit cssc-button" id="search_submit" title="<?php echo sprintf( __( 'Search all %s', 'wputh' ), get_bloginfo('name') ); ?>"><?php _e('Search', 'wputh'); ?></button>
      </div>
  </form>
</div>
