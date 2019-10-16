<?php

namespace PersonalCloudStorage;

use \Exception;
use \PersonalCloudStorage\Debug;

/**
 * @class PCSException
 *
 * Exception class of PersonalCloudStorage
 * 
 * throw PCSException('Error Message', <0 or http status code>);
 *
 */
class PCSException extends Exception
{
	const DEFAULT = 0;
	const HTTP = 1;
	const FILE = 2;
	const BIZLOGIC = 3;

	public $category;
	public $hint;

	public function __construct (string $message = "", int $code = 0, int $category = PCSException::DEFAULT, string $hint = "")
	{
		$this->hint = $hint;
		$this->category = $category;
		if (!empty($this->hint))
		{
			Debug::p('*************************');
			Debug::p($message);
			Debug::p('*************************');
			Debug::p($code);
			Debug::p('*************************');
			Debug::p($this->hint);
			Debug::p('*************************');
		}
		parent::__construct($message, $code);
	}
}

