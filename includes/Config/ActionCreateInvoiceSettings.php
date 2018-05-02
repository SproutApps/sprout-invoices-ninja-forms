<?php if ( ! defined( 'ABSPATH' ) ) { exit; }

return apply_filters( 'ninja_forms_create_invoice_settings', array(


	/*
    |--------------------------------------------------------------------------
    | Role
    |--------------------------------------------------------------------------
    */

	'si_generation' => array(
		'name'          => 'si_generation',
		'type'          => 'select',
		'label'         => __( 'Estimate or Invoice', 'sprout-invoices' ),
		'width'         => 'full',
		'value'         => 'estimate',
		'group'         => 'primary',
		'options'       => array(
			array(
				'value' => 'estimate',
				'label' => __( 'Estimate', 'sprout-invoices' ),
			),
			array(
				'value' => 'invoice',
				'label' => __( 'Invoice', 'sprout-invoices' ),
			),
			array(
				'value' => 'client',
				'label' => __( 'Client (only)', 'sprout-invoices' ),
			),
		),
	),

	/*
    |--------------------------------------------------------------------------
    | Username
    |--------------------------------------------------------------------------
    */

	'subject' => array(
		'name'          => 'subject',
		'type'          => 'field-select',
		'label'         => __( 'Invoice/Estimate Title', 'sprout-invoices' ),
		'width'         => 'full',
		'group'         => 'primary',
		'field_types'   => array(
			'textbox',
		),
	),

	/*
    |--------------------------------------------------------------------------
    | Company Name
    |--------------------------------------------------------------------------
    */

	'client_name' => array(
		'name'          => 'client_name',
		'type'          => 'field-select',
		'label'         => __( 'Company/Client Name', 'sprout-invoices' ),
		'width'         => 'full',
		'group'         => 'primary',
		'field_types'   => array(
			'textbox',
		),
	),

	/*
    |--------------------------------------------------------------------------
    | Email
    |--------------------------------------------------------------------------
    */

	'email' => array(
		'name'          => 'email',
		'type'          => 'field-select',
		'label'         => __( 'Email', 'sprout-invoices' ),
		'width'         => 'full',
		'group'         => 'primary',
		'field_types'   => array(
			'textbox',
			'email',
		),
	),

	/*
    |--------------------------------------------------------------------------
    | First Name
    |--------------------------------------------------------------------------
    */

	'first_name' => array(
		'name'          => 'first_name',
		'type'          => 'field-select',
		'label'         => __( 'First Name', 'sprout-invoices' ),
		'width'         => 'full',
		'group'         => 'primary',
		'field_types'   => array(
			'textbox',
			'firstname',
		),
	),

	/*
    |--------------------------------------------------------------------------
    | Last Name
    |--------------------------------------------------------------------------
    */

	'last_name' => array(
		'name'          => 'last_name',
		'type'          => 'field-select',
		'label'         => __( 'Last Name', 'sprout-invoices' ),
		'width'         => 'full',
		'group'         => 'primary',
		'field_types'   => array(
			'textbox',
			'lastname',
		),
	),

	/*
    |--------------------------------------------------------------------------
    | Address
    |--------------------------------------------------------------------------
    */

	'address' => array(
		'name'          => 'address',
		'type'          => 'field-select',
		'label'         => __( 'Address', 'sprout-invoices' ),
		'width'         => 'full',
		'group'         => 'primary',
		'field_types'   => array(
			'address'
		),
	),

	/*
    |--------------------------------------------------------------------------
    | Notes
    |--------------------------------------------------------------------------
    */

	'notes' => array(
		'name'          => 'notes',
		'type'          => 'field-select',
		'label'         => __( 'Notes', 'sprout-invoices' ),
		'width'         => 'full',
		'group'         => 'primary',
		'field_types'   => array(
			'textarea',
			'textbox',
		),
	),

	/*
    |--------------------------------------------------------------------------
    | Number
    |--------------------------------------------------------------------------
    */

	'duedate' => array(
		'name'          => 'duedate',
		'type'          => 'field-select',
		'label'         => __( 'Due Date', 'sprout-invoices' ),
		'width'         => 'full',
		'group'         => 'primary',
		'field_types'   => array(
			'date',
		),
	),

	/*
    |--------------------------------------------------------------------------
    | Number
    |--------------------------------------------------------------------------
    */

	'number' => array(
		'name'          => 'number',
		'type'          => 'field-select',
		'label'         => __( 'Estimate/Invoice Number', 'sprout-invoices' ),
		'width'         => 'full',
		'group'         => 'primary',
		'field_types'   => array(
			'textarea',
			'textbox',
		),
	),

	/*
    |--------------------------------------------------------------------------
    | VAT
    |--------------------------------------------------------------------------
    */

	'vat' => array(
		'name'          => 'vat',
		'type'          => 'field-select',
		'label'         => __( 'VAT Number', 'sprout-invoices' ),
		'width'         => 'full',
		'group'         => 'primary',
		'field_types'   => array(
			'textbox'
		),
	),

	/*
    |--------------------------------------------------------------------------
    | Defined Line Items
    |--------------------------------------------------------------------------
    */

	'line_items' => array(
		'name'          => 'line_items',
		'type'          => 'field-select',
		'label'         => __( 'Line Items', 'sprout-invoices' ),
		'width'         => 'full',
		'group'         => 'primary',
		'field_types'   => array(
			'listmultiselect',
			'listselect',
			'listcheckbox',
			'listradio',
		),
	),


	/*
    |--------------------------------------------------------------------------
    | Product Type
    |--------------------------------------------------------------------------
    */

	'product_type' => array(
		'name'          => 'product_type',
		'type'          => 'select',
		'label'         => __( 'LINE ITEM TYPE', 'sprout-invoices' ),
		'width'         => 'full',
		'value'         => 'task',
		'group'         => 'advanced',
		'options'       => array(),
	),

	/*
    |--------------------------------------------------------------------------
    | Create Invoice Email
    |--------------------------------------------------------------------------
    */

	'create_user_and_client' => array(
		'name'  => 'create_user_and_client',
		'type'  => 'toggle',
		'label' => __( 'CREATE CLIENT AND USER', 'sprout-invoices' ),
		'width' => 'full',
		'group' => 'advanced',
		'value' => 1,
	),
	/*
    |--------------------------------------------------------------------------
    | Redirect
    |--------------------------------------------------------------------------
    */

	'redirect' => array(
		'name'  => 'redirect',
		'type'  => 'toggle',
		'label' => __( 'REDIRECT TO ESTIMATE/INVOICE', 'sprout-invoices' ),
		'width' => 'full',
		'group' => 'advanced',
		'value' => 1,
	),

	/*
    |--------------------------------------------------------------------------
    | Custom Meta
    |--------------------------------------------------------------------------
    */

	'custom_meta' => array(
		'name'      => 'custom_meta',
		'type'      => 'option-repeater',
		'label'     => __( 'Custom Meta', 'sprout-invoices' ) . ' <a href="#" class="nf-add-new">' .
						__( 'Add New', 'sprout-invoices' ) . '</a>',
		'width'     => 'full',
		'group'     => 'advanced',
		'tmpl_row'  => 'tmpl-nf-sprout-invoices-custom-meta-repeater-row',
		'value'     => array(),
		'columns'   => array(
			'key' => array(
				'header'    => __( 'Meta Key', 'sprout-invoices' ),
				'default'   => '',
				'options' => array(),
			),
			'value' => array(
				'header'    => __( 'Meta Value', 'sprout-invoices' ),
				'default'   => '',
			),
		),
	),
) );
