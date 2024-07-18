<?php

/**
 * @package ThemePlate
 * @since 0.1.0
 */

namespace kermage\CardanoPress\EdgeUser;

class Checker {

	protected string $plugin;

	public const DATA_PREFIX = 'cpeu_';


	public function __construct( string $plugin ) {

		$this->plugin = $plugin;

	}


	public function check( string $url ): ?object {

		$key  = self::DATA_PREFIX . $this->plugin;
		$data = get_site_transient( $key );

		if ( ! is_object( $data ) ) {
			$data = $this->call_remote( $url );

			set_site_transient( $key, $data, HOUR_IN_SECONDS );
		}

		return $data;

	}


	protected function call_remote( string $url ): ?object {

		$options  = array(
			'method'  => 'GET',
			'timeout' => 10,
		);
		$response = wp_remote_request( $url, $options );

		if ( is_wp_error( $response ) ) {
			return null;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );

		if ( 200 === $code ) {
			return json_decode( $body );
		}

		return null;

	}

}
