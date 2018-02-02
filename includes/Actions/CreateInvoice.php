<?php if ( ! defined( 'ABSPATH' ) || ! class_exists( 'NF_Abstracts_Action' ) ) { exit; }

/**
 * Class NF_SproutInvoices_Actions_UserRegistration
 */
final class NF_SproutInvoices_Actions_CreateInvoice extends NF_Abstracts_Action
{
	/**
	 * @var string
	 */
	protected $_name  = 'create-invoice';

	/**
	 * @var array
	 */
	protected $_tags = array();

	/**
	 * @var string
	 */
	protected $_timing = 'normal';

	/**
	 * @var int
	 */
	protected $_priority = '10';

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct();

		$this->_nicename = __( 'Create Invoice', 'ninja-forms-sprout-invoices' );

		add_action( 'admin_init', array( $this, 'init_settings' ) );

		add_action( 'ninja_forms_builder_templates', array( $this, 'builder_templates' ) );

		// Halts form rendering and shows logout message.
		add_filter( 'ninja_forms_display_show_form', array( $this, 'logout_message' ), 10, 3 );
	}



	/*
	* PUBLIC METHODS
	*/

	/**
	 * Logout Message
	 *
	 * Callback method for the ninja_forms_display_show_form filter.
	 *
	 * @param $boolean
	 * @param $form_id
	 * @param $form
	 * @return bool
	 */
	public function logout_message( $boolean, $form_id, $form ) {

		//Checks if filter has been set false anywhere else.
		if ( ! $boolean ) { return false; }

		//Get all actions then loop over them.
		$actions = Ninja_Forms()->form( $form_id )->get_actions();
		foreach ( $actions as $action ) {

			//Checks if user is logged in and if create-invoice action exists.
			if ( is_user_logged_in() && 'create-invoice' == $action->get_setting( 'type' )
				 && ! $_GET['nf_preview_form'] ) {

				//Echoes a logout link to the page.
				echo '<a href="' . wp_logout_url( get_permalink() ) . '">' .
					__( 'Please logout to view this form.', 'ninja-forms-sprout-invoices' ) .
					'</a>';
				return false;
			}
		}
		return true;
	}

	/**
	 * Init Settings
	 *
	 * Adds config file to action settings.
	 */
	public function init_settings() {

		$settings = NF_SproutInvoices::config( 'ActionCreateInvoiceSettings' );

		$li_types = SI_Line_Items::line_item_types();
		$line_item_types = array();
		foreach ( $li_types as $value => $label ) {
			$settings['product_type']['options'][] = array(
				'label' => $label,
				'name' => $label,
				'value' => $value,
			);
		}

		$this->_settings = array_merge( $this->_settings, $settings );
	}

	/**
	 * Builder Template
	 *
	 * Gets custom meta repeater template.
	 */
	public function builder_templates() {

		NF_SproutInvoices::template( 'custom-meta-repeater-row.html.php' );
	}

	/**
	 * Save
	 *
	 * @param $action_settings
	 */
	public function save( $action_settings ) {

	}

	/**
	 * Action Processing.
	 *
	 * Registers user upon form submission.
	 *
	 * @param $action_settings
	 * @param $form_id
	 * @param $data
	 * @return mixed
	 */
	public function process( $action_settings, $form_id, $data ) {


		//Get the fields for the current form.
		$fields = Ninja_Forms()->form( $form_id )->get_fields();
		error_log( 'fields: ' . print_r( $fields, TRUE ) );

		do_action( 'si_log', __CLASS__ . '::' . __FUNCTION__ . ' - action_settings', $action_settings, false );

		$generate  = $action_settings['si_generation'];
		$product_type  = $action_settings['product_type'];
		$redirect = ( isset( $action_settings['redirect'] ) && $action_settings['redirect'] ) ? true : false ;
		$create_user_and_client = ( isset( $action_settings['create_user_and_client'] ) && $action_settings['create_user_and_client'] ) ? true : false ;

		//Setting up array to send user info to WordPress
		$submission = array(
			'subject'    	=> isset( $action_settings['subject'] ) ? $action_settings['subject'] : '',
			'client_name'   => $action_settings['client_name'],
			'email'    		=> $action_settings['email'],
			'first_name'    => $action_settings['first_name'],
			'last_name'     => $action_settings['last_name'],
			'notes'         => $action_settings['notes'],
			'duedate'	    => strtotime( $action_settings['duedate'] ),
			'number'     	=> $action_settings['number'],
			'vat'     		=> $action_settings['vat'],
			'edit_url' 		=> sprintf( 
									'<a href="%s">#%s</a>', 
									add_query_arg( 
										array( 
											'id' => $entry['form_id'],
											'lid' => $entry['id'] ),
										admin_url( sprintf( '/wp-admin/edit.php?post_status=all&post_type=nf_sub&form_id=13&filter_action=Filter&paged=1', $entry['id'] ) ) ), 
									$entry['id']
								)
		);

		if ( isset( $action_settings['address'] ) ) {
			$submission['full_address'] = array();
		}

		$line_items = array();
		if ( isset( $action_settings['line_items'] ) ) {
			$line_items = explode( ',', $action_settings['line_items'] );
			if ( is_array( $line_items ) ) {
				foreach ( $line_items as $line_item ) {
					$line_items[] = array(
						'type' => $product_type,
						'desc' => $line_item,
						'rate' => 1,
						'total' => 1,
						'qty' => 1,
						'tax' => apply_filters( 'si_form_submission_line_item_default_tax', 0.00 ),
					);
				}
			}
		}
		$submission['line_items'] = $line_items;

		switch ( $generate ) {
			case 'invoice':
				$invoice_id = $this->create_invoice( $submission, $action_settings );
				if ( $create_user_and_client ) {
					$this->create_client( $submission, $action_settings, $invoice_id );
				}
				break;
			case 'estimate':
				$estimate_id = $this->create_estimate( $submission, $action_settings );
				if ( $create_user_and_client ) {
					$this->create_client( $submission, $action_settings, $estimate_id );
				}
				break;
			case 'client':
				$this->create_client( $submission, $action_settings );
				break;
			default:
				// nada
				break;
		}

		//Register our custom meta.
		$custom_meta = $this->register_custom_meta( $action_settings, $doc_id );

		//If custom meta is present, we assign it to a variable.
		if ( ! empty( $custom_meta ) ) {
			$data['actions']['user_management']['custom_meta'] = $custom_meta;
		}

		// posible redirection
		$url = wp_get_referer();
		if ( $redirect && ( isset( $invoice_id ) || isset( $estimate_id ) ) ) {
			if ( get_post_type( $invoice_id ) == SI_Invoice::POST_TYPE ) {
				$url = get_permalink( $invoice_id );
			} elseif ( get_post_type( $estimate_id ) == SI_Estimate::POST_TYPE ) {
				$url = get_permalink( $estimate_id );
			}
		}

		//Reloads page upon submission.
		$data['actions']['redirect'] = $url;

		return $data;
	}



	protected function create_invoice( $submission = array(), $entry = array() ) {

		$invoice_args = array(
			'subject' => sprintf( apply_filters( 'si_form_submission_title_format', '%1$s (%2$s)', $submission ), $submission['subject'], $submission['client_name'] ),
			'fields' => $submission,
			'form' => $entry,
			'history_link' => $submission['edit_url'],
			);
		/**
		 * Creates the invoice from the arguments
		 */
		$invoice_id = SI_Invoice::create_invoice( $invoice_args );
		$invoice = SI_Invoice::get_instance( $invoice_id );

		$invoice->set_line_items( $submission['line_items'] );

		// notes
		if ( isset( $submission['notes'] ) ) {
			$record_id = SI_Internal_Records::new_record( $submission['notes'], SI_Controller::PRIVATE_NOTES_TYPE, $invoice_id, '', 0, false );
		}

		if ( isset( $submission['number'] ) ) {
			$invoice->set_invoice_id( $submission['number'] );
		}

		if ( isset( $submission['duedate'] ) ) {
			$invoice->set_due_date( $submission['duedate'] );
		}

		// Finally associate the doc with the form submission
		add_post_meta( $invoice_id, 'gf_form_id', $entry['id'] );

		$history_link = sprintf( '<a href="%s">#%s</a>', $submission['edit_url'], $entry['id'] );

		do_action( 'si_new_record',
			sprintf( __( 'Invoice Submitted: Form %s.', 'sprout-invoices' ), $history_link ),
			'invoice_submission',
			$invoice_id,
			sprintf( __( 'Invoice Submitted: Form %s.', 'sprout-invoices' ), $history_link ),
			0,
		false );

		return $invoice_id;

	}

	protected function create_estimate( $submission = array(), $entry = array() ) {

		$estimate_args = array(
			'subject' => sprintf( apply_filters( 'si_form_submission_title_format', '%1$s (%2$s)', $submission ), $submission['subject'], $submission['client_name'] ),
			'fields' => $submission,
			'form' => $form,
			'history_link' => $submission['edit_url'],
		);
		/**
		 * Creates the estimate from the arguments
		 */
		$estimate_id = SI_Estimate::create_estimate( $estimate_args );
		$estimate = SI_Estimate::get_instance( $estimate_id );

		$estimate->set_line_items( $submission['line_items'] );

		// notes
		if ( isset( $submission['notes'] ) ) {
			$record_id = SI_Internal_Records::new_record( $submission['notes'], SI_Controller::PRIVATE_NOTES_TYPE, $estimate_id, '', 0, false );
		}

		if ( isset( $submission['number'] ) ) {
			$estimate->set_estimate_id( $submission['number'] );
		}

		if ( isset( $submission['duedate'] ) ) {
			$estimate->set_expiration_date( $submission['duedate'] );
		}

		// Finally associate the doc with the form submission
		add_post_meta( $estimate_id, 'gf_form_id', $entry['id'] );

		$history_link = sprintf( '<a href="%s">#%s</a>', $submission['edit_url'], $entry['id'] );

		do_action( 'si_new_record',
			sprintf( __( 'Estimate Submitted: Form %s.', 'sprout-invoices' ), $history_link ),
			'estimate_submission',
			$estimate_id,
			sprintf( __( 'Estimate Submitted: Form %s.', 'sprout-invoices' ), $history_link ),
			0,
		false );

		return $estimate_id;
	}

	protected function create_client( $submission = array(), $entry = array(), $doc_id = 0 ) {

		$email = $submission['email'];
		$client_name = $submission['client_name'];
		$first_name = $submission['first_name'];
		$last_name = $submission['last_name'];

		/**
		 * Attempt to create a user before creating a client.
		 */
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			if ( '' !== $email ) {
				// check to see if the user exists by email
				$user = get_user_by( 'email', $email );
				if ( $user ) {
					$user_id = $user->ID;
				}
			}
		}

		// Create a user for the submission if an email is provided.
		if ( ! $user_id ) {
			// email is critical
			if ( '' !== $email ) {
				$user_args = array(
					'user_login' => esc_attr__( $email ),
					'display_name' => isset( $client_name ) ? esc_attr__( $client_name ) : esc_attr__( $email ),
					'user_email' => esc_attr__( $email ),
					'first_name' => $first_name,
					'last_name' => $last_name,
					'user_url' => '',
				);
				$user_id = SI_Clients::create_user( $user_args );
			}
		}

		// Make up the args in creating a client
		$args = array(
			'company_name' => $submission['client_name'],
			'website' => '',
			'address' => $submission['full_address'],
			'user_id' => $user_id,
		);
		$client_id = SI_Client::new_client( $args );
		$client = SI_Client::get_instance( $client_id );

		if ( isset( $submission['vat'] ) ) {
			$client->save_post_meta( array( '_iva' => $submission['vat'] ) );
			$client->save_post_meta( array( '_vat' => $submission['vat'] ) );
		}

		if ( ! $doc_id ) {
			return;
		}

		/**
		 * After a client is created assign it to the estimate
		 */
		$doc = si_get_doc_object( $doc_id );
		$doc->set_client_id( $client_id );

	}

	/**
	 * Register Custom Meta
	 *
	 * Checks for custom meta, then processes if user meta exists.
	 *
	 * @param $action_settings
	 * @param $user_id
	 */
	private function register_custom_meta( $action_settings, $post_id ) {

		if ( ! empty( $action_settings['custom_meta'] ) ) {
			foreach ( $action_settings['custom_meta'] as $custom_meta ) {
				add_post_meta( $post_id, $custom_meta['key'], $custom_meta['value'] );
			}
		}
	}
}
