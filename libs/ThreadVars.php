<?php

namespace PersonalCloudStorage;

class ThreadVars
{
	protected $vars;
	protected $counter = 0;

	public function __construct()
	{
		$this->vars = array();
	}

	public function __set (string $name , $value) : void
	{
		$this->vars[$name] = $value;
	}

	public function __get (string $name)
	{
		if (array_key_exists($name, $this->vars))
			return $this->vars[$name];
		else
		{
			$trace = debug_backtrace();
			trigger_error(
				'Undefined property via __get(): ' . $name .
				' in ' . $trace[0]['file'] .
				' on line ' . $trace[0]['line'],
				E_USER_NOTICE);
			return null;
		}
	}

	public function __isset ( string $name ) : bool
	{
		return isset($this->vars[$name]);
	}

	public function __unset ( string $name ) : void
	{
		unset($this->vars[$name]);
	}

	public function toArray() : array
	{
		return $this->vars;
	}

	public function setCounter(int $c)
	{
		$this->counter = $c;
	}

	public function getCounter() : int
	{
		return $this->counter;
	}
}
