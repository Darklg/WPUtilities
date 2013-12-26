<?php
include dirname( __FILE__ ) . '/../../z-protect.php';
?><form role="search" method="get" id="header-search" class="header-search" action="<?php echo site_url(); ?>">
    <div>
        <label class="cssc-remove-element" for="header-search__input"><?php echo __( 'Search for:', 'wputh' ); ?></label>
        <input type="text" value="" name="s" id="header-search__input" />
        <button class="cssc-button" id="header-search__submit"><?php echo __('Search', 'wputh'); ?></button>
    </div>
</form>
