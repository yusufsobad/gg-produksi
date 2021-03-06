<?php

class formatting extends _error{

	public static function sanitize($string='',$type='text'){
		$func = '_'.$type;

		if(is_callable(array(new self(), $func))){
			return self::$func($string);
		}else{
			return self::_text($string);
		}
	}

	private static function _text($string=''){
		$string = self::wp_sanitize_text_field($string);
		return self::php_sanitize_string($string);
	}

	private static function _email($email=''){
		$email = self::wp_sanitize_email($email);
		return self::php_sanitize_email($email);
	}

	private static function _select($string=''){
		switch (gettype($string)) {
			case 'integer':
				return self::_number($string);
				break;

			case 'string':
				return self::_text($string);
				break;

			case 'double':
				return self::_decimal($string);
				break;
			
			default:
				return self::_text($string);
				break;
		}
	}

	private static function _number($number=0){
		return self::_decimal($number);
	}

	private static function _decimal($number=0){
		$number = clear_format($number);
		return floatval($number);
	}

	private static function _textarea($string=''){
		$string = self::wp_sanitize_textarea_field($string);
		return self::php_sanitize_string($string);
	}

	private static function _html($string=''){
		return self::_textarea($string);
	}

	public static function _date($date='',$format='Y-m-d'){
		$date = date($date);
		$date = strtotime($date);

		return date($format,$date);
	}

	private static function _price($price=0){	
		$number = clear_format($price);
		return intval($number);
	}

	// -------------------------------------------------------------
	// PHP Sanitize ------------------------------------------------
	// -------------------------------------------------------------

	private static function php_sanitize_string($string=''){
		return filter_var($string, FILTER_SANITIZE_STRING);
	}

	private static function php_sanitize_email($string=''){
		$email = filter_var($string, FILTER_SANITIZE_EMAIL);

		if(filter_var($email, FILTER_VALIDATE_EMAIL)){
			return $email;
		}

		die(parent::_alert_db('Email Not Valid'));
	}

	private static function php_sanitize_url($string=''){
		$url = filter_var($string, FILTER_SANITIZE_URL);

		if(filter_var($url, FILTER_VALIDATE_URL)){
			return $url;
		}

		die(parent::_alert_db('URL not valid'));
	}

	// -------------------------------------------------------------
	// WP Reference ------------------------------------------------
	// -------------------------------------------------------------
	
	private static function wp_sanitize_user( $username, $strict = false ) {
		$raw_username = $username;
		$username     = self::_strip_all_tags( $username );
		$username     = self::remove_accents( $username );
		// Kill octets
		$username = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $username );
		$username = preg_replace( '/&.+?;/', '', $username ); // Kill entities
	 
		// If strict, reduce to ASCII for max portability.
		if ( $strict ) {
			$username = preg_replace( '|[^a-z0-9 _.\-@]|i', '', $username );
		}
	 
		$username = trim( $username );
		// Consolidate contiguous whitespace
		$username = preg_replace( '|\s+|', ' ', $username );
	 
		/**
		 * Filters a sanitized username string.
		 *
		 * @since 2.0.1
		 *
		 * @param string $username     Sanitized username.
		 * @param string $raw_username The username prior to sanitization.
		 * @param bool   $strict       Whether to limit the sanitization to specific characters. Default false.
		 */
	}

	private static function wp_sanitize_email( $email ) {
		// Test for the minimum length the email can be
		if ( strlen( $email ) < 6 ) {
			/**
			 * Filters a sanitized email address.
			 *
			 * This filter is evaluated under several contexts, including 'email_too_short',
			 * 'email_no_at', 'local_invalid_chars', 'domain_period_sequence', 'domain_period_limits',
			 * 'domain_no_periods', 'domain_no_valid_subs', or no context.
			 *
			 * @since 2.8.0
			 *
			 * @param string $sanitized_email The sanitized email address.
			 * @param string $email           The email address, as provided to sanitize_email().
			 * @param string|null $message    A message to pass to the user. null if email is sanitized.
			 */
			return parent::_alert_db('email_too_short');
		}

		// Test for an @ character after the first position
		if ( strpos( $email, '@', 1 ) === false ) {
			/** This filter is documented in wp-includes/formatting.php */
			return parent::_alert_db('email_no_at');
		}

		// Split out the local and domain parts
		list( $local, $domain ) = explode( '@', $email, 2 );

		// LOCAL PART
		// Test for invalid characters
		$local = preg_replace( '/[^a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~\.-]/', '', $local );
		if ( '' === $local ) {
			/** This filter is documented in wp-includes/formatting.php */
			return parent::_alert_db('local_invalid_chars');
		}

		// DOMAIN PART
		// Test for sequences of periods
		$domain = preg_replace( '/\.{2,}/', '', $domain );
		if ( '' === $domain ) {
			/** This filter is documented in wp-includes/formatting.php */
			return parent::_alert_db('domain_period_sequence' );
		}

		// Test for leading and trailing periods and whitespace
		$domain = trim( $domain, " \t\n\r\0\x0B." );
		if ( '' === $domain ) {
			/** This filter is documented in wp-includes/formatting.php */
			return parent::_alert_db('domain_period_limits' );
		}

		// Split the domain into subs
		$subs = explode( '.', $domain );

		// Assume the domain will have at least two subs
		if ( 2 > count( $subs ) ) {
			/** This filter is documented in wp-includes/formatting.php */
			return parent::_alert_db('domain_no_periods' );
		}

		// Create an array that will contain valid subs
		$new_subs = array();

		// Loop through each sub
		foreach ( $subs as $sub ) {
			// Test for leading and trailing hyphens
			$sub = trim( $sub, " \t\n\r\0\x0B-" );

			// Test for invalid characters
			$sub = preg_replace( '/[^a-z0-9-]+/i', '', $sub );

			// If there's anything left, add it to the valid subs
			if ( '' !== $sub ) {
				$new_subs[] = $sub;
			}
		}

		// If there aren't 2 or more valid subs
		if ( 2 > count( $new_subs ) ) {
			/** This filter is documented in wp-includes/formatting.php */
			return parent::_alert_db('domain_no_valid_subs' );
		}

		// Join valid subs into the new domain
		$domain = join( '.', $new_subs );

		// Put the email back together
		$sanitized_email = $local . '@' . $domain;

		// Congratulations your email made it!
		/** This filter is documented in wp-includes/formatting.php */
		return $sanitized_email;
	}

	/**
	 * Sanitizes a string from user input or from the database.
	 *
	 * - Checks for invalid UTF-8,
	 * - Converts single `<` characters to entities
	 * - Strips all tags
	 * - Removes line breaks, tabs, and extra whitespace
	 * - Strips octets
	 *
	 * @since 2.9.0
	 *
	 * @see sanitize_textarea_field()
	 * @see wp_check_invalid_utf8()
	 * @see wp_strip_all_tags()
	 *
	 * @param string $str String to sanitize.
	 * @return string Sanitized string.
	 */
	private static function wp_sanitize_text_field( $str ) {
		$filtered = self::_sanitize_text_fields( $str, false );

		/**
		 * Filters a sanitized text field string.
		 *
		 * @since 2.9.0
		 *
		 * @param string $filtered The sanitized string.
		 * @param string $str      The string prior to being sanitized.
		 */
		return $filtered;
	}

	private static function wp_sanitize_textarea_field( $str ) {
		$filtered = self::_sanitize_text_fields( $str, true );

		/**
		 * Filters a sanitized textarea field string.
		 *
		 * @since 4.7.0
		 *
		 * @param string $filtered The sanitized string.
		 * @param string $str      The string prior to being sanitized.
		 */
		return $filtered;
	}

	/**
	 * Internal helper function to sanitize a string from user input or from the db
	 *
	 * @since 4.7.0
	 * @access private
	 *
	 * @param string $str String to sanitize.
	 * @param bool $keep_newlines optional Whether to keep newlines. Default: false.
	 * @return string Sanitized string.
	 */
	
	private static function _sanitize_text_fields( $str, $keep_newlines = false ) {
		if ( is_object( $str ) || is_array( $str ) ) {
			return '';
		}

		$str = (string) $str;

		$filtered = self::wp_check_invalid_utf8( $str );

		if ( strpos( $filtered, '<' ) !== false ) {
			$filtered = self::wp_pre_kses_less_than( $filtered );
			// This will strip extra whitespace for us.
			$filtered = self::wp_strip_all_tags( $filtered, false );

			// Use html entities in a special case to make sure no later
			// newline stripping stage could lead to a functional tag
			$filtered = str_replace( "<\n", "&lt;\n", $filtered );
		}

		if ( ! $keep_newlines ) {
			$filtered = preg_replace( '/[\r\n\t ]+/', ' ', $filtered );
		}
		$filtered = trim( $filtered );

		$found = false;
		while ( preg_match( '/%[a-f0-9]{2}/i', $filtered, $match ) ) {
			$filtered = str_replace( $match[0], '', $filtered );
			$found    = true;
		}

		if ( $found ) {
			// Strip out the whitespace that may now exist after removing the octets.
			$filtered = trim( preg_replace( '/ +/', ' ', $filtered ) );
		}

		return $filtered;
	}
	
	public static function _strip_all_tags( $string, $remove_breaks = false ) {
		$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
		$string = strip_tags( $string );
	 
		if ( $remove_breaks ) {
			$string = preg_replace( '/[\r\n\t ]+/', ' ', $string );
		}
	 
		return trim( $string );
	}
	
	private static function remove_accents( $string ) {
		if ( ! preg_match( '/[\x80-\xff]/', $string ) ) {
			return $string;
		}
	 
		if ( seems_utf8( $string ) ) {
			$chars = array(
				// Decompositions for Latin-1 Supplement
				'??' => 'a',
				'??' => 'o',
				'??' => 'A',
				'??' => 'A',
				'??' => 'A',
				'??' => 'A',
				'??' => 'A',
				'??' => 'A',
				'??' => 'AE',
				'??' => 'C',
				'??' => 'E',
				'??' => 'E',
				'??' => 'E',
				'??' => 'E',
				'??' => 'I',
				'??' => 'I',
				'??' => 'I',
				'??' => 'I',
				'??' => 'D',
				'??' => 'N',
				'??' => 'O',
				'??' => 'O',
				'??' => 'O',
				'??' => 'O',
				'??' => 'O',
				'??' => 'U',
				'??' => 'U',
				'??' => 'U',
				'??' => 'U',
				'??' => 'Y',
				'??' => 'TH',
				'??' => 's',
				'??' => 'a',
				'??' => 'a',
				'??' => 'a',
				'??' => 'a',
				'??' => 'a',
				'??' => 'a',
				'??' => 'ae',
				'??' => 'c',
				'??' => 'e',
				'??' => 'e',
				'??' => 'e',
				'??' => 'e',
				'??' => 'i',
				'??' => 'i',
				'??' => 'i',
				'??' => 'i',
				'??' => 'd',
				'??' => 'n',
				'??' => 'o',
				'??' => 'o',
				'??' => 'o',
				'??' => 'o',
				'??' => 'o',
				'??' => 'o',
				'??' => 'u',
				'??' => 'u',
				'??' => 'u',
				'??' => 'u',
				'??' => 'y',
				'??' => 'th',
				'??' => 'y',
				'??' => 'O',
				// Decompositions for Latin Extended-A
				'??' => 'A',
				'??' => 'a',
				'??' => 'A',
				'??' => 'a',
				'??' => 'A',
				'??' => 'a',
				'??' => 'C',
				'??' => 'c',
				'??' => 'C',
				'??' => 'c',
				'??' => 'C',
				'??' => 'c',
				'??' => 'C',
				'??' => 'c',
				'??' => 'D',
				'??' => 'd',
				'??' => 'D',
				'??' => 'd',
				'??' => 'E',
				'??' => 'e',
				'??' => 'E',
				'??' => 'e',
				'??' => 'E',
				'??' => 'e',
				'??' => 'E',
				'??' => 'e',
				'??' => 'E',
				'??' => 'e',
				'??' => 'G',
				'??' => 'g',
				'??' => 'G',
				'??' => 'g',
				'??' => 'G',
				'??' => 'g',
				'??' => 'G',
				'??' => 'g',
				'??' => 'H',
				'??' => 'h',
				'??' => 'H',
				'??' => 'h',
				'??' => 'I',
				'??' => 'i',
				'??' => 'I',
				'??' => 'i',
				'??' => 'I',
				'??' => 'i',
				'??' => 'I',
				'??' => 'i',
				'??' => 'I',
				'??' => 'i',
				'??' => 'IJ',
				'??' => 'ij',
				'??' => 'J',
				'??' => 'j',
				'??' => 'K',
				'??' => 'k',
				'??' => 'k',
				'??' => 'L',
				'??' => 'l',
				'??' => 'L',
				'??' => 'l',
				'??' => 'L',
				'??' => 'l',
				'??' => 'L',
				'??' => 'l',
				'??' => 'L',
				'??' => 'l',
				'??' => 'N',
				'??' => 'n',
				'??' => 'N',
				'??' => 'n',
				'??' => 'N',
				'??' => 'n',
				'??' => 'n',
				'??' => 'N',
				'??' => 'n',
				'??' => 'O',
				'??' => 'o',
				'??' => 'O',
				'??' => 'o',
				'??' => 'O',
				'??' => 'o',
				'??' => 'OE',
				'??' => 'oe',
				'??' => 'R',
				'??' => 'r',
				'??' => 'R',
				'??' => 'r',
				'??' => 'R',
				'??' => 'r',
				'??' => 'S',
				'??' => 's',
				'??' => 'S',
				'??' => 's',
				'??' => 'S',
				'??' => 's',
				'??' => 'S',
				'??' => 's',
				'??' => 'T',
				'??' => 't',
				'??' => 'T',
				'??' => 't',
				'??' => 'T',
				'??' => 't',
				'??' => 'U',
				'??' => 'u',
				'??' => 'U',
				'??' => 'u',
				'??' => 'U',
				'??' => 'u',
				'??' => 'U',
				'??' => 'u',
				'??' => 'U',
				'??' => 'u',
				'??' => 'U',
				'??' => 'u',
				'??' => 'W',
				'??' => 'w',
				'??' => 'Y',
				'??' => 'y',
				'??' => 'Y',
				'??' => 'Z',
				'??' => 'z',
				'??' => 'Z',
				'??' => 'z',
				'??' => 'Z',
				'??' => 'z',
				'??' => 's',
				// Decompositions for Latin Extended-B
				'??' => 'S',
				'??' => 's',
				'??' => 'T',
				'??' => 't',
				// Euro Sign
				'???' => 'E',
				// GBP (Pound) Sign
				'??' => '',
				// Vowels with diacritic (Vietnamese)
				// unmarked
				'??' => 'O',
				'??' => 'o',
				'??' => 'U',
				'??' => 'u',
				// grave accent
				'???' => 'A',
				'???' => 'a',
				'???' => 'A',
				'???' => 'a',
				'???' => 'E',
				'???' => 'e',
				'???' => 'O',
				'???' => 'o',
				'???' => 'O',
				'???' => 'o',
				'???' => 'U',
				'???' => 'u',
				'???' => 'Y',
				'???' => 'y',
				// hook
				'???' => 'A',
				'???' => 'a',
				'???' => 'A',
				'???' => 'a',
				'???' => 'A',
				'???' => 'a',
				'???' => 'E',
				'???' => 'e',
				'???' => 'E',
				'???' => 'e',
				'???' => 'I',
				'???' => 'i',
				'???' => 'O',
				'???' => 'o',
				'???' => 'O',
				'???' => 'o',
				'???' => 'O',
				'???' => 'o',
				'???' => 'U',
				'???' => 'u',
				'???' => 'U',
				'???' => 'u',
				'???' => 'Y',
				'???' => 'y',
				// tilde
				'???' => 'A',
				'???' => 'a',
				'???' => 'A',
				'???' => 'a',
				'???' => 'E',
				'???' => 'e',
				'???' => 'E',
				'???' => 'e',
				'???' => 'O',
				'???' => 'o',
				'???' => 'O',
				'???' => 'o',
				'???' => 'U',
				'???' => 'u',
				'???' => 'Y',
				'???' => 'y',
				// acute accent
				'???' => 'A',
				'???' => 'a',
				'???' => 'A',
				'???' => 'a',
				'???' => 'E',
				'???' => 'e',
				'???' => 'O',
				'???' => 'o',
				'???' => 'O',
				'???' => 'o',
				'???' => 'U',
				'???' => 'u',
				// dot below
				'???' => 'A',
				'???' => 'a',
				'???' => 'A',
				'???' => 'a',
				'???' => 'A',
				'???' => 'a',
				'???' => 'E',
				'???' => 'e',
				'???' => 'E',
				'???' => 'e',
				'???' => 'I',
				'???' => 'i',
				'???' => 'O',
				'???' => 'o',
				'???' => 'O',
				'???' => 'o',
				'???' => 'O',
				'???' => 'o',
				'???' => 'U',
				'???' => 'u',
				'???' => 'U',
				'???' => 'u',
				'???' => 'Y',
				'???' => 'y',
				// Vowels with diacritic (Chinese, Hanyu Pinyin)
				'??' => 'a',
				// macron
				'??' => 'U',
				'??' => 'u',
				// acute accent
				'??' => 'U',
				'??' => 'u',
				// caron
				'??' => 'A',
				'??' => 'a',
				'??' => 'I',
				'??' => 'i',
				'??' => 'O',
				'??' => 'o',
				'??' => 'U',
				'??' => 'u',
				'??' => 'U',
				'??' => 'u',
				// grave accent
				'??' => 'U',
				'??' => 'u',
			);
	 
			// Used for locale-specific rules
			$locale = get_locale();
	 
			if ( 'de_DE' == $locale || 'de_DE_formal' == $locale || 'de_CH' == $locale || 'de_CH_informal' == $locale ) {
				$chars['??'] = 'Ae';
				$chars['??'] = 'ae';
				$chars['??'] = 'Oe';
				$chars['??'] = 'oe';
				$chars['??'] = 'Ue';
				$chars['??'] = 'ue';
				$chars['??'] = 'ss';
			} elseif ( 'da_DK' === $locale ) {
				$chars['??'] = 'Ae';
				$chars['??'] = 'ae';
				$chars['??'] = 'Oe';
				$chars['??'] = 'oe';
				$chars['??'] = 'Aa';
				$chars['??'] = 'aa';
			} elseif ( 'ca' === $locale ) {
				$chars['l??l'] = 'll';
			} elseif ( 'sr_RS' === $locale || 'bs_BA' === $locale ) {
				$chars['??'] = 'DJ';
				$chars['??'] = 'dj';
			}
	 
			$string = strtr( $string, $chars );
		} else {
			$chars = array();
			// Assume ISO-8859-1 if not UTF-8
			$chars['in'] = "\x80\x83\x8a\x8e\x9a\x9e"
				. "\x9f\xa2\xa5\xb5\xc0\xc1\xc2"
				. "\xc3\xc4\xc5\xc7\xc8\xc9\xca"
				. "\xcb\xcc\xcd\xce\xcf\xd1\xd2"
				. "\xd3\xd4\xd5\xd6\xd8\xd9\xda"
				. "\xdb\xdc\xdd\xe0\xe1\xe2\xe3"
				. "\xe4\xe5\xe7\xe8\xe9\xea\xeb"
				. "\xec\xed\xee\xef\xf1\xf2\xf3"
				. "\xf4\xf5\xf6\xf8\xf9\xfa\xfb"
				. "\xfc\xfd\xff";
	 
			$chars['out'] = 'EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy';
	 
			$string              = strtr( $string, $chars['in'], $chars['out'] );
			$double_chars        = array();
			$double_chars['in']  = array( "\x8c", "\x9c", "\xc6", "\xd0", "\xde", "\xdf", "\xe6", "\xf0", "\xfe" );
			$double_chars['out'] = array( 'OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th' );
			$string              = str_replace( $double_chars['in'], $double_chars['out'], $string );
		}
	 
		return $string;
	}

	/**
	 * Checks for invalid UTF8 in a string.
	 *
	 * @since 2.8.0
	 *
	 * @staticvar bool $is_utf8
	 * @staticvar bool $utf8_pcre
	 *
	 * @param string  $string The text which is to be checked.
	 * @param bool    $strip Optional. Whether to attempt to strip out invalid UTF8. Default is false.
	 * @return string The checked text.
	 */
	private static function wp_check_invalid_utf8( $string, $strip = false ) {
		$string = (string) $string;

		if ( 0 === strlen( $string ) ) {
			return '';
		}

		// Store the site charset as a static to avoid multiple calls to get_option()
		static $is_utf8 = null;
		if ( ! isset( $is_utf8 ) ) {
			//$is_utf8 = in_array( get_option( 'blog_charset' ), array( 'utf8', 'utf-8', 'UTF8', 'UTF-8' ) );
		}
		if ( ! $is_utf8 ) {
			return $string;
		}

		// Check for support for utf8 in the installed PCRE library once and store the result in a static
		static $utf8_pcre = null;
		if ( ! isset( $utf8_pcre ) ) {
			$utf8_pcre = @preg_match( '/^./u', 'a' );
		}
		// We can't demand utf8 in the PCRE installation, so just return the string in those cases
		if ( ! $utf8_pcre ) {
			return $string;
		}

		// preg_match fails when it encounters invalid UTF8 in $string
		if ( 1 === @preg_match( '/^./us', $string ) ) {
			return $string;
		}

		return '';
	}

	/**
	 * Properly strip all HTML tags including script and style
	 *
	 * This differs from strip_tags() because it removes the contents of
	 * the `<script>` and `<style>` tags. E.g. `strip_tags( '<script>something</script>' )`
	 * will return 'something'. wp_strip_all_tags will return ''
	 *
	 * @since 2.9.0
	 *
	 * @param string $string        String containing HTML tags
	 * @param bool   $remove_breaks Optional. Whether to remove left over line breaks and white space chars
	 * @return string The processed string.
	 */
	private static function wp_strip_all_tags( $string, $remove_breaks = false ) {
		$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
		$string = strip_tags( $string );

		if ( $remove_breaks ) {
			$string = preg_replace( '/[\r\n\t ]+/', ' ', $string );
		}

		return trim( $string );
	}

	/**
	 * Convert lone less than signs.
	 *
	 * KSES already converts lone greater than signs.
	 *
	 * @since 2.3.0
	 *
	 * @param string $text Text to be converted.
	 * @return string Converted text.
	 */
	private static function wp_pre_kses_less_than( $text ) {
		return preg_replace_callback( '%<[^>]*?((?=<)|>|$)%', array($this,'wp_pre_kses_less_than_callback'), $text );
	}

	/**
	 * Callback function used by preg_replace.
	 *
	 * @since 2.3.0
	 *
	 * @param array $matches Populated by matches to preg_replace.
	 * @return string The text returned after esc_html if needed.
	 */
	private static function wp_pre_kses_less_than_callback( $matches ) {
		if ( false === strpos( $matches[0], '>' ) ) {
			return esc_html( $matches[0] );
		}
		return $matches[0];
	}
}