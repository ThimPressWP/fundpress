<?php

class DN_Ajax
{

	function __construct()
	{

		if( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX )
			return;

		$actions = array(
				'donate_load_form'	=> true,
				'donate_submit'		=> true
			);

		foreach ( $actions as $action => $nopriv ) {

			if( ! method_exists( $this, $action ) )
				return;

			add_action( 'wp_ajax_' . $action, array( $this, $action ) );
			if( $nopriv )
			{
				add_action( 'wp_ajax_noprive_' . $action, array( $this, $action ) );
			}

		}

	}

	/**
	 * ajax load form
	 * @return
	 */
	function donate_load_form()
	{

		if( ! isset( $_GET[ 'schema' ] ) || $_GET[ 'schema' ] !== 'donate-ajax' || empty( $_POST ) )
			return;

		if( ! isset( $_POST[ 'nonce' ] ) || ! wp_verify_nonce( $_POST[ 'nonce' ], 'thimpress_donate_nonce' ) )
			return;

		if( ! isset( $_POST[ 'campaign_id' ] ) || ! is_numeric( $_POST[ 'campaign_id' ] ) )
			return;

		$campaign = get_post( $_POST[ 'campaign_id' ] );

		if( ! $campaign || $campaign->post_type !== 'dn_campaign' )
			return;

		$campaign_id = $campaign->ID;
		$campaign = DN_Campaign::instance( $campaign );

		$compensates = array();
		$currency = $campaign->get_currency();

		if( $eachs = $campaign->get_compensate() )
		{
			foreach ( $eachs as $key => $compensate ) {
				/**
				 * convert campaign amount currency to amount with currency setting
				 * @var
				 */
				$amount = donate_campaign_convert_amount( $compensate['amount'], $currency );
				$compensates[ $key ] = array(
						'amount'		=> donate_price( $amount ),
						'desc'			=> $compensate['desc']
					);
			}
		}

		$payments = array();

		// load payments when checkout on lightbox setting isset yes
		if( DN_Settings::instance()->checkout->get( 'lightbox_checkout', 'no' ) === 'yes' )
		{
			$payment_enable = donate_payments_enable();
			foreach( $payment_enable as $key => $payment )
			{
				$payments[] = array(
						'id'		=> $payment->_id,
						'title'		=> $payment->_title,
						'icon'		=> $payment->_icon
					);
			}
		}

		wp_send_json( array(

				'status'		=> 'success',
				'campaign_id'	=> $campaign->ID,
				'compensates'	=> $compensates,
				'currency'		=> donate_get_currency_symbol(),
				'payments'		=> $payments // list payment allow

			));
	}

	/**
	 * donoate submit lightbox
	 * @return
	 */
	function donate_submit()
	{
		// validate sanitize input $_POST
		if( ! isset( $_GET[ 'schema' ] ) || $_GET[ 'schema' ] !== 'donate-ajax' || empty( $_POST ) )
			return;

		if( ! isset( $_POST[ 'thimpress_donate_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'thimpress_donate_nonce' ], 'thimpress_donate_nonce' ) )
			return;

		if( ! isset( $_POST[ 'campaign_id' ] ) || ! is_numeric( $_POST[ 'campaign_id' ] ) )
			return;

		$campaign = get_post( $_POST[ 'campaign_id' ] );

		if( ! $campaign || $campaign->post_type !== 'dn_campaign' )
			return;

		/************** NEW SCRIPT **************/
		// update cart
		$amount = 0;
		if( isset( $_POST[ 'donate_input_amount' ] ) )
			$amount = sanitize_text_field( $_POST[ 'donate_input_amount' ] );

		$compensate_desc = '';
		if( ! $amount && isset( $_POST[ 'donate_input_amount_package' ] ) )
		{
			$compensate_id = sanitize_text_field( $_POST[ 'donate_input_amount_package' ] );
			// Campaign
			$campaign = DN_Campaign::instance( $campaign );
			$compensates = $campaign->get_compensate();

			if( isset( $compensates[ $compensate_id ], $compensates[ $compensate_id ]['amount'] ) )
			{
				$amount = $compensates[ $compensate_id ]['amount'];
			}

		}

		// find compensate by amount donate
		$compensate_desc = donate_find_compensate_by_amount( $campaign, $amount );

		/**
		 * donate 0 currency
		 * @var
		 */
		if( $amount === 0 )
		{
			wp_send_json( array( 'status' => 'failed', 'message' => sprintf( '%s%s', __( 'Can not donate amount zero point', 'tp-donate' ), donate_price( 0 ) ) ) ); die();
		}
		// add to cart param
		$cart_params = apply_filters( 'donate_add_to_cart_item_params', array(

				'product_id'		=> $campaign->ID,
				'currency'			=> donate_get_currency()

			) );

		if( $cart_item_id = DN_Cart::instance()->add_to_cart( $campaign->ID, $cart_params, 1, $amount ) )
		{
			echo '<pre>'; print_r( DN_Cart::instance()->cart_contents ); die();
		}
		/************** END NEW SCRIPT **************/









		/** Old source code **/
		$params = array(
				'donate'			=> array(
						'campaign_id'		=> $campaign->ID
					),
				'dornor'			=> array(
						'first_name'		=> isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : __( 'No First Name', 'tp-donate' ),
						'last_name'			=> isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : __( 'No Last Name', 'tp-donate' ),
						'email'				=> isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : false,
						'phone'				=> isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '',
						'address'			=> isset( $_POST['address'] ) ? sanitize_text_field( $_POST['address'] ) : '',
						'addition_note'		=> isset( $_POST['addition_note'] ) ? sanitize_text_field( $_POST['addition_note'] ) : ''
					),
				'payment_method'	=> isset( $_POST['payment_method'] ) ? $_POST['payment_method'] : 'paypal'
			);

		$amount = 0;
		if( isset( $_POST[ 'donate_input_amount' ] ) )
			$amount = sanitize_text_field( $_POST[ 'donate_input_amount' ] );

		$compensate_desc = '';
		if( ! $amount && isset( $_POST[ 'donate_input_amount_package' ] ) )
		{
			$compensate_id = sanitize_text_field( $_POST[ 'donate_input_amount_package' ] );
			// Campaign
			$campaign = DN_Campaign::instance( $campaign );
			$compensates = $campaign->get_compensate();

			if( isset( $compensates[ $compensate_id ], $compensates[ $compensate_id ]['amount'] ) )
			{
				$amount = $compensates[ $compensate_id ]['amount'];
			}

		}

		// find compensate by amount donate
		$compensate_desc = donate_find_compensate_by_amount( $campaign, $amount );

		/**
		 * donate 0 currency
		 * @var
		 */
		if( $amount === 0 )
		{
			wp_send_json( array( 'status' => 'failed', 'message' => sprintf( '%s%s', __( 'Can not donate', 'tp-donate' ), donate_price( 0 ) ) ) ); die();
		}

		// add param amount
		$params['donate'][ 'amount' ] = $amount;

		if( $params )
		{
			$params = apply_filters( 'donate_ajax_submit_params', $params );

			$checkout = new DN_Checkout();
			// send json
			wp_send_json( $checkout->process_checkout( $params ) ); die();
		}

		// failed
		wp_send_json( array( 'status' => 'failed', 'message' => __( 'Something went wrong. Please try again or contact administrator', 'tp-donate' ) ) ); die();

	}

}

new DN_Ajax();
