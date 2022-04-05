<?php

namespace WPSMTP;

use WP_Error;

class Process {

	private $mail_id;
	private $wsOptions;

	public function __construct() {
		global $wpdb;

		add_filter( 'wp_mail', array( $this, 'log_mails' ), PHP_INT_MAX );
		add_action( 'wp_mail_failed', array( $this, 'update_failed_status' ), PHP_INT_MAX );
		$this->wsOptions = get_option( 'wp_smtp_options' );
	}

	function log_mails( $parts ) {
		if( $this->wsOptions['disable_logs'] != 'yes' ) {
			global $wpdb;

			$data = $parts;

			unset( $data['attachments'] );

			$this->mail_id = Db::get_instance()->insert( $data );
		}

		return $parts;
	}

	/**
	 * @param WP_Error $wp_error
	 */
	function update_failed_status( $wp_error ) {
	

		Admin::$phpmailer_error = $wp_error;
		if( $this->wsOptions['disable_logs'] != 'yes' ) {
			global $wpdb;
			$data = $wp_error->get_error_data('wp_mail_failed' );

			unset( $data['phpmailer_exception_code'] );
			unset( $data['attachments'] );

			$data['error'] = $wp_error->get_error_message();

			if ( ! is_numeric( $this->mail_id ) ) {
				Db::get_instance()->insert( $data );
			} else {
				Db::get_instance()->update( $data, array( 'mail_id' => $this->mail_id ) );
			}
		}
	}
}