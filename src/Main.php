<?php

/**
 * @package ThemePlate
 * @since 0.1.0
 */

namespace kermage\CardanoPress\EdgeUser;

use EUM_Handler;

class Main {

	public const UPDATE_DATA_FORMAT   = 'https://raw.githubusercontent.com/CardanoPress/%s/main/update-data.json';
	public const DOWNLOAD_LINK_FORMAT = 'https://github.com/CardanoPress/%s/archive/refs/tags/v%s.zip';


	protected function get_key( string $plugin ): string {

		return 'eum_plugin_' . dirname( $plugin );

	}


	protected function get_slug( string $transient ): string {

		return str_replace( 'eum_plugin_', '', $transient );

	}


	public function load(): void {

		foreach ( get_option( 'active_plugins' ) as $plugin ) {
			if ( 0 !== strpos( $plugin, 'cardanopress' ) ) {
				continue;
			}

			$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . $plugin;
			$repo_name   = str_replace( 'cardanopress-', 'plugin-', dirname( $plugin ) );

			EUM_Handler::run( wp_normalize_path( $plugin_path ), sprintf( self::UPDATE_DATA_FORMAT, $repo_name ) );
			add_filter(
				'pre_set_site_transient_' . $this->get_key( $plugin ),
				array( $this, 'filter_transient' ),
				10,
				2
			);
		}

	}


	/**
	 * @param mixed $value
	 * @param string $transient
	 *
	 * @return mixed
	 */
	public function filter_transient( $value, string $transient ) {

		if ( empty( $value ) || ! is_object( $value ) || ! isset( $value->download_link, $value->new_version ) ) {
			return $value;
		}

		$value->download_link = sprintf( self::DOWNLOAD_LINK_FORMAT, $this->get_slug( $transient ), $value->new_version );

		if ( isset( $value->upgrade_notice ) && 'Please do the update now to seamlessly use the official WordPress Plugin Directory version' === $value->upgrade_notice ) {
			$value->upgrade_notice = 'Update now to use the latest GitHub version';
		}

		return $value;

	}

}
