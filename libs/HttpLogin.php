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

