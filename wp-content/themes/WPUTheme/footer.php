<?php
include dirname( __FILE__ ) . '/z-protect.php';
if (isset($_POST['is_ajax'])) return;
?>
</div></div>
<div class="main-footer centered-container">
    <footer class="contentinfo" role="contentinfo" id="contentinfo">
        <?php include get_template_directory() . '/tpl/footer/copyright.php'; ?>
    </footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
