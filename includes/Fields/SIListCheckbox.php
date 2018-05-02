<?php if ( ! defined( 'ABSPATH' ) ) { exit; }
/**
 * Class NF_Field_OptIn
 */
class NF_Fields_SIListCheckbox extends NF_Abstracts_List
{
	protected $_name = 'silistcheckbox';

	protected $_type = 'silistcheckbox';

	protected $_nicename = 'SI Pre-defined Line Items';

	protected $_section = 'common';

	protected $_icon = 'list';

	protected $_templates = 'listcheckbox';

	protected $_old_classname = 'list-checkbox';

	public function __construct() {

		parent::__construct();

		$this->_nicename = __( 'Sprout Invoices', 'ninja-forms' );

		add_filter( 'ninja_forms_merge_tag_calc_value_' . $this->_type, array( $this, 'get_calc_value' ), 10, 2 );
	}

	public function admin_form_element( $id, $value ) {
		$form_id = get_post_meta( absint( $_GET['post'] ), '_form_id', true );

		$field = Ninja_Forms()->form( $form_id )->get_field( $id );

		$list = '';

		$items_and_products = array();
		if ( method_exists( 'Predefined_Items', 'get_items_and_products' ) ) {
			$items_and_products = Predefined_Items::get_items_and_products();
		}
		$item_groups = apply_filters( 'si_predefined_items_for_submission', $items_and_products );
		foreach ( $item_groups as $type => $items ) {

			foreach ( $items as $key ) {
				$checked = '';
				if ( is_array( $value ) && in_array( $option['value'], $value ) ) { $checked = 'checked'; }
				$label = $key['title'];
				$title = sprintf( '<b>%s</b><br/><small>%s</small>', $key['title'], $key['content'] );
				$id = $key['id'];
				$list .= "<li><label><input type='checkbox' value='{$label}' name='fields[$id][]' $checked> {$title}</label></li>";
			}
		}
		return "<input type='hidden' name='fields[$id]' value='0' ><ul>$list</ul>";
	}

	public function get_calc_value( $value, $field ) {

		$selected = explode( ',', $value );
		$value = 0;
		if ( isset( $field['options'] ) ) {
			foreach ( $field['options'] as $option ) {
				if ( ! isset( $option['value'] ) || ! in_array( $option['value'], $selected )  || ! isset( $option['calc'] ) ) { continue; }
				$value += $option['calc'];
			}
		}
		return $value;
	}

	public function get_parent_type() {

		return 'listcheckbox';
	}
}
