<?php

namespace PersonalCloudStorage;

//if (array_key_exists('action',$_GET) && ($_GET['action']=='logout'))

class HttpLogin
{
	public static function Login($userlist, string $hints, $force_login = false, $user_key = 'user')
	{
		/// 开始检查登录
		isset($_SESSION) or session_start();

		/// 用户输入了用户名、密码响应HTTP 401
		if (isset($_SERVER['PHP_AUTH_USER']))
		{
			$uu = $_SERVER['PHP_AUTH_USER'];
			$pp = $_SERVER['PHP_AUTH_PW'];
			if(array_key_exists($uu,$userlist) && ($userlist[$uu]['pass']===$pp))
			{
				$_SESSION[$user_key] = $uu;
				return $_SESSION[$user_key];
			}
		}

		if (empty($_SESSION[$user_key]) || $force_login)
		{
			header('WWW-Authenticate: Basic realm="'.$hints.'"');
			header('HTTP/1.0 401 Unauthorized');
			exit();
		}

		return $_SESSION[$user_key];
	}

	public static function Logout(string $url = './', $user_key = 'user')
	{
		//unset($_SESSION[$user_key]);
		session_destroy();
		echo "<html><body><script>window.location.href = '${url}';</script></body></html>";
		exit();
	}
}

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
