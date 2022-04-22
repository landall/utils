<?php

namespace PersonalCloudStorage;


class Debug
{
	static $debug = true;
	public static function p($value)
	{
		if (!Debug::$debug) return;
		echo $value;
		echo PHP_EOL;
	}

	public static function p2($value)
	{
		if (!Debug::$debug) return;
		echo json_encode($value, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
		echo PHP_EOL;
	}

	public static function pr($value)
	{
		if (!Debug::$debug) return;
		print_r($value);
		echo PHP_EOL;
	}

	public static function gp($arr)
	{
		$result = array();
		foreach($arr as $key=>$value)
		{
			$result[$key] = $value->getProperties();
		}
		return $result;
	}

	public static function prgp($value)
	{
		if (!Debug::$debug) return;
		print_r(Debug::gp($value));
		echo PHP_EOL;
	}

	public static function pr2($prefix, $value)
	{
		if (!Debug::$debug) return;
		echo '['.$prefix.'] '.print_r($value, true);
		echo PHP_EOL;
	}

	public static function convertToUTF8($str)
	{
		$encode = mb_detect_encoding($str, array('UTF-8', "GB2312", "GBK", 'BIG5', 'JIS', 'EUC-JP', 'SJIS'));
		return mb_convert_encoding($str, 'UTF-8', $encode);
	}
}
