<?php include dirname( __FILE__ ) . '/z-protect.php';
if(!isset($_POST['ajax'])){ ?>
</div>
<footer class="main-footer">
    <?php include get_template_directory() . '/tpl/footer/copyright.php'; ?>
</footer>
<?php wp_footer(); ?>
</body>
</html>
<?php }