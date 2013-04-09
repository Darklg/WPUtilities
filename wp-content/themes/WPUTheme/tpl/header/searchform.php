<?php
include dirname( __FILE__ ) . '/../../z-protect.php';
?><form role="search" method="get" id="searchform" action="<?php echo site_url(); ?>">
    <div>
        <label class="screen-reader-text" for="s"><?php echo __('Search for:'); ?></label>
        <input type="text" value="" name="s" id="s" />
        <input type="submit" id="searchsubmit" value="Search" />
    </div>
</form>