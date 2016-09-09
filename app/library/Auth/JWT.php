<?php
/**
 * Full JSON Web Tokens class that conforms with the official spec
 * http://tools.ietf.org/html/draft-ietf-oauth-json-web-token-06
 */
namespace App\Library\Auth;

/**
 * USAGE:
 * 
 * $key = "example_key";
 * $token = array(
 *   "iss" => "http://example.org",
 *   "aud" => "http://example.com",
 *   "iat" => 1356999524,
 *   "nbf" => 1357000000
 * );
 *
 * IMPORTANT:
 * You must specify supported algorithms for your application. See
 * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
 * for a list of spec-compliant algorithms.
 *
 * $jwt = JWT::encode($token, $key);
 * $decoded = JWT::decode($jwt, $key, array('HS256'));
 * 
 * print_r($decoded);
 * 
 * NOTE: This will now be an object instead of an associative array. To get
 * an associative array, you will need to cast it as such:
 * 
 * $decoded_array = (array) $decoded;
 * 
 * You can add a leeway to account for when there is a clock skew times between
 * the signing and verifying servers. It is recommended that this leeway should
 * not be bigger than a few minutes.
 *
 * Source: http://self-issued.info/docs/draft-ietf-oauth-json-web-token.html#nbfDef
 *
 * JWT::$leeway = 60; // $leeway in seconds
 * $decoded = JWT::decode($jwt, $key, array('HS256'));
 */
class JWT
{
	/**
	 * When checking nbf, iat or expiration times,
	 * we want to provide some extra leeway time to
	 * account for clock skew.
	 */
	public static $leeway = 0;

	public static $supported_algs = array(
		'HS256' => array('hash_hmac', 'SHA256'),
		'HS512' => array('hash_hmac', 'SHA512'),
		'HS384' => array('hash_hmac', 'SHA384'),
		'RS256' => array('openssl', 'SHA256'),
	);

	/**
	 * Decodes a JWT string into a PHP object.
	 *
	 * @param string $jwt The JWT
	 * @param string|array|null $key The key, or map of keys. If the algorithm used is asymmetric, this is the public key
	 * @param array $allowed_algs List of supported verification algorithms. Supported algorithms are 'HS256', 'HS384', 'HS512' and 'RS256'
	 * @return object The JWT's payload as a PHP object
	 * @throws \Exception
	 * @uses json_decode
	 * @uses urlsafe_decode
	 */
	public static function decode($jwt, $key, $allowed_algs = array()) {
		if (empty($key)) {
			throw new \Exception('Key cannot be empty');
		}
		$tks = explode('.', $jwt);
		if (count($tks) != 3) {
			throw new \Exception('Wrong number of segments');
		}
		list($headb64, $bodyb64, $cryptob64) = $tks;
		if (null === ($header = JWT::json_decode(JWT::urlsafe_decode($headb64)))) {
			throw new \Exception('Invalid header encoding');
		}
		if (null === $payload = JWT::json_decode(JWT::urlsafe_decode($bodyb64))) {
			throw new \Exception('Invalid claims encoding');
		}
		$sig = JWT::urlsafe_decode($cryptob64);
		if (empty($header->alg)) {
			throw new \Exception('Empty algorithm');
		}
		if (empty(self::$supported_algs[$header->alg])) {
			throw new \Exception('Algorithm not supported');
		}
		if (!is_array($allowed_algs) || !in_array($header->alg, $allowed_algs)) {
			throw new \Exception('Algorithm not allowed');
		}
		if (is_array($key) || $key instanceof \ArrayAccess) {
			if (isset($header->kid)) {
				$key = $key[$header->kid];
			} else {
				throw new \Exception('"kid" empty, unable to lookup correct key');
			}
		}
		// Check the signature
		if (!JWT::verify("$headb64.$bodyb64", $sig, $key, $header->alg)) {
			throw new \Exception('Signature verification failed');
		}
		// Check if the nbf if it is defined. This is the time that the
		// token can actually be used. If it's not yet that time, abort.
		if (isset($payload->nbf) && $payload->nbf > (time() + self::$leeway)) {
			throw new \Exception('Cannot handle token prior to ' . date(\DateTime::ISO8601, $payload->nbf));
		}
		// Check that this token has been created before 'now'. This prevents
		// using tokens that have been created for later use (and haven't
		// correctly used the nbf claim).
		if (isset($payload->iat) && $payload->iat > (time() + self::$leeway)) {
			throw new \Exception('Cannot handle token prior to ' . date(\DateTime::ISO8601, $payload->iat));
		}
		// Check if this token has expired.
		if (isset($payload->exp) && (time() - self::$leeway) >= $payload->exp) {
			throw new \Exception('Expired token');
		}
		return $payload;
	}

	/**
	 * Converts and signs a PHP object or array into a JWT string.
	 *
	 * @param object|array $payload PHP object or array
	 * @param string $key The secret key. If the algorithm used is asymmetric, this is the private key
	 * @param string $alg The signing algorithm. Supported algorithms are 'HS256', 'HS384', 'HS512' and 'RS256'
	 * @param array $head An array with header elements to attach
	 * @return string A signed JWT
	 * @uses json_encode
	 * @uses urlsafe_encode
	 */
	public static function encode($payload, $key, $alg = 'HS256', $keyId = null, $head = null) {
		$header = array('typ' => 'JWT', 'alg' => $alg);
		if ($keyId !== null) {
			$header['kid'] = $keyId;
		}
		if (isset($head) && is_array($head)) {
			$header = array_merge($head, $header);
		}
		$segments = array();
		$segments[] = JWT::urlsafe_encode(JWT::json_encode($header));
		$segments[] = JWT::urlsafe_encode(JWT::json_encode($payload));
		$signing_input = implode('.', $segments);
		$signature = JWT::sign($signing_input, $key, $alg);
		$segments[] = JWT::urlsafe_encode($signature);
		return implode('.', $segments);
	}

	/**
	 * Sign a string with a given key and algorithm.
	 *
	 * @param string $msg The message to sign
	 * @param string|resource $key The secret key
	 * @param string $alg The signing algorithm. Supported algorithms are 'HS256', 'HS384', 'HS512' and 'RS256'
	 * @return string An encrypted message
	 * @throws \Exception Unsupported algorithm was specified
	 */
	public static function sign($msg, $key, $alg = 'HS256') {
		if (empty(self::$supported_algs[$alg])) {
			throw new \Exception('Algorithm not supported');
		}
		list($function, $algorithm) = self::$supported_algs[$alg];
		switch ($function) {
			case 'hash_hmac':
				return hash_hmac($algorithm, $msg, $key, true);
			case 'openssl':
				$signature = '';
				$success = openssl_sign($msg, $signature, $key, $algorithm);
				if (!$success) {
					throw new \Exception("OpenSSL unable to sign data");
				} else {
					return $signature;
				}
		}
	}

	/**
	 * Verify a signature with the message, key and method. Not all methods
	 * are symmetric, so we must have a separate verify and sign method.
	 *
	 * @param string $msg The original message (header and body)
	 * @param string $signature The original signature
	 * @param string|resource $key For HS*, a string key works. for RS*, must be a resource of an openssl public key
	 * @param string $alg The algorithm
	 * @return bool
	 * @throws \Exception Invalid Algorithm or OpenSSL failure
	 */
	private static function verify($msg, $signature, $key, $alg) {
		if (empty(self::$supported_algs[$alg])) {
			throw new \Exception('Algorithm not supported');
		}
		list($function, $algorithm) = self::$supported_algs[$alg];
		switch ($function) {
			case 'openssl':
				$success = openssl_verify($msg, $signature, $key, $algorithm);
				if (!$success) {
					throw new \Exception("OpenSSL unable to verify data: " . openssl_error_string());
				} else {
					return $signature;
				}
			case 'hash_hmac':
			default:
				$hash = hash_hmac($algorithm, $msg, $key, true);
				if (function_exists('hash_equals')) {
					return hash_equals($signature, $hash);
				}
				$len = min(self::safe_strlen($signature), self::safe_strlen($hash));
				$status = 0;
				for ($i = 0; $i < $len; $i++) {
					$status |= (ord($signature[$i]) ^ ord($hash[$i]));
				}
				$status |= (self::safe_strlen($signature) ^ self::safe_strlen($hash));
				return ($status === 0);
		}
	}

	/**
	 * Decode a JSON string into a PHP object.
	 *
	 * @param string $input JSON string
	 * @return object Object representation of JSON string
	 * @throws \Exception Provided string was invalid JSON
	 */
	public static function json_decode($input) {
		if (version_compare(PHP_VERSION, '5.4.0', '>=') && !(defined('JSON_C_VERSION') && PHP_INT_SIZE > 4)) {
			/** In PHP >=5.4.0, json_decode() accepts an options parameter, that allows you
			 * to specify that large ints (like Steam Transaction IDs) should be treated as
			 * strings, rather than the PHP default behaviour of converting them to floats.
			 */
			$obj = json_decode($input, false, 512, JSON_BIGINT_AS_STRING);
		} else {
			/** Not all servers will support that, however, so for older versions we must
			 * manually detect large ints in the JSON string and quote them (thus converting
			 *them to strings) before decoding, hence the preg_replace() call.
			 */
			$max_int_length = strlen((string)PHP_INT_MAX) - 1;
			$json_without_bigints = preg_replace('/:\s*(-?\d{' . $max_int_length . ',})/', ': "$1"', $input);
			$obj = json_decode($json_without_bigints);
		}
		if (function_exists('json_last_error') && $errno = json_last_error()) {
			JWT::handle_json_error($errno);
		} elseif ($obj === null && $input !== 'null') {
			throw new \Exception('Null result with non-null input');
		}
		return $obj;
	}

	/**
	 * Encode a PHP object into a JSON string.
	 *
	 * @param object|array $input A PHP object or array
	 * @return string JSON representation of the PHP object or array
	 * @throws \Exception Provided object could not be encoded to valid JSON
	 */
	public static function json_encode($input) {
		$json = json_encode($input);
		if (function_exists('json_last_error') && $errno = json_last_error()) {
			JWT::handle_json_error($errno);
		} elseif ($json === 'null' && $input !== null) {
			throw new \Exception('Null result with non-null input');
		}
		return $json;
	}

	/**
	 * Decode a string with URL-safe Base64.
	 *
	 * @param string $input A Base64 encoded string
	 * @return string A decoded string
	 */
	public static function urlsafe_decode($input) {
		$remainder = strlen($input) % 4;
		if ($remainder) {
			$padlen = 4 - $remainder;
			$input .= str_repeat('=', $padlen);
		}
		return base64_decode(strtr($input, '-_', '+/'));
	}

	/**
	 * Encode a string with URL-safe Base64.
	 *
	 * @param string $input The string you want encoded
	 * @return string The base64 encode of what you passed in
	 */
	public static function urlsafe_encode($input) {
		return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
	}

	/**
	 * Helper method to create a JSON error.
	 *
	 * @param int $errno An error number from json_last_error()
	 * @throws \Exception
	 */
	private static function handle_json_error($errno) {
		$messages = array(
			JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
			JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
			JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON'
		);
		throw new \Exception(isset($messages[$errno]) ? $messages[$errno] : 'Unknown JSON error: ' . $errno);
	}

	/**
	 * Get the number of bytes in cryptographic strings.
	 *
	 * @param string
	 * @return int
	 */
	private static function safe_strlen($str) {
		if (function_exists('mb_strlen')) {
			return mb_strlen($str, '8bit');
		}
		return strlen($str);
	}
}
