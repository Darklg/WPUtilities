<?php
include dirname( __FILE__ ) . '/../../z-protect.php';
?>
<div class="search search--header header-search">
  <form role="search" method="get" id="header-search" class="search__form" action="<?php echo site_url(); ?>">
      <div class="search__inner">
          <label class="cssc-remove-element" for="s"><?php _e( 'Search for:', 'wputh' ); ?></label>
          <input type="text" value="" name="s" id="s" class="search__input" id="header-search__input" placeholder="<?php echo esc_attr__( 'Enter your keywords...', 'wputh' ); ?>" title="<?php echo esc_attr__( 'Search by keywords', 'wputh' ); ?>" />
          <button class="search__submit cssc-button cssc-button--default" id="search_submit" title="<?php echo sprintf( __( 'Search on %s', 'wputh' ), get_bloginfo('name') ); ?>"><?php _e('Search', 'wputh'); ?></button>
      </div>
  </form>
</div>
