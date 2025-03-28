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

		return 'eum_plugin_' . $plugin;

	}


	protected function get_slug( string $transient ): string {

		return str_replace( 'eum_plugin_', '', $transient );

	}


	public function load(): void {

		$transient = get_site_transient( 'update_plugins' );

		if ( ! is_object( $transient ) ) {
			$transient = (object) array();
		}

		$this->core_update = $transient;

		foreach ( (array) get_option( 'active_plugins' ) as $plugin ) {
			if ( 0 !== strpos( $plugin, 'cardanopress' ) || plugin_basename( CP_EDGE_USER_FILE ) === $plugin ) {
				continue;
			}

			$plugin_slug = dirname( $plugin );
			$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . $plugin;
			$repo_name   = str_replace( 'cardanopress-', 'plugin-', $plugin_slug );
			$branch_name = (string) apply_filters( 'cp_edge_user-' . $repo_name, 'main' );
			$plugin_data = get_plugin_data( $plugin_path, false, false );
			$update_url  = sprintf( self::UPDATE_DATA_FORMAT, $repo_name, $branch_name );
			$remote_data = ( new Checker( $plugin_slug ) )->check( $update_url );
			$core_data   = $this->core_update->response[ $plugin ] ?? (object) array();

			add_action( 'delete_site_transient_eum_plugin_' . $plugin_slug, array( $this, 'reset_cached_data' ) );

			if ( ! $remote_data || ( $plugin_data['Version'] === $remote_data->new_version ) || ( isset( $core_data->new_version ) && $core_data->new_version === $remote_data->new_version ) ) {
				continue;
			}

			EUM_Handler::run( wp_normalize_path( $plugin_path ), $update_url );
			add_filter(
				'pre_set_site_transient_' . $this->get_key( $plugin_slug ),
				array( $this, 'filter_transient' ),
				10,
				2
			);
		}

	}


	public function reset_cached_data( string $transient ): void {
		delete_site_transient( Checker::DATA_PREFIX . $this->get_slug( $transient ) );
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

		$branch_name = (string) apply_filters( 'cp_edge_user-' . $repo_name, 'main' );

		$value->download_link = sprintf( self::DOWNLOAD_LINK_FORMAT, $repo_name, $value->new_version );

		if ( isset( $value->upgrade_notice ) && 'Please do the update now to seamlessly use the official WordPress Plugin Directory version' === $value->upgrade_notice ) {
			$value->upgrade_notice = 'Update now to use the latest GitHub version' . ( 'main' === $branch_name ? '' : ' (' . $branch_name . ')' );
		}

		return $value;

	}

}
