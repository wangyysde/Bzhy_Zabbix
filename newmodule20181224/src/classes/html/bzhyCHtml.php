<?php
/* 
 *  Author: Wayne Wang
 *  Website: http://www.bzhy.com
 *  Email: net_use@bzhy.com
 *  Copyright 2010 - 2018 http://www.bzhy.com
 */


/**
 * A helper class for working with HTML.
 */
class bzhyCHtml {

	/**
	 * Encodes the value to be used in HTML code. If the given value is an array, the values will be
	 * encoded recursively.
	 *
	 * @static
	 *
	 * @param mixed $data
	 *
	 * @return mixed
	 */
	public static function encode($data) {
		if (is_array($data)) {
			$rs = [];
			foreach ($data as $key => $value) {
				$rs[$key] = self::encode($value);
			}

			return $rs;
		}

		return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
	}

	/**
	 * Encodes the data as a JSON string with HTML entities escaped.
	 *
	 * @static
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	public static function serialize(array $data) {
		return self::encode(CJs::encodeJson($data));
	}
}
