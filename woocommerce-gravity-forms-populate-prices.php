<?php
/*
 *  Plugin Name:  WooCommerce Gravity Forms Populate Prices
 *  Description:  Allows Gravity Forms Fields to be populated via other product's prices.  Designed to work with WooCommerce Gravity Forms Product Option fields only.
 *  Version: 1.0
 *  Author: Element Stark
 *  Author URI:  https://www.elementstark.com
 *  Tested up to: 5.3.2
 *
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}


class WC_Gravity_Forms_Populate_Prices {

	private static $instance;

	public static function register() {

		if ( self::$instance == null ) {
			self::$instance = new WC_Gravity_Forms_Populate_Prices();
		}

	}


	protected function __construct() {
		//Set it up globally for all forms.
		add_filter( 'gform_pre_render', [ $this, 'populate_prices' ] );
		add_filter( 'gform_pre_validation', [ $this, 'populate_prices' ] );
		add_filter( 'gform_pre_submission_filter', [ $this, 'populate_prices' ] );
	}

	public function populate_prices( $form ) {

		foreach ( $form['fields'] as &$field ) {

			if ( $field->type !== 'option' || strpos( $field->inputName, 'wc_product_price' ) === false ) {
				continue;
			}

			$product_choices = [];
			foreach($field->choices as $choice) {
				$product = wc_get_product($choice['value']);

				$product_choices[] = [
					'text' => $choice['text'],
					'price' => $product->get_price(),
					'value' => $choice['value']
				];
			}

			$field->choices = $product_choices;

		}

		return $form;

	}

}

if ( is_woocommerce_active() ) {
	WC_Gravity_Forms_Populate_Prices::register();
}
