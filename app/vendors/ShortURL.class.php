<?php 
if (!defined('SHORT_URL_KEY')) {
	define('SHORT_URL_KEY', 'FWOPJEFJeojfefe@#(*&$@$123086');
}
/*
 * ShortURL (https://github.com/delight-im/ShortURL)
 * Copyright (c) delight.im (https://www.delight.im/)
 * Licensed under the MIT License (https://opensource.org/licenses/MIT)
 */

/**
 * ShortURL: Bijective conversion between natural numbers (IDs) and short strings
 *
 * ShortURL::encode() takes an ID and turns it into a short string
 * ShortURL::decode() takes a short string and turns it into an ID
 *
 * Features:
 * + large alphabet (51 chars) and thus very short resulting strings
 * + proof against offensive words (removed 'a', 'e', 'i', 'o' and 'u')
 * + unambiguous (removed 'I', 'l', '1', 'O' and '0')
 *
 * Example output:
 * 123456789 <=> pgK8p
 */
class ShortURL {

	const ALPHABET = '23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ-_';
	const BASE = 51; // strlen(self::ALPHABET)

	public static function encode($num) {
		$str = '';

		while ($num > 0) {
			$str = self::ALPHABET[($num % self::BASE)] . $str;
			$num = (int) ($num / self::BASE);
		}

		return $str;
	}

	public static function decode($str) {
		$num = 0;
		$len = strlen($str);

		for ($i = 0; $i < $len; $i++) {
			$num = $num * self::BASE + strpos(self::ALPHABET, $str[$i]);
		}

		return $num;
	}

	public static function encrypt($data, $key)  {
		$char="";
		$str="";
		$key =   md5($key);
		$x =   0;
		$len =   strlen($data);
		$l =   strlen($key);
		for ($i = 0; $i < $len; $i++) {
			if ($x == $l) { $x = 0; }
			$char .= $key{$x};
			$x++;
		}
		for ($i = 0; $i < $len; $i++){
			$str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
		}
		return base64_encode($str);
	}
	
	public static function decrypt($data, $key) {
		$key = md5($key);
		$x = 0;
		$data = base64_decode($data);
		$len = strlen($data);
		$l = strlen($key);
		for ($i = 0; $i < $len; $i++) {
			if ($x == $l){ $x = 0;}
			$char .= substr($key, $x, 1);
			$x++;
		}
		for ($i = 0; $i < $len; $i++){
			if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))){
				$str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
			}else{
				$str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
			}
		}
		return $str;
	}
}
?>