<?php
/** PHPUnit bootstrap for Beanstalk unit tests. */

require dirname( __DIR__ ) . '/vendor/autoload.php';

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 5 ) . '/' );
}

if ( ! defined( 'OBJECT' ) ) {
	define( 'OBJECT', 'OBJECT' );
}

if ( ! class_exists( 'WP_Post' ) ) {
	class WP_Post {
		/** @var int */
		public $ID = 0;

		/** @var string */
		public $post_type = '';

		/** @var string */
		public $post_status = '';

		/** @var string */
		public $post_content = '';

		/**
		 * @param array<string, mixed> $properties Post properties.
		 */
		public function __construct( array $properties = array() ) {
			foreach ( $properties as $property => $value ) {
				$this->{$property} = $value;
			}
		}
	}
}

define( 'BEANSTALK_THEME_DIR', dirname( __DIR__, 3 ) . '/wp-content/themes/bricks/beanstalk' );
require_once __DIR__ . '/TestCase.php';
