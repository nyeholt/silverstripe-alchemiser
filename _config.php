<?php

/**
 * A simple helper function to deal with DB quoting.
 */
if (!defined('SSAU_QUOTE_CHAR')) {

	define('SSAU_QUOTE_CHAR', defined('DB::USE_ANSI_SQL') ? '"' : '');

	/**
	 * Quote up a filter of the form
	 *
	 * array ("ParentID =" => 1)
	 *
	 *
	 *
	 * @param unknown_type $filter
	 * @return unknown_type
	 */
	function db_quote($filter = array(), $join = " AND ")
	{
		$string = '';
		$sep = '';

		foreach ($filter as $field => $value) {
			// first break the field up into its two components
			list($field, $operator) = explode(' ', trim($field));
			if (is_array($value)) {
				// quote each individual one into a string
				$ins = '';
				$insep = '';
				foreach ($value as $v) {
					$ins .= $insep . Convert::raw2sql($v);
					$insep = ',';
				}
				$value = '('.$ins.')';
			} else {
				$value = "'" . Convert::raw2sql($value) . "'";
			}

			if (strpos($field, '.')) {
				list($tb, $fl) = explode('.', $field);
				$string .= $sep . SSAU_QUOTE_CHAR . $tb . SSAU_QUOTE_CHAR . '.' . SSAU_QUOTE_CHAR . $fl . SSAU_QUOTE_CHAR . " $operator " . $value;
			} else {
				$string .= $sep . SSAU_QUOTE_CHAR . $field . SSAU_QUOTE_CHAR . " $operator " . $value;
			}
			$sep = $join;
		}

		return $string;
	}

}

if (!function_exists('ssau_log')) {
	function ssau_log($message) {
		$message = array(
			'errno' => '',
			'errstr' => $message,
			'errfile' => dirname(__FILE__),
			'errline' => '',
			'errcontext' => ''
		);

		SS_Log::log($message, SS_Log::NOTICE);
	}
}

?>