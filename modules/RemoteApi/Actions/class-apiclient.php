<?php
/**
 * File class-apiclient.php
 *
 * Contains the Remote API client
 *
 * @since 2024-09-17
 * @license GPL-3.0-or-later
 *
 * @package ProteectLogin\RemoteAPI
 */

namespace ProtectLogin\Modules\RemoteApi\Actions;

/**
 * Containts the client to access the remote API
 */
class ApiClient {

	/**
	 * Current instance.
	 *
	 * @var null
	 */
	private static $instance = null;

	/**
	 * URL of remote API
	 *
	 * @var string|false|mixed|null
	 */
	private string $endpoint_url = '';

	/**
	 * Access key of remote API
	 *
	 * @var string|false|mixed|null
	 */
	private string $endpoint_key = '';


	/**
	 * Return instance of this class.
	 *
	 * @return ApiClient
	 */
	public static function get_instance(): ApiClient {
		\protect_login_ensure_activation();
		if ( null === self::$instance ) {
			self::$instance = new ApiClient();
		}

		return self::$instance;
	}

	/**
	 * Constructor of the class
	 */
	public function __construct() {
		$this->endpoint_url = get_option( 'protect_login_remote_api_endpoint_url', '' );
		$this->endpoint_key = get_option( 'protect_login_remote_api_endpoint_key', '' );
	}

	/**
	 * Returns allowlisted addresses from Remote API or empty array, if no endpoint defined or invalid API key given
	 *
	 * @return array
	 */
	public function get_remote_allowlisted_ips() {
		$url       = $this->endpoint_url . '/login/list-allowlisted-addresses';
		$addresses = $this->open( $url );
		if ( ! is_array( $addresses ) ) {
			return array();
		}

		return $addresses;
	}

	/**
	 * Returns blocklisted addresses from Remote API or empty array, if no endpoint defined or invalid API key given
	 *
	 * @return array
	 */
	public function get_remote_blocklisted_ips() {
		$url       = $this->endpoint_url . '/login/list-blocklisted-addresses';
		$addresses = $this->open( $url );
		if ( ! is_array( $addresses ) ) {
			return array();
		}

		return $addresses;
	}

	/**
	 * Get current locked out addresses from API or empty array, if no endpoint defined or invalid API key given
	 *
	 * @return array
	 */
	public function get_remote_blocked_ips() {
		$url       = $this->endpoint_url . '/list-blocked-addresses';
		$addresses = $this->open( $url );
		if ( ! is_array( $addresses ) ) {
			return array();
		}

		return $addresses;
	}

	/**
	 * Submits a list of addresses to permanently allow on remote API
	 *
	 * @param array $addresses Address to permanently allow.
	 *
	 * @return void
	 */
	public function allow_addresses( array $addresses ) {
		$url    = $this->endpoint_url . '/login/release';
		$params = array(
			'addresses'   => $addresses,
			'permanently' => true,
		);

		$result = $this->open( $url, $params );
	}

	/**
	 * Submits a list of addresses to permanently block on remote API
	 *
	 * @param array $addresses Address to permanently block.
	 *
	 * @return void
	 */
	public function block_addresses( array $addresses ) {
		$url    = $this->endpoint_url . '/login/lockout';
		$params = array(
			'addresses'   => $addresses,
			'permanently' => true,
		);

		$result = $this->open( $url, $params );
	}

	/**
	 * Removes an address from remote's block list
	 *
	 * @param string $address Address to remove from block list.
	 *
	 * @return void
	 */
	public function unblock_remote_address( string $address ) {
		$url    = $this->endpoint_url . '/unblock';
		$params = array(
			'address' => $address,
		);

		$result = $this->open( $url, $params );
	}

	/**
	 * Blocks a single address on rtemote API permanently (block list) or temporary (locked-out list)
	 *
	 * @param string $address Address to block.
	 * @param bool   $permanently Permanently block (block list) or locked-out list on false.
	 *
	 * @return void
	 */
	public function block_remote_address( string $address, bool $permanently ) {
		$url    = $this->endpoint_url . '/lockout';
		$params = array(
			'address'     => $address,
			'permanently' => $permanently,
		);

		$result = $this->open( $url, $params );
	}

	/**
	 * Releases an address on remote API
	 *
	 * @param string $address Address to release.
	 * @param bool   $allowlist Put on allow-list (true) or just remove from locked-out list (false).
	 *
	 * @return void
	 */
	public function release_remote_address( string $address, bool $allowlist ) {
		$url    = $this->endpoint_url . '/release';
		$params = array(
			'address'     => $address,
			'permanently' => $allowlist,
		);

		$result = $this->open( $url, $params );
	}

	/**
	 * Removes an address from a specified list on remote API
	 *
	 * @param string $address   Address to remove.
	 * @param string $listtype List from where to remove the address (allowlist or blocklist).
	 *
	 * @return void
	 */
	public function remove( string $address, string $listtype ) {
		$url    = $this->endpoint_url . '/login/remove';
		$params = array(
			'address'  => $address,
			'listtype' => $listtype,
		);

		$result = $this->open( $url, $params );
	}

	/**
	 * Internal function to send the request to remote API. Returns a json_decoded array of the rsponse or false, if:
	 *      - Endpoint not configured (no endpoint_url or endpoint_api_key)
	 *      - An error occures on execution
	 *
	 * @param string $url Complete URL to open.
	 * @param array  $args  Arguments to pass to endpoint, the API key is merged into args automatically.
	 *
	 * @return false|mixed
	 */
	private function open( string $url, array $args = array() ) {
		if (
			'' === $this->endpoint_key ||
			'' === $this->endpoint_url
		) {
			return false;
		}

		$plugin_data = get_plugin_data( PROTECT_LOGIN_STARTUP_FILE );

		$http_request            = array();
		$http_request['headers'] = array(
			'api-key'      => $this->endpoint_key,
			'Content-Type' => 'application/json',
			'user_agent'   => 'ProtectLogin/' . $plugin_data['Version'],
		);

		$http_request['body'] = wp_json_encode( $args );

		$result = wp_remote_post( $url, $http_request );

		if ( is_a( $result, 'WP_Error' ) ) {
			return false;
		}
		return json_decode( wp_remote_retrieve_body( $result ), true );
	}
}
