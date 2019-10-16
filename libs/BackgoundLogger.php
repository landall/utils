<?php

//来自php parallel的github issue，作者自己写的

namespace PersonalCloudStorage;

use \parallel\Runtime;
use \parallel\Channel;

class BackgroundLogger
{
	public function __construct(string $file) {
		$this->runtime = new Runtime();
		$this->channel = 
			Channel::make($file, Channel::Infinite);
			
		$this->runtime->run(function($file){
			$channel = Channel::open($file);
			$handle  = fopen($file, "rb");
			
			if (!is_resource($handle)) {
				throw new \RuntimeException(
					"could not open {$file}");
			}
			
			while (($input = $channel->recv())) {
				fwrite($handle, $input.PHP_EOL);
			}
			
			fclose($handle);
		}, [$file]);
	}
	
	public function log($message, ... $args) {
		$this->channel->send(
			vsprintf($message, $args));
	}
	
	public function __destruct() {
		$this->channel->send(false);
	}
	
	private $channel;
	private $runtime;
}

//$logger = new BackgroundLogger("php://stdout");
//$logger->log("hello world");
//$logger->log("I am %s", "here");
