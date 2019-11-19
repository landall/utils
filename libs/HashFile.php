<?php

namespace PersonalCloudStorage;

use \PersonalCloudStorage\PCSException;

/*
  这个文件格式参考的是由Hashdeep生成的输出文件
  前两行是%%%%开头，第一行表示生成工具，第二行是列的定义
  #开头的行不理
  其他行是逗号分隔，除了最后一列外，列的值没有含逗号的情况，最后一列含逗号没关系。所有字段均可以含有空格，不需要加引号。

  每行最长8K

%%%% HASHDEEP-1.0
%%%% size,md5,sha256,filename
## Invoked from: H:\md5deep-4.4
## H:\md5deep-4.4> hashdeep64.exe -r -l -W blueccA.hash z:\
## 
5952280,56dc18800d0d7f2206452fa4d196dc86,e3fd6b3558f7667ce317af7dd8c42f4c59f680154d3b51d3316764ce06204fe0,z:\CDDisk-Dump\2004-1\Cost\QQ2003II Muzi Edition.exe
*/

class HashFile
{
	public static function load(string $fn, $cols=array('filename','md5','size'), $strict=true, $max_lines = 2147483647)
	{
		$original_setting = ini_get("auto_detect_line_endings");
		ini_set("auto_detect_line_endings", true);
		$f = fopen($fn,'rb');

		$s = fgets($f, 8192);
		if (strpos($s, '%%%%') !== 0)
			throw new PCSException('line 1 do not start with %%%%',1,PCSException::BIZLOGIC);
		Debug::pr(trim(substr($s, 4)));

		$s = fgets($f, 8192);
		if (strpos($s, '%%%%') !== 0)
			throw new PCSException('line 2 do not start with %%%%',2,PCSException::BIZLOGIC);
		Debug::pr($k = trim(substr($s, 4)));
		$cs = explode(',', $k);
		$cc = count($cs);
		$ins = array();
		foreach ($cols as $col)
		{
			$in = array_search($col, $cs);
			if($in === false)
				if($strict)
					throw new PCSException('colum '.$col.' not found',3,PCSException::BIZLOGIC);
			$ins[$col]=$in;
		}

		$i = 2;
		$result = array();

		while(!feof($f))
		{
			$s = trim(fgets($f, 8192));
			$i++;
			if (strpos($s, '#') === 0) continue;
			if (empty($s)) continue;
			$m = explode(',', $s);
			if($cc > count($m))
				throw new PCSException('column count of line '.$i.' is wrong',4,PCSException::BIZLOGIC);
			if($cc < count($m))
			{
				//Debug::pr('----Merge----');
				//Debug::p($cc);
				//Debug::pr($i);
				//Debug::pr($s);
				//Debug::pr($m);
				$b = array_slice($m, 0, $cc-1);
				$b[] = implode(',', array_slice($m, $cc-1));
				$m = $b;
				//Debug::pr($m);
				//sleep(2);
			}
			foreach($ins as $col=>$in)
			{
				if(sizeof($m) <= $in) {
					//出现了就说明其他地方有bug
					Debug::pr($in);
					Debug::pr($m);
				}
				$a[$col] = ($in === false)? '' : $m[$in];
			}
			$result[] = $a;
			if($i>=$max_lines) break;
		}
		ini_set("auto_detect_line_endings", $original_setting);
		return $result;
	}

	public static function loadDataFile(string $fn)
	{

	}
}
