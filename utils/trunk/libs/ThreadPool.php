<?php

namespace PersonalCloudStorage;

use \parallel\{Runtime, Future, Channel, Events};

use \PersonalCloudStorage\ThreadVars;

class ThreadPool
{
	public $workers;
	public $channel_go;
	public $events;

	const STEP_INIT = 0;
	const STEP_LOOP = 1;
	const STEP_STOP = 10;

	public function __construct($func, array $args, int $threadCount = 5)
	{
		$s = function($func, $init)
		{
			require_once __DIR__ . '/../autoload.php';
			$ch_cmd = $init['command'];
			$ch_reply = $init['reply'];
			$args = $init['args'];
			$id = 'Thread_'.$init['id'];
			$counter = 0;
			$vars = new ThreadVars();

			$reply = call_user_func($func, ThreadPool::STEP_INIT, $args, $vars, null);

			while(true)
			{
				$data=$ch_cmd->recv();
				if($data['verb']=='stop') break;
				$data=$data['data'];
				Debug::pr2($id, $data);
				$reply = call_user_func($func, ThreadPool::STEP_LOOP, $args, $vars, $data);
				$data = array('id'=>$id,'verb'=>'data','data'=>array($data,$reply));
				$ch_reply->send($data);
				$counter++;
			}
			$reply = call_user_func($func, ThreadPool::STEP_STOP, $args, $vars, null);
			$vars->setCounter($counter);

			$ch_reply->send(array('id'=>$id,'verb'=>'stop'));
			return $vars;
		};

		$this->channel_go = new Channel(Channel::Infinite);
		$this->events = new Events();

		$this->workers = array();
		for($i=1;$i<=$threadCount;$i++)
		{
			$a = new Runtime();
			$channel_reply = new Channel(100);
			$future = $a->run($s, array($func, array('command'=>$this->channel_go,'reply'=>$channel_reply, 'args'=>$args, 'id'=>$i)));
			$this->workers[$i] = array('thread'=>$a,'future'=>$future,'reply'=>$channel_reply);
			$this->events->addChannel($channel_reply);
		}

		$this->events->setBlocking(true);
	}

	public function addTask($data)
	{
		$this->channel_go->send(array('verb'=>'data','data'=>$data));
	}

	public function addStop()
	{
		foreach($this->workers as $id=>$w)
			$this->channel_go->send(array('verb'=>'stop'));
	}

	public function getReply()
	{
		$event = $this->events->poll();
		if($event)
		{
			$channel = $event->object;
			$data = $event->value;
			//Debug::pr2('getReply',json_encode($data));
			if ($data['verb']=='data')
			{
				$this->events->addChannel($channel);
				unset($data['verb']);
			}
			else //Stop Verb
			{
				$channel->close();
				$data = null;
			}
			return $data;
		}
		else
			return null;
	}

	public function isAllDone() : bool
	{
		if(count($this->events)>0) return false;

		foreach($this->workers as $w)
			if (!$w['future']->done()) return false;
		return true;
	}

	public function getResults() : array
	{
		//TODO cancelled, killed, running
		$result = array();
		foreach($this->workers as $id=>$w)
			if ($w['future']->done())
			{
				$result[$id] = $w['future']->value()->getCounter();
			}
			else
			{
				$result[$id] = null;
			}
		return $result;
	}

	public function getResultVars(string $name) : array
	{
		//TODO cancelled, killed, running
		$result = array();
		foreach($this->workers as $id=>$w)
			if ($w['future']->done())
			{
				$result[$id] = $w['future']->value()->$name;
			}
			else
			{
				$result[$id] = null;
			}
		return $result;
	}

}
