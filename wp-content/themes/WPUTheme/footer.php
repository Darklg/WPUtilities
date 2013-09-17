<?php
include dirname( __FILE__ ) . '/z-protect.php';
if (isset($_POST['ajax'])) return;
?>
</div></div>
<footer class="main-footer centered-container">
    <div>
        <?php include get_template_directory() . '/tpl/footer/copyright.php'; ?>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
