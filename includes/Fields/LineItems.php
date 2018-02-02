<?php if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Class NF_Field_OptIn
 */
class NF_Sprout_Invoices_Fields_LineItems extends NF_Abstracts_Field
{
	protected $_name = 'sprout-invoices-line-items';
	protected $_type = 'sprout-invoices-line-items';
	protected $_section = 'misc';

	public static $_base_template = 'list';
	protected $_templates = 'sprout-invoices-line-items';

	protected $_test_value = '';

	protected $_settings = array( 'label', 'classes' );

	public function __construct() {

		parent::__construct();
		$this->_nicename = __( 'SI Line Items', 'ninja-forms' );

		error_log( 'templates: ' . print_r( $this->_templates, true ) );
		error_log( 'settings: ' . print_r( $this->_settings, true ) );
	}
}
