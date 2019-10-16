<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PersonalCloudStorage\Debug;
use PersonalCloudStorage\ThreadPool;

$s = function(int $step, array $args, $vars, $data)
{
	//if (!defined(ThreadPool)) $i=0;  //想测试线程触发warning的情况就加上这行
	if ($vars === null)
		require_once __DIR__ . '/../vendor/autoload.php';
	//step 1需要返回值，其他的都不需要
	if ($step==ThreadPool::STEP_LOOP)
	{
		usleep(30);
		return '>>>'.$data.'<<<';
	}
	return null;
};

$pool = new ThreadPool($s, array(1, '12', 'abc'), 5);

for($i=1;$i<=1000;$i++)
{
	$pool->addTask($i);
}

$pool->addStop();

$counter=0;
Debug::p('----0----');
while(!$pool->isAllDone())
{
	$a = $pool->getReply();
	if ($a)
	{
		$counter++;
		Debug::pr2('Main', json_encode($a));
	}
	usleep(20);
}

Debug::p('----1----');
Debug::pr2('Main', $pool->getResults());
Debug::p('----2----');
Debug::p($counter);
Debug::p('----END----');



