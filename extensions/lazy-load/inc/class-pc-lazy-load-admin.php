<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PC_Lazy_Load_Admin' ) ):

	class PC_Lazy_Load_Admin extends PC_Extension_Admin_Base {
		public $fields;

		function __construct() {
			$this->fields = array(
				'post_content'   => array(
					'default'   => true,
					'sanitizer' => 'boolval',
				),
				'image'          => array(
					'default'   => true,
					'sanitizer' => 'boolval',
				),
				'iframe'         => array(
					'default'   => true,
					'sanitizer' => 'boolval',
				),
				'widget_text'    => array(
					'default'   => true,
					'sanitizer' => 'boolval',
				),
				'post_thumbnail' => array(
					'default'   => true,
					'sanitizer' => 'boolval',
				),
				'avatar'         => array(
					'default'   => true,
					'sanitizer' => 'boolval',
				),
			);

			parent::__construct( array(
				'plugin_id'   => 'lazyload',
				'plugin_name' => __( 'Lazy Load', 'powered-cache' ),
			) );


			$this->setup();
		}

		public function settings_page() {
			$settings_file[] = realpath( dirname( __FILE__ ) ) . '/settings.php';
			parent::settings_template( $settings_file );
		}


		/**
		 * Return an instance of the current class
		 *
		 * @since 1.0
		 * @return PC_Preload_Admin
		 */
		public static function factory() {
			static $instance = false;

			if ( ! $instance ) {
				$instance = new self();
			}

			return $instance;
		}
	}
endif;