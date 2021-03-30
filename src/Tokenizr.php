<?php
	/**
	 * Tokenizr
	 * Salted, SHA256-hashed token generation and checking
	 * @author 	biohzrdmx <github.com/biohzrdmx>
	 * @version 3.0
	 * @license MIT
	 */

	namespace Tokenizr;

	class Tokenizr {

		/**
		 * Token salt
		 * @var string
		 */
		protected $salt;

		/**
		 * Token divider
		 * @var string
		 */
		protected $divider;

		/**
		 * Constructor
		 * @param string $salt    Token salt
		 * @param string $divider Token divider
		 */
		function __construct($salt, $divider = '.') {
			$this->salt = $salt;
			$this->divider = $divider;
		}

		/**
		 * Create a new token
		 * @param  string|array  $data Token data, may be an string or an array
		 * @return string              Generated token
		 */
		public function create($data) {
			$ret = false;
			if ( is_array($data) ) {
				$data = http_build_query($data);
				$ret = $this->create($data);
			} else {
				$hash = hash_hmac('sha256', $data, $this->salt);
				$ret = sprintf('%s%s%s', $data, $this->divider, $hash);
			}
			return $ret;
		}

		/**
		 * Check a token
		 * @param  string  $token The token to check
		 * @return boolean        TRUE if the token is valid, FALSE if it isn't
		 */
		public function check($token) {
			$ret = false;
			$parts = explode($this->divider, $token);
			$data = isset( $parts[0] ) ? $parts[0] : '';
			$hash = isset( $parts[1] ) ? $parts[1] : '';
			if ($data && $hash) {
				$check = hash_hmac('sha256', $data, $this->salt);
				$ret = $hash === $check;
			}
			return $ret;
		}

		/**
		 * Get token data
		 * @param  string       $token The token to retrieve data from
		 * @return string|array        Token data
		 */
		public function getData($token) {
			$ret = false;
			if ( $this->check($token) ) {
				$parts = explode($this->divider, $token);
				$data = isset( $parts[0] ) ? $parts[0] : '';
				if ( strpos($data, '&') ) {
					$items = null;
					parse_str($data, $items);
					$ret = $items;
				} else {
					$ret = $data;
				}
			}
			return $ret;
		}
	}

?>