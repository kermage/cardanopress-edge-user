<?php

/**
 * @package ThemePlate
 * @since 0.1.0
 */

namespace kermage\CardanoPress\EdgeUser;

use EUM_Handler;

class Main {

	protected object $core_update;

	public const UPDATE_DATA_FORMAT   = 'https://raw.githubusercontent.com/CardanoPress/%s/%s/update-data.json';
	public const DOWNLOAD_LINK_FORMAT = 'https://github.com/CardanoPress/%s/archive/refs/tags/v%s.zip';


	protected function get_key( string $plugin ): string {

		return 'eum_plugin_' . dirname( $plugin );

	}


	protected function get_slug( string $transient ): string {

		return str_replace( 'eum_plugin_', '', $transient );

	}


	public function load(): void {

		$this->core_update = get_site_transient( 'update_plugins' ) ?? (object) array();

		foreach ( get_option( 'active_plugins' ) as $plugin ) {
			if ( 0 !== strpos( $plugin, 'cardanopress' ) || plugin_basename( CP_EDGE_USER_FILE ) === $plugin ) {
				continue;
			}

			$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . $plugin;
			$repo_name   = str_replace( 'cardanopress-', 'plugin-', dirname( $plugin ) );
			$branch_name = apply_filters( 'cp_edge_user-' . $repo_name, 'main' );
			$plugin_data = get_plugin_data( $plugin_path, false, false );
			$update_url  = sprintf( self::UPDATE_DATA_FORMAT, $repo_name, $branch_name );
			$remote_data = ( new Checker( $plugin ) )->check( $update_url );

			if ( ! $remote_data || $plugin_data['Version'] === $remote_data->new_version || ( isset( $this->core_update->response[ $plugin ] ) && $this->core_update->response[ $plugin ] === $remote_data->new_version ) ) {
				continue;
			}

			EUM_Handler::run( wp_normalize_path( $plugin_path ), $update_url );
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

		$repo_name = str_replace( 'cardanopress-', 'plugin-', $this->get_slug( $transient ) );

		$branch_name = apply_filters( 'cp_edge_user-' . $repo_name, 'main' );

		$value->download_link = sprintf( self::DOWNLOAD_LINK_FORMAT, $repo_name, $value->new_version );

		if ( isset( $value->upgrade_notice ) && 'Please do the update now to seamlessly use the official WordPress Plugin Directory version' === $value->upgrade_notice ) {
			$value->upgrade_notice = 'Update now to use the latest GitHub version' . ( 'main' === $branch_name ? '' : ' (' . $branch_name . ')' );
		}

		return $value;

	}

}
