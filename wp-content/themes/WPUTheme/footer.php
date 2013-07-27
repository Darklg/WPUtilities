<?php include dirname( __FILE__ ) . '/z-protect.php';
if(!isset($_POST['ajax'])){ ?>
</div>
<footer class="main-footer">
    <small>&copy; <?php echo date('Y'); ?> - <?php bloginfo('name'); ?></small>
</footer>
<?php wp_footer(); ?>
</body>
</html>
<?php }