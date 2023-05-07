<?php

/**
 * @package ThemePlate
 * @since 0.1.0
 */

namespace kermage\CardanoPress\EdgeUser;

use EUM_Handler;

class Main {

	public const UPDATE_DATA_FORMAT = 'https://raw.githubusercontent.com/CardanoPress/%s/main/update-data.json';


	public function load(): void {

		foreach ( get_option( 'active_plugins' ) as $plugin ) {
			if ( 0 !== strpos( $plugin, 'cardanopress' ) ) {
				continue;
			}

			$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . $plugin;
			$repo_name   = str_replace( 'cardanopress-', 'plugin-', dirname( $plugin ) );

			EUM_Handler::run( wp_normalize_path( $plugin_path ), sprintf( self::UPDATE_DATA_FORMAT, $repo_name ) );
		}

	}

}
