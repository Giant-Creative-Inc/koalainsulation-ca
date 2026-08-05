<?php
namespace Bricks;

do_action( 'bricks_before_footer' );

do_action( 'render_footer' );

do_action( 'bricks_after_footer' );

do_action( 'bricks_after_site_wrapper' );

wp_footer(); // Ensure this is the last function before closing </body>
?>
</body>
</html>