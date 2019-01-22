<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

/*
 * Plugin Name: Sprout Invoices + Ninja Forms
 * Plugin URI: https://sproutapps.co/sprout-invoices/integrations/
 * Description: Allows for a form submitted by Ninja Forms to create all necessary records to send your client an invoice or estimate.
 * Author: Sprout Apps
 * Version: 1.3.1
 * Author URI: https://sproutapps.co
 * Text Domain: sprout-invoices
 * Domain Path: languages
 */

if ( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3', '<' ) || get_option( 'ninja_forms_load_deprecated', false ) ) {

} else {

	/**
	 * Class NF_SproutInvoices
	 */
	final class NF_SproutInvoices
	{
		const VERSION = '1.2';
		const SLUG    = 'sprout-invoices';
		const NAME    = 'Sprout Invoices';
		const AUTHOR  = 'WP Ninjas';
		const PREFIX  = 'NF_SproutInvoices';

		/**
		 * @var NF_SproutInvoices
		 * @since 3.0
		 */
		private static $instance;

		/**
		 * Plugin Directory
		 *
		 * @since 3.0
		 * @var string $dir
		 */
		public static $dir = '';

		/**
		 * Plugin URL
		 *
		 * @since 3.0
		 * @var string $url
		 */
		public static $url = '';

		/**
		 * Main Plugin Instance
		 *
		 * Insures that only one instance of a plugin class exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 3.0
		 * @static
		 * @static var array $instance
		 * @return NF_SproutInvoices Highlander Instance
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! (self::$instance instanceof NF_SproutInvoices) ) {
				self::$instance = new NF_SproutInvoices();

				self::$dir = plugin_dir_path( __FILE__ );

				self::$url = plugin_dir_url( __FILE__ );

				/*
                 * Register our autoloader
                 */
				spl_autoload_register( array( self::$instance, 'autoloader' ) );
			}

			return self::$instance;
		}

		/**
		 * NF_SproutInvoices constructor.
		 */
		public function __construct() {

			// add_filter( 'ninja_forms_register_fields', array( $this, 'register_fields' ) );

			/*
             * Register actions
             */
			add_filter( 'ninja_forms_register_actions', array( $this, 'register_actions' ) );

			add_action( 'ninja_forms_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			//add_filter( 'ninja_forms_new_form_templates', array( $this, 'register_templates' ) );
		}

		public function enqueue_scripts() {

			wp_enqueue_script( 'nf_user_management', self::$url . 'assets/js/errorHandling.js', array() );
		}

		/**
		 * Register Fields
		 *
		 * @param array $actions
		 * @return array $actions
		 */
		public function register_fields( $actions ) {
			if ( ! class_exists( 'NF_Fields_SIListCheckbox' ) ) {
				$classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
				require_once $classes_dir . 'Fields/SIListCheckbox.php';
			}
			$actions['silistcheckbox'] = new NF_Fields_SIListCheckbox();
			return $actions;
		}



		/**
		 * Register Actions
		 *
		 * Registers all actions that are used by user management add-on.
		 *
		 * @param $actions
		 * @return mixed
		 */
		public function register_actions( $actions ) {
			$actions['create-invoice']     = new NF_SproutInvoices_Actions_CreateInvoice();
			return $actions;
		}

		/**
		 * Strip Merge Tags
		 *
		 * Accepts a key/value pair of $action_settings removes the merge tag formatting from the Value only of each pair.
		 *
		 * @param $action_settings
		 * @return array of setting_value(with mergetag formatting removed)/setting_name
		 *
		 * example return username_1489426787243 => username
		 */
		public function strip_merge_tags( $action_settings ) {

			//Build our return array.
			$settings = array();

			//Loop over action settings.
			foreach ( $action_settings as $key => $value ) {
				//Removes the merge tag formatting
				$settings_value = str_replace( '{field:', '', $value );
				$settings_value = str_replace( '}', '', $settings_value );

				//Builds $fields array for return value.
				$settings[ $settings_value ] = $key;
			}
			return $settings;
		}

		/**
		 * Get Field ID
		 *
		 * Loops over fields and action setting keys to build an array of setting name.
		 *
		 * @param $fields
		 * @param $settings
		 * @return array
		 *
		 * example return username => 5
		 */
		public function get_field_id( $fields, $settings ) {

			//Creating the array we will use to return.
			$field_ids = array();

			//Loop over $fields array
			foreach ( $fields as $field ) {

				//Get the field key of each field in the fields array.
				$field_key = $field->get_setting( 'key' );

				//Loop over array of setting keys.
				foreach ( $settings as $setting_key => $setting_value ) {

					//Compares setting key and field key to ensure they are the same.
					if ( $setting_key == $field_key ) {
						//Builds the return array of the setting value and field IDs
						$field_ids[ $setting_value ] = $field->get_id();
					}
				}
			}
			return $field_ids;
		}

		/**
		 * Register Templates
		 *
		 * Registers our custom form templates.
		 *
		 * @param $templates
		 * @return mixed
		 */
		public function register_templates( $templates ) {

			$templates['formtemplate-createinvoice'] = array(
				'id'            => 'formtemplate-createinvoice',
				'title'         => __( 'Create a Sprout Invoice', 'sprout-invoices' ),
				'template-desc' => __( 'Create an invoice (and client records) for Sprout Invoices.', 'sprout-invoices' ),
			);

			return $templates;
		}

		/**
		 * autoloader - built by Kozo Generator/
		 *
		 * @param $class_name
		 */
		public function autoloader( $class_name ) {

			if ( class_exists( $class_name ) ) { return; }

			if ( false === strpos( $class_name, self::PREFIX ) ) { return; }

			$class_name = str_replace( self::PREFIX, '', $class_name );
			$classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
			$class_file = str_replace( '_', DIRECTORY_SEPARATOR, $class_name ) . '.php';

			if ( file_exists( $classes_dir . $class_file ) ) {
				require_once $classes_dir . $class_file;
			}
		}

		/**
		 * Template
		 *
		 * @param string $file_name
		 * @param array $data
		 * @return string
		 */
		public static function template( $file_name = '', array $data = array() ) {

			if ( ! $file_name ) { return; }

			extract( $data );

			include self::$dir . 'includes/Templates/' . $file_name;
		}

		/**
		 * Config
		 *
		 * @param $file_name
		 * @return mixed
		 */
		public static function config( $file_name ) {

			return include self::$dir . 'includes/Config/' . $file_name . '.php';
		}
	}

	/**
	 * The main function responsible for returning The Highlander Plugin
	 * Instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * @since 3.0
	 * @return {class} Highlander Instance
	 */
	function NF_SproutInvoices() {

		return NF_SproutInvoices::instance();
	}

	NF_SproutInvoices();
}
