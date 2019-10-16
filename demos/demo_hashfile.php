<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PersonalCloudStorage\Debug;
use PersonalCloudStorage\HashFile;

Debug::p('----Strict And Success----');
Debug::pr(HashFile::load(__DIR__ . '/blueccA.hash', array('filename','md5','size'), true));
Debug::p('----Not Strict And Success----');
Debug::pr(HashFile::load(__DIR__ . '/blueccA.hash', array('filename','md5','crc32'), false));
Debug::p('----Not Strict And Failed----');
Debug::pr(HashFile::load(__DIR__ . '/blueccA.hash', array('filename','md5','crc32'), true));
Debug::p('----END----');



